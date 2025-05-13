<?php

namespace App\Http\Controllers\Settings;

use App\Helpers\Classes\Helper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SearchapiSettingController
{
    public function index(Request $request): RedirectResponse|View
    {
        return view('default.panel.admin.settings.searchapi');
    }

    public function update(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $data = $request->validate([
            'searchapi_api_key'         => 'required|string',
            'searchapi_api_for_youtube' => 'sometimes|nullable|string',
        ]);

        $data['searchapi_api_for_youtube'] = (int) isset($data['searchapi_api_for_youtube']);

        setting($data)->save();

        return back()->with([
            'type'    => 'success',
            'message' => trans('Searchapi Settings updated successfully.'),
        ]);
    }
}
