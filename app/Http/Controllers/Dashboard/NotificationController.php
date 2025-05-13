<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            if ($request->has('id')) {
                $notification = $request->user()->notifications()->where('id', $request->id)->first();
                if ($notification) {
                    $notification->markAsRead();
                }

                return response()->json(['success' => true]);
            } else {
                $request->user()->unreadNotifications->markAsRead();

                return response()->json(['success' => true]);
            }
        }
    }
}
