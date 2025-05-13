<?php

declare(strict_types=1);

namespace App\Domains\Engine;

use App\Domains\Engine\Engine as EngineManager;
use App\Domains\Engine\Enums\EngineEnum;
use Illuminate\Support\ServiceProvider;

class EngineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EngineManager::class, function ($app) {
            return new EngineManager($app);
        });

        $this->app->alias(EngineManager::class, 'ai.engine');
    }

    public function boot(): void
    {
        $engine = $this->app->make(EngineManager::class);

        foreach (EngineEnum::cases() as $value) {
            $engine->extend($value->value, function ($app) use ($value) {
                $class = $value->driverClass();

                return new $class;
            });
        }
    }
}
