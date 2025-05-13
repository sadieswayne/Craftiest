<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

test('calculator traits should have interfaces', function () {

    $traits = File::files(app_path('Domains/Entity/Concerns/Calculate'));
    $interfaces = File::files(app_path('Domains/Entity/Contracts/Calculate'));

    expect($traits)
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->and($interfaces)
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->toHaveCount(count($traits) + 1); // without WithCalculate
});

test('input traits should have interfaces', function () {

    $traits = File::files(app_path('Domains/Entity/Concerns/Input'));
    $interfaces = File::files(app_path('Domains/Entity/Contracts/Input'));

    expect($traits)
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->and($interfaces)
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->toHaveCount(count($traits));
});
