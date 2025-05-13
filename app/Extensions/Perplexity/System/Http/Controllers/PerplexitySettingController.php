<?php

namespace App\Extensions\Perplexity\System\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerplexitySettingController extends Controller
{
    public function index()
    {
        return view('perplexity::settings');
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'perplexity_key' => 'required|string|max:255',
        ]);

        if (Helper::appIsNotDemo()) {
            setting([
                'perplexity_key' => $request->perplexity_key,
            ])->save();
        }

        return back()->with(['message' => __('Updated Successfully'), 'type' => 'success']);
    }
}
