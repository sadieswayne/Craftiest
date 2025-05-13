<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Models\Plan;

test('mutators', function () {

    $plan = Plan::factory()->create();

    expect($plan)
        ->toBeInstanceOf(Plan::class)
        ->ai_models
        ->toBeArray()
        ->ai_models
        ->not
        ->toBeEmpty();

    $aiModels = $plan->ai_models;
    $aiModels[EngineEnum::OPEN_AI->value] = [];
    $aiModels[EngineEnum::OPEN_AI->value][EntityEnum::DAVINCI->value] = 2;
    $plan->update([
        'ai_models' => $aiModels,
    ]);

    $this->assertSame(2, $plan->refresh()->ai_models[EngineEnum::OPEN_AI->value][EntityEnum::DAVINCI->value]);
});

test('return type of checkOpenAiItemCount', function () {
    expect(Plan::factory()->create([
        'open_ai_items' => null,
    ]))->open_ai_items
        ->toBeEmpty()
        ->checkOpenAiItemCount()
        ->toBeInt();
});
