<?php

declare(strict_types=1);

namespace App\Domains\Entity\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Domains\Entity\EntityManager
 * @mixin \App\Domains\Entity\BaseDriver
 */
class Entity extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ai.entity';
    }
}
