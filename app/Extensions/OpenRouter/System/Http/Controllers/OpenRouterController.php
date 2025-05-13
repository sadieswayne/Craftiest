<?php

namespace App\Extensions\OpenRouter\System\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpenRouterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show(): View
    {
        return view('open-router::settings');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        if (Helper::appIsNotDemo()) {
            setting([
                'open_router_api'           => $request->get('open_router_api'),
                'default_open_router_model' => $request->get('default_open_router_model'),
                'open_router_status'        => $request->get('open_router_status'),
            ])->save();
        }

        return back()->with(['message' => __('Settings saved successfully'), 'type' => 'success']);
    }
}
