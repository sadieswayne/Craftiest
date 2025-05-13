<?php

declare(strict_types=1);

namespace App\Domains\Engine\Drivers;

use App\Domains\Engine\BaseDriver;
use App\Domains\Engine\Enums\EngineEnum;

class PebblelyEngineDriver extends BaseDriver
{
    public function enum(): EngineEnum
    {
        return EngineEnum::PEBBLELY;
    }
}
