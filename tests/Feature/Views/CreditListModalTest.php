<?php

declare(strict_types=1);

use App\Domains\Engine\Contracts\EngineDriverInterface;
use App\Domains\Engine\Facades\Engine;
use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Models\Setting;
use App\Models\SettingTwo;

beforeEach(function () {
    Setting::factory()->create();
    SettingTwo::factory()->create();
});

test('modal listable entity', function () {

    expect(Entity::all(onlyListableCases: false))
        ->count()
        ->toBe(count(EntityEnum::cases()))
        ->and(Entity::all(onlyListableCases: true))
        ->count()
        ->toBe(count(EntityEnum::listableCases()));

});

test('modal credit list', function () {

    loginAsUser();

    $engines = Engine::all()->map(function ($engine) {
        return [
            'engine'   => $engine,
            'entities' => Entity::all($engine->enum(), onlyListableCases: true),
        ];
    });

    expect($engines)
        ->toBeCollection()
        ->not
        ->toBeEmpty()
        ->each(
            fn ($item) => $item
                ->toHaveKey('engine')
                ->engine
                ->toBeInstanceOf(EngineDriverInterface::class)
                ->not
                ->toBeEmpty()
                ->engine->label()
                ->toBeString()
                ->entities
                ->toBeCollection()
                ->not
                ->toBeEmpty()
                ->each(
                    fn ($entity) => $entity
                        ->toBeInstanceOf(EntityDriverInterface::class)
                        ->creditBalance()
                        ->toBeFloat()
                ),
        );
});
