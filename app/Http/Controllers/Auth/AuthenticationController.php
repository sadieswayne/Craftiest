<?php

namespace App\Http\Controllers\Auth;

use App\Actions\EmailConfirmation;
use App\Events\UsersActivityEvent;
use App\Helpers\Classes\MarketplaceHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Setting;
use App\Models\Team\TeamMember;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use JsonException;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Newsletter\Facades\Newsletter;

class AuthenticationController extends Controller
{
    public function githubCallback(Request $request): RedirectResponse
    {
        $githubUser = Socialite::driver('github')->user();
        $checkUser = User::where('email', $githubUser->getEmail())->exists();
        if ($checkUser) {
            $user = User::where('email', $githubUser->getEmail())->first();
            $user->github_token = $githubUser->token;
            $user->github_refresh_token = $githubUser->refreshToken;
            $userSocialAvatar = $githubUser->getAvatar() ?? ($user->avatar ?? 'assets/img/auth/default-avatar.png');
            $user->avatar = $user->avatar === 'assets/img/auth/default-avatar.png' ? $userSocialAvatar : $user->avatar;
            $user->affiliate_code = $user->affiliate_code ?? Str::upper(Str::random(12));
            $user->save();
        } else {
            $user = User::updateOrCreate([
                'github_id' => $githubUser->id,
            ], [
                'name'                 => $githubUser->getName() ?? $githubUser->getNickname(),
                'surname'              => '',
                'email'                => $githubUser->getEmail(),
                'github_token'         => $githubUser->token,
                'github_refresh_token' => $githubUser->refreshToken,
                'avatar'               => $githubUser->getAvatar(),
                'password'             => Hash::make(Str::random(12)),
                'affiliate_code'       => Str::upper(Str::random(12)),
                'email_verified_at'    => now(),
                'email_confirmed'      => true,
            ]);
            $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));
        }
        Auth::login($user);
        $ip = $request->ip();
        $connection = $request->header('User-Agent');
        event(new UsersActivityEvent($user->email, $user->type, $ip, $connection));

        return redirect('/dashboard/user');
    }

    public function googleCallback(Request $request): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $checkUser = User::where('email', $googleUser->getEmail())->exists();
        $nameParts = explode(' ', $googleUser->getName());
        $name = $nameParts[0] ?? '';
        $surname = $nameParts[1] ?? '';
        if ($checkUser) {
            $user = User::where('email', $googleUser->getEmail())->first();
            $user->google_token = $googleUser->token;
            $user->google_refresh_token = $googleUser->refreshToken;
            $userSocialAvatar = $googleUser->getAvatar() ?? ($user->avatar ?? 'assets/img/auth/default-avatar.png');
            $user->avatar = $user->avatar === 'assets/img/auth/default-avatar.png' ? $userSocialAvatar : $user->avatar;
            $user->affiliate_code = $user->affiliate_code ?? Str::upper(Str::random(12));
            $user->save();
        } else {
            $user = User::updateOrCreate([
                'google_id' => $googleUser->id,
            ], [
                'name'                 => $name,
                'surname'              => $surname,
                'email'                => $googleUser->getEmail(),
                'google_token'         => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar'               => $googleUser->getAvatar(),
                'password'             => Hash::make(Str::random(12)),
                'affiliate_code'       => Str::upper(Str::random(12)),
                'email_verified_at'    => now(),
                'email_confirmed'      => true,
            ]);
            $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));
        }
        Auth::login($user);
        $ip = $request->ip();
        $connection = $request->header('User-Agent');
        event(new UsersActivityEvent($user->email, $user->type, $ip, $connection));

        return redirect('/dashboard/user');
    }

    public function facebookCallback(Request $request): RedirectResponse
    {
        $facebookUser = Socialite::driver('facebook')->user();
        if ($facebookUser->getEmail()) {
            $checkUser = User::where('email', $facebookUser->getEmail())->exists();
            $nameParts = explode(' ', $facebookUser->getName());
            $name = $nameParts[0] ?? '';
            $surname = $nameParts[1] ?? '';
            if ($checkUser) {
                $user = User::where('email', $facebookUser->getEmail())->first();
                $user->facebook_token = $facebookUser->token;
                $userSocialAvatar = $facebookUser->getAvatar() ?? ($user->avatar ?? 'assets/img/auth/default-avatar.png');
                $user->avatar = $user->avatar === 'assets/img/auth/default-avatar.png' ? $userSocialAvatar : $user->avatar;
                $user->affiliate_code = $user->affiliate_code ?? Str::upper(Str::random(12));
                $user->save();
            } else {
                $user = User::updateOrCreate([
                    'facebook_id' => $facebookUser->id,
                ], [
                    'name'              => $name,
                    'surname'           => $surname,
                    'email'             => $facebookUser->getEmail(),
                    'facebook_token'    => $facebookUser->token,
                    'avatar'            => $facebookUser->getAvatar(),
                    'password'          => Hash::make(Str::random(12)),
                    'affiliate_code'    => Str::upper(Str::random(12)),
                    'email_verified_at' => now(),
                    'email_confirmed'   => true,
                ]);
                $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));
            }
            Auth::login($user);
            $ip = $request->ip();
            $connection = $request->header('User-Agent');
            event(new UsersActivityEvent($user->email, $user->type, $ip, $connection));
        }

        return redirect('/dashboard/user');

    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();
        $ip = $request->ip();
        $connection = $request->header('User-Agent');
        event(new UsersActivityEvent($user?->email, $user?->type, $ip, $connection));

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function registerCreate(Request $request): View
    {
        return view('panel.authentication.register', [
            'plan' => $request->get('plan'),
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function registerStore(Request $request): JsonResponse
    {
        $settings = Setting::getCache();

        if ($settings->recaptcha_register && ($settings->recaptcha_sitekey || $settings->recaptcha_secretkey)) {
            $client = new Client;
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => config('services.recaptcha.secret'),
                    'response' => $request->input('g-recaptcha-response'),
                ],
            ])->getBody()->getContents();

            if (! json_decode($response, true, 512, JSON_THROW_ON_ERROR)['success']) {
                return response()->json([
                    'errors' => ['Invalid Recaptcha'],
                    'type'   => 'recaptcha',
                ], 401);
            }
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^(?!.*\b(?:[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})\b).*$/'],
            'surname'  => ['required', 'string', 'max:255', 'regex:/^(?!.*\b(?:[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})\b).*$/'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults(), 'max:20'],
        ], [
            'name.regex'    => 'The name field must not contain a URL or domain.',
            'surname.regex' => 'The surname field must not contain a URL or domain.',
        ]);

        $teamMember = TeamMember::query()
            ->with('team')
            ->where('email', $request->email)
            ->where('status', 'waiting')
            ->first();

        $affCode = null;
        if ($request->affiliate_code !== null) {
            $affUser = User::where('affiliate_code', $request->affiliate_code)->first();
            $affCode = $affUser?->id;
        }

        $user = User::create([
            'team_id'                 => $teamMember?->team_id,
            'team_manager_id'         => $teamMember?->team?->user_id,
            'name'                    => $request->name,
            'surname'                 => $request->surname,
            'email'                   => $request->email,
            'email_confirmation_code' => Str::random(67),
            'password'                => Hash::make($request->password),
            'email_verification_code' => Str::random(67),
            'affiliate_id'            => $affCode,
            'affiliate_code'          => Str::upper(Str::random(12)),
        ]);

        $user->updateCredits(setting('freeCreditsUponRegistration', User::getFreshCredits()));

        if ($teamMember) {
            $teamMember->update([
                'user_id'   => $user->id,
                'status'    => 'active',
                'joined_at' => now(),
            ]);
        }

        // event(new Registered($user));
        EmailConfirmation::forUser($user)->send();

        if ($settings->login_without_confirmation === 1) {
            Auth::login($user);

            $ip = $request->ip();
            $connection = $request->header('User-Agent');

            event(new UsersActivityEvent($user->email, $user->type, $ip, $connection));
        } else {
            $data = [
                'errors' => ['We have sent you an email for account confirmation. Please confirm your account to continue.'],
                'type'   => 'confirmation',
            ];

            return response()->json($data, 401);
        }

        if (class_exists('App\Classes\PapAffiliate')) {
            try {
                (new \App\Classes\PapAffiliate)->addAffiliate([
                    'email'        => $user->email,
                    'firstname'    => $request->name,
                    'lastname'     => $request->surname,
                    'password'     => $user->password,
                    'companyname'  => 'companyname',
                    'address1'     => 'Address 1',
                    'city'         => 'City',
                    'state'        => 'State',
                    'country'      => 'Country',
                    'userid'       => $user->id,
                    'refid'        => $user->affiliate_code,
                    'parentuserid' => $request->affiliate_code,
                ]);
            } catch (Exception $e) {
            }
        }

        if (MarketplaceHelper::isRegistered('mailchimp-newsletter') && setting('mailchimp_register') === 1) {
            Newsletter::subscribeOrUpdate(
                $request->email,
                ['FNAME' => $request->name, 'LNAME' => $request->surname],
            );
        }

        if (MarketplaceHelper::isRegistered('hubspot') && setting('hubspot_crm_contact_register') === 1) {
            (new \App\Extensions\Hubspot\System\Services\HubspotService)->createCrmContacts($request->email, $request->name, $request->surname);
        }

        return response()->json(['status' => 'OK']);
    }

    public function PasswordResetCreate(): View
    {
        return view('panel.authentication.password_reset');
    }
}
