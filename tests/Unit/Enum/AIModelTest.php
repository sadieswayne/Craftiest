<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;

test('all cases should be labeled', function ($model) {

    expect($model)
        ->label()
        ->toBeString();

})->with('entities');

test('all cases should have driver class', function ($model) {

    expect($model)
        ->driverClass()
        ->toBeString()
        ->and(class_exists($model->driverClass()))
        ->toBeTrue();

})->with('entities');

test('ai model enum relations', function () {

    expect(EntityEnum::cases())
        ->each(
            fn ($model) => $model->engine()
                ->toBeInstanceOf(EngineEnum::class)
        );
});
