<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns;

use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Enums\StatusEnum;

trait HasStatus
{
    public function status(): ?StatusEnum
    {
        return $this->model()?->status;
    }

    public function isEnabled(): bool
    {
        if ($this instanceof EntityDriverInterface && ! $this->engineDriver()->isEnabled()) {
            return false;
        }

        return $this->status() === StatusEnum::ENABLED;
    }

    public function updateStatus(bool $status): bool
    {
        return $this->model()->update([
            'status' => $status ? StatusEnum::ENABLED : StatusEnum::DISABLED,
        ]);
    }
}
