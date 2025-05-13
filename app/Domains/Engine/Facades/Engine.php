<?php

declare(strict_types=1);

namespace App\Domains\Engine\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Domains\Engine\Engine
 * @mixin \App\Domains\Engine\BaseDriver
 */
class Engine extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ai.engine';
    }
}
