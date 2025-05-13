<?php

declare(strict_types=1);

namespace App\Extensions\AIVideoToVideo\System;

use App\Domains\Marketplace\Contracts\UninstallExtensionServiceProviderInterface;
use App\Extensions\AIVideoToVideo\System\Http\Controllers\FalAISettingController;
use App\Extensions\AIVideoToVideo\System\Http\Controllers\GenerateController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AIVideoToVideoServiceProvider extends ServiceProvider implements UninstallExtensionServiceProviderInterface
{
    public function register(): void
    {
        // UserController - openAIGenerator - ai_video
        // panel.user.openai.components.generator_video
        // panel.user.openai.generator
        // $this->registerConfig();
    }

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes()
            ->registerMigrations()
            ->publishAssets()
            ->registerComponents();

    }

    public function registerComponents(): static
    {
        //        $this->loadViewComponentsAs('ai-video-to-video', []);

        return $this;
    }

    public function publishAssets(): static
    {
        $this->publishes([
            //            __DIR__ . '/../resources/assets/js' => public_path('vendor/ai-video-to-video/js'),
            //            __DIR__ . '/../resources/assets/images' => public_path('vendor/ai-video-to-video/images'),
        ], 'extension');

        return $this;
    }

    public function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-video-to-video.php', 'ai-video-to-video');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'ai-video-to-video');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'ai-video-to-video');

        return $this;
    }

    public function registerMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        return $this;
    }

    private function registerRoutes(): static
    {
        $this->router()
            ->group([
                'prefix'     => LaravelLocalization::setLocale(),
                'middleware' => ['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],

            ], function (Router $router) {

                $router
                    ->prefix('dashboard')
                    ->name('dashboard.')
                    ->group(function (Router $router) {

                        $router->controller(GenerateController::class)
                            ->as('user.video-to-video.')
                            ->prefix('user/video-to-video')
                            ->middleware(['auth'])
                            ->group(function (Router $router) {
                                $router->post('generate', 'generate')->name('generate');
                                $router->any('checked', 'checked')->name('checked');
                                $router->any('checked-all', 'checkedAll')->name('checked-all');
                            });

                        $router->controller(FalAISettingController::class)
                            ->prefix('admin/settings')
                            ->middleware(['auth', 'admin'])
                            ->name('admin.settings.')->group(function (Router $router) {
                                $router->get('fal-ai', 'index')->name('fal-ai');
                                $router->post('fal-ai', 'update')->name('fal-ai.update');
                            });
                    });

            });

        return $this;
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }

    public static function uninstall(): void
    {
        // TODO: Implement uninstall() method.
    }
}
