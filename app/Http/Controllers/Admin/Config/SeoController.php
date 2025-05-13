<?php

namespace App\Http\Controllers\Admin\Config;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\PrivacyTerms;
use App\Models\Setting;
use App\Models\SettingTwo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoController extends Controller
{
    protected $settings;

    protected $settingTwo;

    public function __construct()
    {
        $this->settings = Setting::query()->first();
        $this->settingTwo = SettingTwo::query()->first();
    }

    public function index(): View
    {
        return view('panel.admin.config.seo');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $this->settings->update([
            'google_analytics_code' => $request->get('google_analytics_code'),
            'meta_keywords'         => $request->get('meta_keywords'),
            'meta_title'            => $request->get('meta_title'),
            'meta_description'      => $request->get('meta_description'),
        ]);

        $metaTitleLocal = $request->get('metaTitleLocal');
        $metaDescLocal = $request->get('metaDescLocal');

        if ($metaTitleLocal == $this->settingTwo->languages_default) {
            $this->settings->meta_title = $request->get('meta_title');
        } else {
            $meta_title = PrivacyTerms::where('type', 'meta_title')->where('lang', $metaTitleLocal)->first();
            if ($meta_title) {
                $meta_title->content = $request->get('meta_title');
                $meta_title->save();
            } else {
                $new_meta_title = new PrivacyTerms;
                $new_meta_title->type = 'meta_title';
                $new_meta_title->lang = $metaTitleLocal;
                $new_meta_title->content = $request->get('meta_title');
                $new_meta_title->save();
            }
        }

        if ($metaDescLocal == $this->settingTwo->languages_default) {
            $this->settings->meta_description = $request->get('meta_description');
        } else {
            $meta_description = PrivacyTerms::where('type', 'meta_desc')->where('lang', $metaDescLocal)->first();
            if ($meta_description) {
                $meta_description->content = $request->get('meta_description');
                $meta_description->save();
            } else {
                $new_meta_description = new PrivacyTerms;
                $new_meta_description->type = 'meta_desc';
                $new_meta_description->lang = $metaDescLocal;
                $new_meta_description->content = $request->get('meta_description');
                $new_meta_description->save();
            }
        }

        Setting::forgetCache();
        SettingTwo::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }
}
