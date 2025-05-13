<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Models\SettingTwo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->fullCount = count(EngineEnum::cases()) + count(EntityEnum::listableCases());
});

test('transfer image and word limits for all models', function () {

    $migrationFilename = '2024_09_24_214000_update_plan_credits_temporary_migration';

    $this->assertTrue(File::exists(database_path('migrations/' . $migrationFilename . '.php')));

    $this->assertSame(0, User::count());

    $user = User::factory()->create([
        'remaining_words'  => 100,
        'remaining_images' => 150,
    ]);

    $this->assertSame(1, User::count());

    $this->assertNotNull($user);

    $migrationQuery = DB::table('migrations')->where('migration', $migrationFilename);

    $this->assertTrue($migrationQuery->exists());

    $this->assertSame(1, $migrationQuery->delete());

    $this->assertFalse($migrationQuery->exists());

    SettingTwo::create([
        'stablediffusion_default_model' => EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0,
        'dalle'                         => EntityEnum::DALL_E_3,
    ]);

    setting(['fal_ai_default_model' => EntityEnum::FLUX_REALISM->slug()])->save();

    $this->artisan('migrate', ['--path' => 'database/migrations/' . $migrationFilename . '.php']);

    $expectedCredits = User::getFreshCredits();

    $expectedCredits[EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->engine()->slug()][EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug()] = [
        'credit'      => 50,
        'isUnlimited' => false,
    ];
    $expectedCredits[EntityEnum::DALL_E_3->engine()->slug()][EntityEnum::DALL_E_3->slug()] = [
        'credit'      => 50,
        'isUnlimited' => false,
    ];
    $expectedCredits[EntityEnum::FLUX_REALISM->engine()->slug()][EntityEnum::FLUX_REALISM->slug()] = [
        'credit'      => 50,
        'isUnlimited' => false,
    ];
    $expectedCredits[EntityEnum::GPT_4_O->engine()->slug()][EntityEnum::GPT_4_O->slug()] = [
        'credit'      => 100,
        'isUnlimited' => false,
    ];

    expect($user->refresh())
        ->entity_credits
        ->toBeArray()
        ->toHaveCount(count(EngineEnum::cases())) // 1st level engines count
        ->toEqualCanonicalizing($expectedCredits);

    assertExpectedCreditsSize($expectedCredits, $this->fullCount);
});

test('transfer word limit for all models', function () {

    $migrationFilename = '2024_09_24_214000_update_plan_credits_temporary_migration';

    $this->assertTrue(File::exists(database_path('migrations/' . $migrationFilename . '.php')));

    $this->assertSame(0, User::count());

    $user = User::factory()->create([
        'remaining_words'  => 100,
        'remaining_images' => 150,
    ]);

    $this->assertSame(1, User::count());

    $this->assertNotNull($user);

    $migrationQuery = DB::table('migrations')->where('migration', $migrationFilename);

    $this->assertTrue($migrationQuery->exists());

    $this->assertSame(1, $migrationQuery->delete());

    $this->assertFalse($migrationQuery->exists());

    SettingTwo::create([
        'stablediffusion_default_model' => EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug(),
        'dalle'                         => EntityEnum::DALL_E_2->slug(),
    ]);

    setting(['fal_ai_default_model' => EntityEnum::FLUX_REALISM->slug()])->save();

    $this->artisan('migrate', ['--path' => 'database/migrations/' . $migrationFilename . '.php']);

    $expectedCredits = User::getFreshCredits();
    $expectedCredits[EntityEnum::GPT_4_O->engine()->slug()][EntityEnum::GPT_4_O->slug()] = [
        'credit'      => 100,
        'isUnlimited' => false,
    ];
    $expectedCredits[EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->engine()->slug()][EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug()] = [
        'credit'      => 50,
        'isUnlimited' => false,
    ];
    $expectedCredits[EntityEnum::DALL_E_2->engine()->slug()][EntityEnum::DALL_E_2->slug()] = [
        'credit'      => 50,
        'isUnlimited' => false,
    ];
    $expectedCredits[EntityEnum::FLUX_REALISM->engine()->slug()][EntityEnum::FLUX_REALISM->slug()] = [
        'credit'      => 50,
        'isUnlimited' => false,
    ];

    expect($user->refresh())
        ->entity_credits
        ->not->toBeNull()
        ->entity_credits
        ->toBeArray()
        ->toHaveCount(count(EngineEnum::cases()))
        ->toEqualCanonicalizing($expectedCredits);

    assertExpectedCreditsSize($expectedCredits, $this->fullCount);
});
