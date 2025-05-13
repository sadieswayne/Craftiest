<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Facades\Engine;

test('driverClass', function () {
    expect(Engine::driver())->toBeInstanceOf(EngineEnum::OPEN_AI->driverClass());
});

test('driver name to method name', function () {

    $values = collect(EngineEnum::cases())->map(function ($value) {
        return str($value->value)->slug()->camel()->toString();
    })->toArray();

    expect($values)->each->toBeString();
});

test('enum', function ($engine) {
    expect(Engine::driver($engine))->enum()->toBeInstanceOf(EngineEnum::class);
})->with('ai_engines');
