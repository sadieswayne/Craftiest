<?php

declare(strict_types=1);

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;

test('enum', function ($model) {
    expect(Entity::driver($model))->enum()->toBeInstanceOf(EntityEnum::class);
})->with('entities');

test('subLabel', function () {

    expect(EntityEnum::cases())
        ->each(
            fn ($case) => $case->subLabel()->toBeString()->not->toBeEmpty(),
        );
});

test('listable cases from creditBy', function () {

    $cases = EntityEnum::cases();

    $listableCases = EntityEnum::listableCases();

    expect($listableCases)
        ->not
        ->toBeEmpty()
        // ->not # now we show all cases
        ->toHaveCount(count($cases));
});
