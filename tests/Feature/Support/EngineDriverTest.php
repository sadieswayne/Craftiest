<?php

declare(strict_types=1);

use App\Domains\Engine\Contracts\EngineDriverInterface;
use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Facades\Engine;
use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Enums\EntityEnum;

test('all drivers', function () {

    expect(Engine::all())
        ->toBeCollection()
        ->toHaveCount(count(EngineEnum::cases()))
        ->each(function ($driver) {
            return $driver->toBeInstanceOf(EngineDriverInterface::class)
                ->enum()->toBeInstanceOf(EngineEnum::class);
        });
});

test('entityDrivers by default engine', function () {

    expect(Engine::entityDrivers())
        ->toBeCollection()
        ->not
        ->toHaveCount(count(EntityEnum::cases()))
        ->each(function ($driver) {
            return $driver->toBeInstanceOf(EntityDriverInterface::class)
                ->enum()->toBeInstanceOf(EntityEnum::class)
                ->enum()->engine()->toBe(EngineEnum::from(Engine::getDefaultDriver()));
        });
});

test('entityDrivers by engine', function () {

    expect(Engine::entityDrivers(EngineEnum::STABLE_DIFFUSION))
        ->toBeCollection()
        ->not
        ->toHaveCount(count(EntityEnum::cases()))
        ->each(function ($driver) {
            return $driver->toBeInstanceOf(EntityDriverInterface::class)
                ->enum()->toBeInstanceOf(EntityEnum::class)
                ->enum()->engine()->toBe(EngineEnum::STABLE_DIFFUSION);
        });
});
