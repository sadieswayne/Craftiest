<?php

declare(strict_types=1);

namespace App\Extensions\ChatShare\System;

use App\Extensions\ChatShare\System\Http\Controllers\ShareController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ChatShareServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'chat-share');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'chat-share');

        return $this;
    }

    private function registerRoutes(): static
    {
        $this->router()
            ->group([
                'prefix'     => LaravelLocalization::setLocale(),
                'middleware' => ['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
            ], function (Router $router) {
                $router->get('share/{category}/{chat}/{message}', [ShareController::class, 'share']);
                $router->post('share/link', [ShareController::class, 'createLink'])->name('make.link');
            });

        return $this;
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }
}
