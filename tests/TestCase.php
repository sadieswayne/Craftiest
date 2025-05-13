<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Once\Cache as OnceCache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // protected bool $seed = true;
    //
    // protected string $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        OnceCache::getInstance()->disable();

    }
}
