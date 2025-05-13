<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\AIChatController;
use App\Http\Controllers\Chatbot\ChatbotAssetsController;
use App\Http\Controllers\Chatbot\ChatbotEmbedController;
use App\Http\Controllers\Chatbot\ChatbotTokenController;
use App\Support\Vite;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ChatbotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\Illuminate\Foundation\Vite::class, static function () {
            return new Vite;
        });
    }

    public function boot(Kernel $kernel): void
    {
        $this->setMiddlewareGroup($kernel);

        $this->registerRoutes();
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }

    private function setMiddlewareGroup(Kernel $kernel): void
    {
        $this->router()
            ->middlewareGroup('chatbot_api', [
                \App\Http\Middleware\ChatbotPreflightMiddleware::class,
                \App\Http\Middleware\CorsMiddleware::class,
                \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ]);
    }

    private function registerRoutes(): void
    {
        $this->router()
            ->prefix('chatbot-assets')
            ->controller(ChatbotAssetsController::class)
            ->group(function () {
                $this->app['router']->get('/{entryPoint}', 'asset')->where('entryPoint', '.*');
            });

        $this->router()
            ->middleware('chatbot_api')
            ->prefix('chatbot-api')
            ->group(function () {
                Route::any('/token', ChatbotTokenController::class);
                Route::match(['get', 'post', 'options'], '/chat-send', [AIChatController::class, 'chatOutput']);
                Route::match(['get', 'post', 'options'], '/chatbot-send', [AIChatController::class, 'chatbotOutput']);
                Route::match(['options', 'post'], '/open-chatbot-area', [
                    AIChatController::class, 'openChatBotArea',
                ])->name('open-chatbot-area');
                Route::any('embed', ChatbotEmbedController::class)->name('embed');
                Route::post('/start-new-chatbot', [AIChatController::class, 'startNewChatBot']);
            })->name('chatbot-api.');
    }
}
