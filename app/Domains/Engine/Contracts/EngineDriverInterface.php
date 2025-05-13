<?php

declare(strict_types=1);

namespace App\Domains\Engine\Contracts;

use App\Domains\Engine\Enums\EngineEnum;

interface EngineDriverInterface
{
    public function enum(): EngineEnum;

    public function label(): string;

    public function name(): string;
}
