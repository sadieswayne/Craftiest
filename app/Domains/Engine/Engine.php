<?php

declare(strict_types=1);

namespace App\Domains\Engine;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Facades\Entity;
use Illuminate\Support\Collection;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class Engine extends Manager
{
    public function getDefaultDriver(): string
    {
        return EngineEnum::OPEN_AI->value;
    }

    /**
     * @template T
     *
     * @returns T & \App\Domains\Engine\BaseDriver
     */
    public function driver($driver = null): BaseDriver
    {
        if ($driver && ! $driver instanceof EngineEnum) {
            throw new InvalidArgumentException('Driver must be an instance of AIEngine');
        }

        /** @var BaseDriver $driverInstance */
        $driverInstance = parent::driver($driver?->value);

        return $driverInstance->createDriverReqsIfNeeded();
    }

    public function all(): Collection
    {
        return collect(EngineEnum::cases())
            ->map(function ($model) {
                return self::driver($model);
            });
    }

    public function entityDrivers(?EngineEnum $engine = null): Collection
    {
        $engineEnum = $engine ?: EngineEnum::from($this->getDefaultDriver());

        return Entity::all($engineEnum);
    }
}
