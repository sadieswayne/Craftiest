<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts;

use App\Domains\Engine\Contracts\EngineDriverInterface;
use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;

interface EntityDriverInterface
{
    public function label(): string;

    public function name(): string;

    public function creditKey(): string;

    public function enum(): EntityEnum;

    public function calculateCredit(): static;

    public function engineDriver(): EngineDriverInterface;

    public function engine(): EngineEnum;
}
