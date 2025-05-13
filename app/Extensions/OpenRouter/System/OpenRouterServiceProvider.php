<?php

declare(strict_types=1);

namespace App\Extensions\OpenRouter\System;

use App\Extensions\OpenRouter\System\Http\Controllers\OpenRouterController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class OpenRouterServiceProvider extends ServiceProvider
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
            __DIR__ . '/../resources/assets/js' => public_path('vendor/open-router/js'),
        ], 'extension');

        return $this;
    }

    public function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/open-router.php', 'open-router');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'open-router');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'open-router');

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
            ], function (Router $route) {

                $route->group([
                    'prefix' => 'dashboard/admin/settings',
                    'as'     => 'dashboard.admin.settings.',
                ], function (Router $router) {
                    $router->singleton('open-router', OpenRouterController::class)
                        ->except('edit');
                });
            });

        return $this;
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }
}
