<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PrivacyTerms;
use App\Models\Setting;
use App\Models\SettingTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function pageContent($slug)
    {
        $page = Page::where('slug', $slug)->first();

        // Check page status
        if (! $page->status) {
            abort(404);
        }

        if ($page) {
            return view('page.index', compact('page'));
        } else {
            abort(404);
        }
    }

    public function pageList()
    {
        $list = Page::orderBy('id', 'asc')->get();

        return view('panel.page.list', compact('list'));
    }

    public function pageAddOrUpdate($id = null)
    {
        if ($id == null) {
            $page = null;
        } else {
            $page = Page::where('id', $id)->firstOrFail();
        }

        return view('panel.page.form', compact('page'));
    }

    public function pageDelete($id = null)
    {
        $page = Page::where('id', $id)->firstOrFail();
        $page->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function pageAddOrUpdateSave(Request $request)
    {

        if ($request->page_id != 'undefined') {
            $page = Page::where('id', $request->page_id)->firstOrFail();
        } else {
            $page = new Page;
        }
        $page->titlebar_status = $request->titlebar_status;
        $page->show_on_footer = $request->show_on_footer;
        $page->title = $request->title;
        $page->slug = Str::slug($request->slug);
        $page->content = $request->get('content');
        $page->status = $request->status;
        $page->save();
    }

    public function pagePrivacy()
    {

        $lang = app()->getLocale();
        $settingTwo = SettingTwo::getCache();
        $settings = Setting::getCache();

        $page = (object) [];
        $page->titlebar_status = true;
        $page->show_on_footer = false;
        if ($lang == $settingTwo->languages_default) {
            $page->content = $settings->privacy_content;
        } else {
            $privacy = PrivacyTerms::where('type', 'privacy')->where('lang', $lang)->first();
            $page->content = $privacy?->content;
        }

        $page->status = $settings->privacy_enable;
        $page->title = __('Privacy Policy');

        // Check page status
        if (! $page->status) {
            abort(404);
        }

        if ($settings) {
            return view('page.index', compact('page'));
        } else {
            abort(404);
        }
    }

    public function pageTerms()
    {

        $lang = app()->getLocale();
        $settingTwo = SettingTwo::getCache();
        $settings = Setting::getCache();

        $page = (object) [];
        $page->titlebar_status = true;

        if ($lang == $settingTwo->languages_default) {
            $page->content = $settings->terms_content;
        } else {
            $terms = PrivacyTerms::where('type', 'terms')->where('lang', $lang)->first();
            $page->content = $terms?->content;
        }

        $page->status = $settings->privacy_enable;
        $page->title = __('Terms and Conditions');

        // Check page status
        if (! $page->status) {
            abort(404);
        }

        if ($settings) {
            return view('page.index', compact('page'));
        } else {
            abort(404);
        }
    }
}
