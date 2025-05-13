<?php

declare(strict_types=1);

namespace App\Extensions\Midjourney\System;

use App\Extensions\Midjourney\System\Http\Controllers\MidjourneyCheckStatusController;
use App\Extensions\Midjourney\System\Http\Controllers\MidjourneySettingController;
use App\Extensions\Midjourney\System\Http\Controllers\MidjourneyWebhookController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MidjourneyServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'midjourney');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'midjourney');

        return $this;
    }

    private function registerRoutes(): void
    {
        $this->router()
            ->group([
                'prefix'     => LaravelLocalization::setLocale(),
                'middleware' => ['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
            ], function (Router $router) {
                $router->any('generator/webhook/midjourney', MidjourneyWebhookController::class)
                    ->name('generator.webhook.midjourney')
                    ->withoutMiddleware(['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']);

                $router
                    ->middleware(['auth'])
                    ->name('dashboard.midjourney.check-status')
                    ->get('dashboard/midjourney/check-status', MidjourneyCheckStatusController::class);

                $router->controller(MidjourneySettingController::class)
                    ->prefix('dashboard/admin/settings')
                    ->middleware(['auth', 'admin'])
                    ->name('dashboard.admin.settings.')->group(function (Router $router) {
                        $router->get('piapi-ai', 'index')->name('piapi-ai');
                        $router->post('piapi-ai', 'update')->name('piapi-ai.update');
                    });

            });
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }
}
