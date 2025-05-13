<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Contracts\Calculate\WithCharsInterface;
use App\Domains\Entity\Contracts\Calculate\WithImagesInterface;
use App\Domains\Entity\Contracts\Calculate\WithImageToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithPlagiarismInterface;
use App\Domains\Entity\Contracts\Calculate\WithSpeechToTextInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToSpeechInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithVisionPreviewInterface;
use App\Domains\Entity\Contracts\Calculate\WithWordsInterface;
use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Contracts\WithCreditInterface;
use App\Domains\Entity\EntityStats;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use Database\Seeders\EngineSeeder;
use Database\Seeders\EntitySeeder;

beforeEach(function () {
    Setting::factory()->create();
    SettingTwo::factory()->create();
    $this->fullCount = count(EngineEnum::cases()) + count(EntityEnum::listableCases());
});

function mockDefaultModelsCount($type): int
{
    $engines = EngineEnum::cases();
    $count = 0;

    foreach ($engines as $engine) {
        $engineDefaultModels = $engine->getDefaultModels(Setting::getCache(), SettingTwo::getCache());

        $filteredCollection = collect($engineDefaultModels)
            ->map(function ($entity) {
                return Entity::driver($entity); // Adjust if `Entity::driver()` isn't static
            })
            ->filter(fn (EntityDriverInterface $driver) => $driver instanceof $type);

        $count += $filteredCollection->count();
    }

    return $count;
}

test('total default models credits for current user', function ($increase, $type) {
    $user = loginAsUser();

    $this->seed([
        EntitySeeder::class,
        EngineSeeder::class,
    ]);

    $stats = EntityStats::type($type)
        ->includeDisabled(true)
        ->includeUnlisted(! $increase);

    $stats->list()
        ->each(function (WithCreditInterface $entity) use ($increase, $user) {

            $this->assertEquals($user->id, $entity->getUser()->id);

            if ($increase) {
                $entity->increaseCredit(1.5);
            } else {
                $entity->setCredit(1.5);
            }
        });
    expect($stats->totalCredits())->toBe(mockDefaultModelsCount($type) * 1.5);
})->with('bool')->with([
    WithCharsInterface::class,
    WithImagesInterface::class,
    WithImageToVideoInterface::class,
    WithWordsInterface::class,
    WithSpeechToTextInterface::class,
    WithTextToSpeechInterface::class,
    WithVisionPreviewInterface::class,
    WithTextToVideoInterface::class,
    WithPlagiarismInterface::class,
]);

test('default schema of user credits', function () {
    $enginesWithCredits = User::getFreshCredits();
    expect($enginesWithCredits)
        ->toHaveCount(count(EngineEnum::cases())) // Ensure first-level count only
        ->each(function ($entities) {
            $entities->each(function ($entity) {
                $entity
                    ->toBeArray()                     // Assert that $entity is an array
                    ->toHaveKeys(['credit', 'isUnlimited'])  // Check that it has the expected keys
                    ->toHaveKey('credit', 0)                 // Check that 'credit' is 0
                    ->toHaveKey('isUnlimited', false);       // Check that 'isUnlimited' is false
            });
        });

});

test('default schema from user credits', function () {

    $creditModel = User::factory()->create([
        'entity_credits' => null,
    ]);

    expect($creditModel->entity_credits)
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->toHaveCount(count(EngineEnum::cases()));

    assertExpectedCreditsSize($creditModel->entity_credits, $this->fullCount);
});

test('updated schema with missing models from user credits', function () {

    $creditModel = User::factory()->create();
    $enginesFirstLevelCount = count(EngineEnum::cases());
    $listableCasesCount = count(EntityEnum::listableCases());

    $fullCount = $enginesFirstLevelCount + $listableCasesCount;

    expect($creditModel->entity_credits)
        ->toHaveCount($enginesFirstLevelCount)
        ->toHaveKey(EntityEnum::GPT_4_O->engine()->slug());

    assertExpectedCreditsSize($creditModel->entity_credits, $fullCount);

    // retrun array not collection
    $enginesWithEntitiesWithoutGPT40Entity = collect($creditModel->entity_credits)
        ->map(function ($entities, $engine) {
            if ($engine === EntityEnum::GPT_4_O->engine()->slug()) {
                return collect($entities)
                    ->filter(function ($entity, $key) {
                        return $key !== EntityEnum::GPT_4_O->slug();
                    });
            }

            return $entities;
        })->toArray();

    expect($enginesWithEntitiesWithoutGPT40Entity)
        ->toHaveCount($enginesFirstLevelCount)
        ->toHaveKey(EntityEnum::GPT_4_O->engine()->slug())
        ->and($enginesWithEntitiesWithoutGPT40Entity[EntityEnum::GPT_4_O->engine()->slug()])->not->toHaveKey(EntityEnum::GPT_4_O->slug());

    assertExpectedCreditsSize($enginesWithEntitiesWithoutGPT40Entity, $fullCount - 1);

    $creditModel->update([
        'entity_credits' => $enginesWithEntitiesWithoutGPT40Entity,
    ]);

    // refresh the model
    $newModelCredits = $creditModel->refresh()->entity_credits;

    expect($newModelCredits)
        ->toHaveCount($enginesFirstLevelCount)
        ->toHaveKey(EntityEnum::GPT_4_O->engine()->slug());

    assertExpectedCreditsSize($newModelCredits, $fullCount);
});

