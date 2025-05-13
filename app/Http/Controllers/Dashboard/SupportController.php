<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\TicketAction;
use App\Http\Controllers\Controller;
use App\Models\UserSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function list()
    {
        $user = auth()->user();

        $items = $user->isAdmin() ? UserSupport::all() : $user->supportRequests;

        return view('panel.support.list', compact('items'));
    }

    public function newTicket()
    {
        return view('panel.support.new');
    }

    public function newTicketSend(Request $request): void
    {
        if (! $user = Auth::user()) {
            return;
        }

        $support = $user->supportRequests()->create([
            'ticket_id' => Str::upper(Str::random(10)),
            'priority'  => $request->priority,
            'category'  => $request->category,
            'subject'   => $request->subject,
        ]);

        TicketAction::ticket($support)
            ->fromUser()
            ->new($request->message)
            ->send();
    }

    public function viewTicket($ticket_id)
    {
        $ticket = UserSupport::where('ticket_id', $ticket_id)->firstOrFail();

        if ($ticket->user_id == Auth::id() or Auth::user()->isAdmin()) {
            return view('panel.support.view', compact('ticket'));
        } else {
            return back()->with(['message' => __('Unauthorized'), 'type' => 'error']);
        }
    }

    public function viewTicketSendMessage(Request $request): void
    {
        if (! $user = Auth::user()) {
            return;
        }

        TicketAction::ticket($request->input('ticket_id'))
            ->fromAdminIfTrue($user->isAdmin())
            ->answer($request->input('message'))
            ->send();
    }
}
