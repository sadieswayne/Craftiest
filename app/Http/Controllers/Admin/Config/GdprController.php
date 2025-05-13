<?php

namespace App\Http\Controllers\Admin\Config;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GdprController extends Controller
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::query()->first();
    }

    public function index(): View
    {
        return view('panel.admin.config.gdpr');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $this->settings->update([
            'gdpr_status'  => $request->has('gdpr_status'),
            'gdpr_button'  => $request->get('gdpr_button'),
            'gdpr_content' => $request->get('gdpr_content'),
        ]);

        Setting::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }
}
