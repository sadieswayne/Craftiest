<?php

declare(strict_types=1);

namespace App\Extensions\FluxPro\System;

use App\Extensions\FluxPro\System\Http\Controllers\FalAISettingController;
use App\Extensions\FluxPro\System\Http\Controllers\FalAIWebhookController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class FluxProServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes();

    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'flux-pro');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'flux-pro');

        return $this;
    }

    private function registerRoutes(): void
    {
        $this->router()
            ->group([
                'prefix'     => LaravelLocalization::setLocale(),
                'middleware' => ['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
            ], function (Router $router) {
                $router->any('generator/webhook/fal-ai', FalAIWebhookController::class)
                    ->name('generator.webhook.fal-ai')
                    ->withoutMiddleware(['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']);

                $router->controller(FalAISettingController::class)
                    ->prefix('dashboard/admin/settings')
                    ->middleware(['auth', 'admin'])
                    ->name('dashboard.admin.settings.')->group(function (Router $router) {
                        $router->get('fal-ai', 'index')->name('fal-ai');
                        $router->post('fal-ai', 'update')->name('fal-ai.update');
                    });

            });
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }
}
