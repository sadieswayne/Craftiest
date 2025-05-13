<?php

namespace App\Providers;

use App\Helpers\Classes\Helper;
use App\Helpers\Classes\TableSchema;
use App\Models\Frontend\FrontendSectionsStatus;
use App\Models\Frontend\FrontendSetting;
use App\Models\OpenAIGenerator;
use App\Models\Section\AdvancedFeaturesSection;
use App\Models\Section\BannerBottomText;
use App\Models\Section\ComparisonSectionItems;
use App\Models\Section\FeaturesMarquee;
use App\Models\Section\FooterItem;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use App\View\Composers\PlanComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public array $tables = [];

    public ?Setting $settings = null;

    public function register(): void {}

    public function boot(): void
    {
        // Share app status
        $this->sharedAppStatus();

        // pagination
        Paginator::useBootstrap();

        if (! Helper::dbConnectionStatus()) {
            return;
        }

        $this->tables = app('magicai_tables');

        if (! TableSchema::hasTable('migrations', $this->tables) || ! TableSchema::hasTable('settings', $this->tables)) {
            return;
        }

        $this->shareSetting();

        $this->shareAiGenerator();

        //        $this->viewComposerShare();

        $this->goodForNowShare();

        View::composer(
            ['components.navbar.navbar', 'panel.layout.partials.menu'],
            PlanComposer::class
        );
    }

    public function goodForNowShare(): void
    {
        $goodForNow = false;

        if (TableSchema::hasTable('settings_two', $this->tables)) {
            $goodForNow = $this->settings && Helper::settingTwo('liquid_license_type');
        }

        View::share('good_for_now', $goodForNow);
    }

    public function viewComposerShare(): void
    {

        view()->composer('*', function ($view) {
            if (Auth::check()) {

                if (
                    ! Cache::has('total_words_' . Auth::id())
                    or ! Cache::has('total_documents_' . Auth::id())
                    or ! Cache::has('total_text_documents_' . Auth::id())
                    or ! Cache::has('total_image_documents_' . Auth::id())
                ) {
                    $total_documents_finder = UserOpenai::where('user_id', Auth::id())->get();
                    $total_words = UserOpenai::where('user_id', Auth::id())->sum('credits');
                    Cache::put('total_words_' . Auth::id(), $total_words, now()->addMinutes(360));
                    $total_documents = count($total_documents_finder);
                    Cache::put('total_documents_' . Auth::id(), $total_documents, now()->addMinutes(360));
                    $total_text_documents = count($total_documents_finder->where('credits', '!=', 1));
                    Cache::put('total_text_documents_' . Auth::id(), $total_text_documents, now()->addMinutes(360));
                    $total_image_documents = count($total_documents_finder->where('credits', '==', 1));
                    Cache::put('total_image_documents_' . Auth::id(), $total_image_documents, now()->addMinutes(360));
                }
                $total_words = Cache::get('total_words_' . Auth::id()) ?? 0;
                View::share('total_words', $total_words);
                $total_documents = Cache::get('total_documents_' . Auth::id()) ?? 0;
                View::share('total_documents', $total_documents);
                $total_text_documents = Cache::get('total_text_documents_' . Auth::id()) ?? 0;
                View::share('total_text_documents', $total_text_documents);
                $total_image_documents = Cache::get('total_image_documents_' . Auth::id()) ?? 0;
                View::share('total_image_documents', $total_image_documents);
            }
        });
    }

    public function shareAiGenerator(): void
    {
        if (! TableSchema::hasTable('openai', $this->tables)) {
            return;
        }

        View::share(
            'aiWriters',
            OpenAIGenerator::query()
                ->orderBy('title', 'asc')
                ->where('active', 1)
                ->get()
        );
    }

    public function shareSetting(): void
    {
        if ($settings = Setting::getCache()) {
            $this->settings = $settings;

            View::share('setting', $settings);
        }

        // frontend setting shared
        if (TableSchema::hasTable('frontend_footer_settings', $this->tables)) {

            $frontendSetting = FrontendSetting::getCache();

            if (! $frontendSetting) {
                $frontendSetting = new FrontendSetting;
                $frontendSetting->save();
            }

            View::share('fSetting', $frontendSetting);
        }

        // frontend sections status shared
        if (TableSchema::hasTable('frontend_sections_statuses_titles', $this->tables)) {

            $fSectSettings = FrontendSectionsStatus::getCache();

            if (! $fSectSettings) {
                $fSectSettings = new FrontendSectionsStatus;

                $fSectSettings->save();
            }

            View::share('fSectSettings', FrontendSectionsStatus::first());
        }

        if (TableSchema::hasTable('openai', $this->tables)) {
            View::share('openAiList', OpenAIGenerator::query()->get());
        }

        // advanced_features_section
        if (TableSchema::hasTable('advanced_features_section', $this->tables)) {
            $advanced_features_section = AdvancedFeaturesSection::all();
            View::share('advanced_features_section', $advanced_features_section);
        }

        // comparison
        if (TableSchema::hasTable('comparison_section_items', $this->tables)) {
            $comparison_section_items = ComparisonSectionItems::all();
            View::share('comparison_section_items', $comparison_section_items);
        }

        // comparison
        if (TableSchema::hasTable('features_marquees', $this->tables)) {

            $marqueeItems = FeaturesMarquee::getCache(static function () {
                return FeaturesMarquee::query()->select('title', 'position')->get();
            });

            View::share('top_marquee_items', $marqueeItems->where('position', 'top')->pluck('title')->toArray());
            View::share('bottom_marquee_items', $marqueeItems->where('position', 'bottom')->pluck('title')->toArray());
        }

        // footer item

        if (TableSchema::hasTable('footer_items', $this->tables)) {
            $footer_items = FooterItem::query()->pluck('item')->toArray();
            View::share('footer_items', $footer_items);
        }
        // modern
        if (TableSchema::hasTable('banner_bottom_texts', $this->tables)) {

            $banner_bottom_texts = BannerBottomText::getCache(static function () {
                return BannerBottomText::query()->select('text')->pluck('text')->toArray();
            });

            View::share('banner_bottom_texts', $banner_bottom_texts);
        }

        // commission
        if (TableSchema::hasTable('app_settings', $this->tables)) {
            $is_onetime_commission = setting('onetime_commission', 0);
            View::share('is_onetime_commission', $is_onetime_commission);
        }

        // App service providerde yoxlandigi ucun burda HasTable ye gerek yok!
        // frontend setting shared
        if (TableSchema::hasTable('settings_two', $this->tables)) {
            $settings_two = SettingTwo::getCache();

            if (! $settings_two) {
                $settings_two = new SettingTwo;
                $settings_two->save();
            }
            View::share('settings_two', $settings_two);
        }
    }

    public function sharedAppStatus(): void
    {
        View::share('app_is_demo', Helper::appIsDemo());
        View::share('app_is_not_demo', Helper::appIsNotDemo());
    }
}
