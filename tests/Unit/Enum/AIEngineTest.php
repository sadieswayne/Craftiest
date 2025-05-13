<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Models\Setting;
use App\Models\SettingTwo;

function mockSettings(): void
{
    Setting::factory()->create(['gcs_file' => 'fake-gcs.json']);
    SettingTwo::factory()->create(['daily_voice_limit_enabled' => false]);
}

test('engine enum relations', function ($aiEngine) {

    expect($aiEngine)
        ->models()
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->each(
            fn ($models) => $models->toBeInstanceOf(EntityEnum::class)
        );

})->with('ai_engines');

test('rules', function () {

    expect($rules = EngineEnum::rules('plan.ai_models.', ['sometimes|numeric|min:0', 'sometimes|boolean']))
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->sequence(
            fn ($rule) => $rule->toBeString()->toBe('sometimes|numeric|min:0'),
            fn ($rule) => $rule->toBeString()->toBe('sometimes|boolean')
        )
        ->toHaveKeys([
            'plan.ai_models.openai.gpt-3__5-turbo-16k.credit',
            'plan.ai_models.openai.gpt-3__5-turbo-16k.isUnlimited',
        ]);

});

test('default models for ai engines table', function ($aiEngine) {
    expect($aiEngine)
        ->getDefaultModels(Setting::getCache(), SettingTwo::getCache())
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->each(
            fn ($model) => $model->toBeInstanceOf(EntityEnum::class)
        );

})->with('ai_engines');

test('default models for ai engines enum', function () {
    $engines = EngineEnum::cases();
    expect($engines)
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->each(
            fn ($engine) => $engine->getDefaultModels(Setting::getCache(), SettingTwo::getCache())
                ->toBeArray()
                ->not
                ->toBeEmpty()
                ->each(
                    fn ($model) => $model->toBeInstanceOf(EntityEnum::class)
                )
        );
});
