<?php

namespace App\Http\Controllers\Admin\Config;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SettingTwo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::query()->first();
    }

    public function index(): View
    {
        return view('panel.admin.config.login');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $settingTwo = SettingTwo::query()->first();
        $settingTwo->update([
            'daily_limit_enabled'       => $request->has('daily_limit_enabled'),
            'daily_voice_limit_enabled' => $request->has('daily_voice_limit_enabled'),
            'allowed_images_count'      => $request->get('allowed_images_count'),
            'allowed_voice_count'       => $request->get('allowed_voice_count'),
        ]);

        $this->settings->update([
            'recaptcha_login'            => $request->has('recaptcha_login'),
            'recaptcha_register'         => $request->has('recaptcha_register'),
            'login_without_confirmation' => $request->has('login_without_confirmation'),
            'recaptcha_sitekey'          => $request->get('recaptcha_sitekey'),
            'recaptcha_secretkey'        => $request->get('recaptcha_secretkey'),
            'login_with_otp'             => $request->get('login_with_otp'),
            'register_active'            => $request->get('register_active'),
            'facebook_active'            => $request->has('facebook_active'),
            'google_active'              => $request->has('google_active'),
            'github_active'              => $request->has('github_active'),
        ]);

        setting(
            [
                'freeCreditsUponRegistration'     => $request->get('entities'),
            ]
        )->save();

        Setting::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }
}
