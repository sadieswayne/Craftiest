<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts;

use App\Enums\StatusEnum;

interface WithStatus
{
    public function status(): ?StatusEnum;

    public function isEnabled(): bool;

    public function updateStatus(bool $status): bool;
}
