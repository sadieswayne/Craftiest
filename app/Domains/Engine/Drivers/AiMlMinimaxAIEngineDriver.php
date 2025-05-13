<?php

namespace App\Domains\Engine\Drivers;

use App\Domains\Engine\BaseDriver;
use App\Domains\Engine\Enums\EngineEnum;

class AiMlMinimaxAIEngineDriver extends BaseDriver
{
    public function enum(): EngineEnum
    {
        return EngineEnum::AI_ML_MINIMAX;
    }
}
