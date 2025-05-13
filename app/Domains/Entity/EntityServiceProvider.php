<?php

declare(strict_types=1);

namespace App\Domains\Entity;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Mixins\EntityCollectionMixin;
use Illuminate\Support\ServiceProvider;
use ReflectionException;

class EntityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EntityManager::class, function ($app) {
            return new EntityManager($app);
        });

        $this->app->alias(EntityManager::class, 'ai.entity');
    }

    public function boot(): void
    {
        $this->registerMixins();

        $entity = $this->app->make(EntityManager::class);

        foreach (EntityEnum::cases() as $value) {

            $entity->extend($value->value, function ($app) use ($value) {
                $class = $value->driverClass();

                return new $class;
            });
        }
    }

    /**
     * @throws ReflectionException
     */
    private function registerMixins(): void
    {
        \Illuminate\Support\Collection::mixin(new EntityCollectionMixin);
    }
}
