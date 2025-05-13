<?php

namespace App\Domains\Engine\Drivers;

use App\Domains\Engine\BaseDriver;
use App\Domains\Engine\Enums\EngineEnum;

class TavusAIEngineDriver extends BaseDriver
{
    public function enum(): EngineEnum
    {
        return EngineEnum::TAVUS_AI;
    }
}
