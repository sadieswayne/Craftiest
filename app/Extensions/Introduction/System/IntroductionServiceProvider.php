<?php

declare(strict_types=1);

namespace App\Extensions\Introduction\System;

use App\Extensions\Introduction\System\Http\Controllers\IntroductionController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class IntroductionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
    }

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes();

    }

    public function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/example.php', 'introduction');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'introduction');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'introduction');

        return $this;
    }

    private function registerRoutes(): void
    {
        $this->router()
            ->group([
                'prefix'     => LaravelLocalization::setLocale(),
                'middleware' => ['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
            ], function (Router $router) {
                $router->resource('introductions', IntroductionController::class)->only(['index', 'store']);
            });
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }
}