test('total default models credits for specific user', function ($increase, $type) {

    $user = User::factory()->create();

    $this->seed([
        EntitySeeder::class,
        EngineSeeder::class,
    ]);

    $stats = EntityStats::type($type)
        ->includeDisabled(true)
        ->includeUnlisted(! $increase)
        ->forUser($user);

    $stats->list()
        ->each(function (WithCreditInterface $entity) use ($increase, $user) {

            $this->assertEquals($user->id, $entity->getUser()->id);

            if ($increase) {
                $entity->increaseCredit(1.5);
            } else {
                $entity->setCredit(1.5);
            }
        });

    expect($stats->totalCredits())->toBe(mockDefaultModelsCount($type) * 1.5);

})->with('bool')->with([
    WithImagesInterface::class,
    WithImageToVideoInterface::class,
    WithWordsInterface::class,
    WithSpeechToTextInterface::class,
    WithTextToSpeechInterface::class,
    WithVisionPreviewInterface::class,
    WithTextToVideoInterface::class,
    WithPlagiarismInterface::class,
]);

test('set all image drivers to unlimited', function () {
    $user = loginAsUser();

    $this->seed([
        EntitySeeder::class,
        EngineSeeder::class,
    ]);

    $stats = EntityStats::image()
        ->includeDisabled(true)
        ->includeUnlisted(true);

    $stats->list()
        ->each(function ($entity) {
            $entity->setAsUnlimited();
        });

    $stats->list()
        ->each(function ($entity) {
            $defaultModels = $entity->engine()->getDefaultModels(Setting::getCache(), SettingTwo::getCache());
            if (in_array($entity->enum(), $defaultModels, true)) {
                $this->assertTrue($entity->isUnlimitedCredit());
            } else {
                $this->assertFalse($entity->isUnlimitedCredit());
            }
        });

});

test('set all image drivers to limited', function () {
    $user = loginAsUser();

    $this->seed([
        EntitySeeder::class,
        EngineSeeder::class,
    ]);

    $stats = EntityStats::image()
        ->includeDisabled(true)
        ->includeUnlisted(true);

    $stats->list()
        ->each(function ($entity) {
            $entity->setCredit(1.5);
            $entity->setAsUnlimited(false);
        });

    $stats->list()
        ->each(function ($entity) {
            $this->assertFalse($entity->isUnlimitedCredit());
        });

});

test('set all word drivers to unlimited', function () {
    $user = loginAsUser();

    $this->seed([
        EntitySeeder::class,
        EngineSeeder::class,
    ]);

    $stats = EntityStats::word()
        ->includeDisabled(true)
        ->includeUnlisted(true);

    $stats->list()
        ->each(function ($entity) {
            $entity->setAsUnlimited();
        });

    $stats->list()
        ->each(function ($entity) {
            $defaultModels = $entity->engine()->getDefaultModels(Setting::getCache(), SettingTwo::getCache());
            if (in_array($entity->enum(), $defaultModels, true)) {
                $this->assertTrue($entity->isUnlimitedCredit());
            } else {
                $this->assertFalse($entity->isUnlimitedCredit());
            }
        });

});

test('set all word drivers to limited', function () {
    $user = loginAsUser();

    $this->seed([
        EntitySeeder::class,
        EngineSeeder::class,
    ]);

    $stats = EntityStats::word()
        ->includeDisabled(true)
        ->includeUnlisted(true);

    $stats->list()
        ->each(function ($entity) {
            $entity->setCredit(1.5);
            $entity->setAsUnlimited(false);
        });

    $stats->list()
        ->each(function ($entity) {
            $this->assertFalse($entity->isUnlimitedCredit());
        });

});
