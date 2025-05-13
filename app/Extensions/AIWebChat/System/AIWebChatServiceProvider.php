<?php

declare(strict_types=1);

namespace App\Extensions\AIWebChat\System;

use App\Extensions\AIWebChat\System\Http\Controllers\AIWebChatController;
use App\Http\Middleware\CheckTemplateTypeAndPlan;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AIWebChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
    }

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes()
            ->registerMigrations()
            ->publishAssets();

    }

    public function publishAssets(): static
    {
        $this->publishes([
            __DIR__ . '/../resources/assets/js'     => public_path('vendor/web-chat/js'),
//            __DIR__ . '/../resources/assets/images' => public_path('themes/default/assets/img'),
        ], 'extension');

        return $this;
    }

    public function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/web-chat.php', 'web-chat');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'web-chat');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'web-chat');

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
                    ->controller(AIWebChatController::class)
                    ->prefix('dashboard/user/openai')
                    ->name('dashboard.user.openai.')
                    ->group(function () {
                        Route::get('webchat', 'openAIGeneratorWorkbook')->name('webchat.workbook')->middleware(CheckTemplateTypeAndPlan::class);
                        Route::post('webchat/open-chat-area-container', 'openChatAreaContainer')->name('webchat.open-chat-area-container');
                        Route::post('webchat/start-new-chat', 'startNewChat')->name('webchat.start-new-chat');
                        Route::get('webchat/stream', 'chatStream')->name('webchat.stream');
                        Route::match(['get', 'post'], 'webchat/chat-send', 'chatOutput')->name('webchat.chat-send');
                    });
            });

        return $this;
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }
}
