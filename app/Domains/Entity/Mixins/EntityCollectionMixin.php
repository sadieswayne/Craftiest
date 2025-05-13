<?php

declare(strict_types=1);

namespace App\Domains\Entity\Mixins;

use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Contracts\WithCreditInterface;
use App\Domains\Entity\Contracts\WithStatus;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\HigherOrderCollectionProxy;

/**
 * @mixin Collection
 */
class EntityCollectionMixin
{
    public function includeDisabled(): Closure
    {
        /** @phpstan-ignore-next-line */
        return function (bool $condition): Collection|static {
            /**
             * @var Collection|HigherOrderCollectionProxy $this
             */
            return $this->when(
                ! $condition,
                fn ($entities) => $entities->filter(fn (WithStatus $driver) => $driver->isEnabled())
            );
        };
    }

    public function includeUnlisted(): Closure
    {
        /** @phpstan-ignore-next-line */
        return function (bool $condition): Collection|static {
            /**
             * @var Collection|HigherOrderCollectionProxy $this
             */
            return $this->when(
                ! $condition,
                fn ($entities) => $entities->filter(fn (EntityDriverInterface&WithCreditInterface $driver) => $driver->enum() === $driver->creditEnum())
            );
        };
    }
}
