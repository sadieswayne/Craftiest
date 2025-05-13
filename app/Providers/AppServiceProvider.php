<?php

namespace App\Providers;

use App\Helpers\Classes\Helper;
use App\Helpers\Classes\TableSchema;
use App\Models\Ad;
use App\Models\Finance\AiChatModelPlan;
use App\Models\Frontend\FrontendSectionsStatus;
use App\Models\Frontend\FrontendSetting;
use App\Models\OpenAIGenerator;
use App\Models\Section\BannerBottomText;
use App\Models\Section\FeaturesMarquee;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use App\Observer\AdObserver;
use App\Observer\FeaturesMarqueeObserver;
use App\Observer\Frontend\BannerBottomTextObserver;
use App\Observer\Frontend\FrontendSectionsStatusObserver;
use App\Observer\Frontend\FrontendSettingObserver;
use App\Observer\OpenAIGeneratorObserver;
use App\Observer\Setting\SettingObserver;
use App\Observer\Setting\SettingTwoObserver;
use App\Observer\UserObserver;
use App\Services\MemoryLimit;
use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    public array $tables = [];

    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        $this->forceSchemeHttps();

        $this->app->useLangPath(base_path('lang'));

        if (Helper::dbConnectionStatus()) {
            Schema::defaultStringLength(191);

            $this->initializeTables();
            $this->setTheme();
            $this->configSet();
            $this->jobRuns();
            $this->app->setLocale($this->getLocale('en'));
        } else {
            Theme::set('default');
        }

        $this->registerHealthChecks();
        $this->bootBladeDirectives();
        $this->bootObservers();
    }

    protected function configSet(): void
    {
        if (TableSchema::hasTable('settings', $this->tables) && DB::table('settings')->exists()) {
            $setting = Setting::getCache();
            $this->setPusherConfig();
            $this->setRecaptchaConfig($setting);
            $this->setMailConfig($setting);
        }
    }

    protected function setPusherConfig(): void
    {
        $this->app['config']->set('broadcasting.connections.pusher.key', setting('pusher_app_key', ''));
        $this->app['config']->set('broadcasting.connections.pusher.secret', setting('pusher_app_secret', ''));
        $this->app['config']->set('broadcasting.connections.pusher.app_id', setting('pusher_app_id', ''));
        $this->app['config']->set('broadcasting.connections.pusher.cluster', setting('pusher_app_cluster', 'mt1'));
        $this->app['config']->set('broadcasting.connections.pusher.options.host', 'api-' . setting('pusher_app_cluster', 'mt1') . '.pusher.com');
    }

    protected function setRecaptchaConfig($setting): void
    {
        $this->app['config']->set('services.recaptcha.key', $setting->recaptcha_sitekey);
        $this->app['config']->set('services.recaptcha.secret', $setting->recaptcha_secretkey);
    }

    protected function setMailConfig($setting): void
    {
        $this->app['config']->set('mail.mailers.smtp.transport', config('mail.default', 'smtp'));
        $this->app['config']->set('mail.mailers.smtp.host', $setting->smtp_host ?? config('mail.mailers.smtp.host'));
        $this->app['config']->set('mail.mailers.smtp.port', (int) ($setting->smtp_port ?? config('mail.mailers.smtp.port')));
        $this->app['config']->set('mail.mailers.smtp.encryption', ($setting->smtp_encryption ?? config('mail.mailers.smtp.encryption')));
        $this->app['config']->set('mail.mailers.smtp.username', $setting->smtp_username ?? config('mail.mailers.smtp.username'));
        $this->app['config']->set('mail.mailers.smtp.password', $setting->smtp_password ?? config('mail.mailers.smtp.password'));

        $this->app['config']->set('mail.from.address', $setting->smtp_email ?? config('mail.from.address'));
        $this->app['config']->set('mail.from.name', $setting->smtp_sender_name ?? config('mail.from.name'));
    }

    protected function initializeTables(): void
    {
        $this->app->singleton('magicai_tables', fn () => (new TableSchema)->allTables());

        $this->tables = app('magicai_tables');

        $this->app->singleton('ai_chat_model_plan', function () {
            if (Schema::hasColumn('ai_chat_model_plans', 'entity_id')) {
                return AiChatModelPlan::query()->select('entity_id')->pluck('entity_id')->toArray();
            }

            return [];
        });
    }

    protected function getLocale(string $default): string
    {
        if (TableSchema::hasTable('settings_two', $this->tables)) {
            return Helper::settingTwo('languages_default', $default);
        }

        return $default;
    }

    protected function setTheme(): void
    {
        if (TableSchema::hasTable('app_settings', $this->tables)) {
            $this->setDefaultSettings();

            $activated_front_theme = setting('front_theme');
            $activated_dash_theme = setting('dash_theme');

            $sameTheme = $activated_front_theme === $activated_dash_theme;

            $isDashboard = request()->is('dashboard*', '*/dashboard*');

            $themeToSet = match (true) {
                $sameTheme   => $activated_front_theme,
                $isDashboard => $activated_dash_theme,
                default      => $activated_front_theme,
            };

            Theme::set($themeToSet);
        }
    }

    protected function setDefaultSettings(): void
    {
        if (setting('front_theme') === null) {
            setting(['front_theme' => 'default'])->save();
        }
        if (setting('dash_theme') === null) {
            setting(['dash_theme' => 'default'])->save();
        }
    }

    protected function registerHealthChecks(): void
    {
        Health::checks([
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            DatabaseCheck::new(),
            MemoryLimit::new(),
        ]);
    }

    public function bootObservers(): void
    {
        SettingTwo::observe(SettingTwoObserver::class);
        Setting::observe(SettingObserver::class);
        FrontendSectionsStatus::observe(FrontendSectionsStatusObserver::class);
        FrontendSetting::observe(FrontendSettingObserver::class);
        FeaturesMarquee::observe(FeaturesMarqueeObserver::class);
        BannerBottomText::observe(BannerBottomTextObserver::class);
        OpenAIGenerator::observe(OpenAIGeneratorObserver::class);
        Ad::observe(AdObserver::class);
        User::observe(UserObserver::class);
    }

    public function jobRuns(): void
    {
        if (Schema::hasTable('jobs')) {
            $wordlist = DB::table('jobs')->where('id', '>', 0)->get();

            if (count($wordlist) > 0) {
                // change each job not default to default
                DB::table('jobs')
                    ->where('queue', '<>', 'default')
                    ->update(['queue' => 'default']);

                Artisan::call('queue:work --once');
            }
        }
    }

    public function forceSchemeHttps(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    private function bootBladeDirectives(): void
    {
        Blade::directive('formatNumber', function ($expression) {
            return "<?php echo is_numeric($expression) ? rtrim(rtrim(number_format((float) $expression, 2), '0'), '.') : ($expression); ?>";
        });

        Blade::directive('formatNumberShort', static function ($expression) {
            return "<?php
            if (is_null({$expression})) {
                echo '0';
            } else if ( !is_numeric({$expression} ) ) {
                echo {$expression};
            } else {
                echo number_shorten({$expression});
            }
            ?>";
        });

        Blade::directive('showCredit', static function ($model) {
            return "<?php echo
                    {$model}->checkIfThereUnlimited() ?
                        __('Unlimited') :
                        {$model}->totalCredits();
             ?>";
        });

        Blade::directive('pushOnceFor', static function ($expression) {
            [$name, $suffix] = str($expression)->substr(1, -1)
                ->trim()
                ->replace('-', '_')
                ->explode(':');

            $key = '__pushonce_' . $name . '_' . $suffix;

            return "<?php if(! isset(\$__env->{$key})): \$__env->{$key} = 1; \$__env->startPush('{$name}'); ?>";
        });

        Blade::directive('endPushOnceFor', static function () {
            return '<?php $__env->stopPush(); endif; ?>';
        });
    }
}
