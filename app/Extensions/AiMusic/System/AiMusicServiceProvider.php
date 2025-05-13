<?php

declare(strict_types=1);

namespace App\Extensions\AiMusic\System;

use App\Extensions\AiMusic\System\Http\Controllers\AiMusicController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AiMusicServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes()
            ->registerMigrations();
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'ai-music');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'ai-music');

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
                        $router->prefix('user')
                            ->name('user.')
                            ->group(function (Router $router) {
                                $router->resource('ai-music', AiMusicController::class)->except('destroy', 'show');
                                $router->get('ai-music-delete/{id}', [AiMusicController::class, 'delete'])->name('ai-music.delete');
                                $router->get('ai-music-check', [AiMusicController::class, 'checkVideoStatus'])->name('ai-music.check');
                            });
                    });
            });

        return $this;
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }
}
