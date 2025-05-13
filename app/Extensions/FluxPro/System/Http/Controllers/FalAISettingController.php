<?php

namespace App\Extensions\FluxPro\System\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FalAISettingController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('flux-pro::setting');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fal_ai_api_secret'    => 'required|string',
            'fal_ai_default_model' => 'required|string',
        ]);

        setting($data)->save();

        return back()->with([
            'type'    => 'success',
            'message' => trans('Settings updated successfully.'),
        ]);
    }
}
