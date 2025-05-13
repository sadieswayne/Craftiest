<?php

namespace App\Http\Controllers\Admin\Config;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\SettingTwo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorageController extends Controller
{
    protected $settingTwo;

    public function __construct()
    {
        $this->settingTwo = SettingTwo::query()->first();
    }

    public function index(): View
    {
        $cloudflare = Extension::query()->where('slug', 'cloudflare-r2')->exists();

        return view('panel.admin.config.storage', compact('cloudflare'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $request->validate([
            'ai_image_storage' => 'required',
        ]);

        $this->settingTwo->update([
            'ai_image_storage' => $request->get('ai_image_storage'),
        ]);

        SettingTwo::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }
}
