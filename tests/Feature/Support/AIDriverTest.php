<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Contracts\Calculate\WithCharsInterface;
use App\Domains\Entity\Contracts\Calculate\WithImagesInterface;
use App\Domains\Entity\Contracts\Calculate\WithImageToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToSpeechInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithVideoToVideoInterface;
use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;

test('auth error', function () {
    Entity::driver()->increaseCredit(2);
})->throws(RuntimeException::class, 'User is not provided');

test('driverClass', function () {
    expect(Entity::driver())->toBeInstanceOf(EntityEnum::GPT_4_O->driverClass());
});

test('driverClass for user', function () {
    $user = loginAsUser();

    expect(Entity::driverForUser($user))->toBeInstanceOf(EntityEnum::GPT_4_O->driverClass());
});

test('specific driverClass for user', function () {
    $user = loginAsUser();

    expect(Entity::driverForUser($user, EntityEnum::FLUX_PRO))->toBeInstanceOf(EntityEnum::FLUX_PRO->driverClass());
});

test('driver name to method name', function () {

    $values = collect(EntityEnum::cases())->map(function ($value) {
        return str($value->value)->slug()->camel()->toString();
    })->toArray();

    expect($values)->each->toBeString();
});

test('driver has accessible', function () {
    expect(Entity::driver(EntityEnum::AZURE)->inputVoiceCount(1))
        ->toBeInstanceOf(EntityEnum::AZURE->driverClass());
});

test('input test', function () {
    $driver = Entity::driver(EntityEnum::GPT_4_O);

    $driver->input('foo bar');

    expect($driver->getInput())->toBe('foo bar');
});

test('throws when image count is not provided', function () {
    $model = EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0;

    $driver = Entity::driverForUser(loginAsUser(), $model);

    $driver->calculateCredit();

})->throws(RuntimeException::class, 'Input image count is not provided');

test('calculate credit with input for all models', function ($model) {
    $driver = Entity::driverForUser(loginAsUser(), $model);

    match (true) {
        $driver instanceof WithImagesInterface => $driver->inputImageCount(2),
        $driver instanceof WithImageToVideoInterface,
        $driver instanceof WithVideoToVideoInterface,
        $driver instanceof WithTextToVideoInterface  => $driver->inputVideoCount(2),
        $driver instanceof WithTextToSpeechInterface => $driver->inputVoiceCount(2),
        $driver instanceof WithCharsInterface        => $driver->input('fo'),
        default                                      => $driver->input('foo bar'),
    };

    $driver->calculateCredit();

    expect($driver->getCalculatedInputCredit())->toBe(2.0);

})->with('entities');

test('all drivers', function () {

    expect(Entity::all())
        ->toBeCollection()
        ->toHaveCount(count(EntityEnum::cases()))
        ->each(function ($driver) {
            return $driver->toBeInstanceOf(EntityDriverInterface::class)
                ->enum()->toBeInstanceOf(EntityEnum::class);
        });
});

test('all drivers with engine filter', function () {

    expect(Entity::all(EngineEnum::OPEN_AI))
        ->toBeCollection()
        ->not
        ->toHaveCount(count(EntityEnum::cases()))
        ->each(function ($driver) {
            return $driver->toBeInstanceOf(EntityDriverInterface::class)
                ->enum()->toBeInstanceOf(EntityEnum::class)
                ->enum()->engine()->toBe(EngineEnum::OPEN_AI);
        });
});
