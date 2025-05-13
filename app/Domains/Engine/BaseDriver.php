<?php

declare(strict_types=1);

namespace App\Domains\Engine;

use App\Domains\Engine\Concerns\HasModel;
use App\Domains\Engine\Contracts\EngineDriverInterface;
use App\Domains\Engine\Contracts\WithModel;

abstract class BaseDriver implements EngineDriverInterface, WithModel
{
    use HasModel;

    public function label(): string
    {
        return $this->enum()->label();
    }

    public function name(): string
    {
        return $this->enum()->value;
    }

    public function createDriverReqsIfNeeded(): static
    {
        return $this;
    }
}
