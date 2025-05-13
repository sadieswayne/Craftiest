<?php

namespace App\Http\Controllers\Team;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamInviteRequest;
use App\Jobs\SendTeamInviteEmail;
use App\Models\Team\Team;
use App\Models\Team\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Helper::setting('team_functionality') == 0, 404);

        $team = $this->getTeam($request->user());

        return view('panel.user.team.index', [
            'subscription' => getCurrentActiveSubscription(),
            'filter'       => 'all',
            'team'         => $team,
            'user'         => $team->user,
            'members'      => TeamMember::query()
                ->with('user')
                ->where('team_id', $team->getAttribute('id'))
                ->get(),
        ]);
    }

    public function getTeam(User $user)
    {
        if ($team = $user->myCreatedTeam) {

            if ($team->allow_seats != $user?->relationPlan?->plan_allow_seat) {
                $team->allow_seats = $user?->relationPlan?->plan_allow_seat ?: 0;
                $team->save();
            }

            return $team;
        }

        $allow_seats = $user->isAdmin() ? 100 : $user?->relationPlan?->plan_allow_seat;

        return Team::query()->firstOrCreate([
            'user_id' => auth()->id(),
        ], [
            'name'        => $user?->fullName(),
            'allow_seats' => $allow_seats ?: 0,
        ]);
    }

    public function storeInvitation(TeamInviteRequest $request, Team $team)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        abort_if(Helper::setting('team_functionality') == 0, 404);

        $subscription = getCurrentActiveSubscription();

        if (! $subscription || $request->user()?->relationPlan?->is_team_plan == 0) {
            return back()->with([
                'type'    => 'error', // success, error, warning, info, '
                'message' => trans('Please subscribe to a new plan'),
            ]);
        }

        TeamMember::query()->create($request->validated());

        dispatch(new SendTeamInviteEmail($request->user(), $request->get('email')));

        return back()->with([
            'type'    => 'success', // success, error, warning, info, '
            'message' => trans('Invitation sent successfully.'),
        ]);
    }

    public function teamMember(Team $team, TeamMember $teamMember)
    {
        return view('panel.user.team.edit', [
            'filter'           => 'all',
            'team'             => $team,
            'member'           => $teamMember,
            'user'             => $teamMember->user,
            'remaining_words'  => $teamMember->used_image_credit ?: 0,
            'remaining_images' => $teamMember->remaining_words ?: 0,
        ]);
    }

    public function teamMemberUpdate(Request $request, Team $team, TeamMember $teamMember)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $request['allow_unlimited_credits'] = (bool) $request->get('allow_unlimited_credits', false);

        $user = $team->user;

        $manager_remaining_images = $user->remaining_images;
        $manager_remaining_words = $user->remaining_words;

        $data = $request->validate([
            'role'             => 'required',
            'status'           => 'required',
            'remaining_images' => $request['allow_unlimited_credits']
                ? 'sometimes|nullable|numeric'
                : 'required|numeric|max:' . $manager_remaining_images,
            'remaining_words' => $request['allow_unlimited_credits']
                ? 'sometimes|nullable|numeric'
                : 'required|numeric|max:' . $manager_remaining_words,
            'allow_unlimited_credits' => 'sometimes|nullable|boolean',
        ]);

        $teamMember->update($data);

        return to_route('dashboard.user.team.index')->with([
            'type'    => 'success',
            'message' => __('Team member updated successfully.'),
        ]);
    }

    public function teamMemberDelete(Team $team, TeamMember $teamMember)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        abort_if(auth()->id() !== $team->user_id, 404);

        if ($teamMember->user) {
            $teamMember->user->update(['team_id' => null, 'team_member_id' => null]);
        }

        $teamMember->delete();

        return back()->with([
            'type'    => 'success', // success, error, warning, info,
            'message' => trans('Team member deleted successfully.'),
        ]);
    }
}
