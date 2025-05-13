<?php

declare(strict_types=1);

namespace App\Domains\Entity;

use App\Domains\Engine\Contracts\EngineDriverInterface;
use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Facades\Engine;
use App\Domains\Entity\Concerns\HasCreditLimit;
use App\Domains\Entity\Concerns\HasModel;
use App\Domains\Entity\Contracts\Calculate\WithCalculate;
use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Contracts\WithCreditInterface;
use App\Domains\Entity\Contracts\WithModel;
use App\Models\Plan;
use App\Models\User;
use RuntimeException;

abstract class BaseDriver implements EntityDriverInterface, WithCalculate, WithCreditInterface, WithModel
{
    use HasCreditLimit;
    use HasModel;

    private ?User $user;

    private ?Plan $plan = null;

    private ?int $lastUsedUserId = 0;

    public function engine(): EngineEnum
    {
        return $this->enum()->engine();
    }

    public function engineDriver(): EngineDriverInterface
    {
        return Engine::driver($this->enum()->engine());
    }

    protected function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function ensureUserProvided(): void
    {
        if (! isset($this->user)) {
            throw new RuntimeException('User is not provided');
        }
    }

    public function ensurePlanProvided(): void
    {
        if (! isset($this->plan)) {
            throw new RuntimeException('Plan is not provided');
        }
    }

    private function setLastUsedUserId(?int $id): static
    {
        $this->lastUsedUserId = $id;

        return $this;
    }

    public function forUser(null|int|User $user): static
    {
        $userModel = is_int($user) ? User::findOrFail($user) : $user;

        return $this->setUser($userModel)->createDriverReqsIfNeeded(false);
    }

    public function forPlan(?Plan $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    protected function forCurrentUser(): static
    {
        $user = auth()->user();

        if (! $user) {
            throw new RuntimeException('User is not authenticated');
        }

        return $this->forUser($user);
    }

    protected function createDriverUserReqsIfNeeded(bool $forCurrentUser = true): static
    {
        if ($forCurrentUser) {
            $userId = $this->forCurrentUser()->getUser()->id;
        } else {
            $userId = $this->getUser()?->id;
        }

        if ($this->lastUsedUserId !== $userId) {
            $this->createDriverUserReqs($userId);
        }

        return $this;
    }

    public function createDriverReqsIfNeeded(bool $forCurrentUser = true): static
    {
        $self = $this->createDriverReqs();

        if (! $forCurrentUser || auth()->check()) {
            $self->createDriverUserReqsIfNeeded($forCurrentUser);
        }

        return $self;
    }

    private function createDriverReqs(): static
    {
        return $this;
    }

    private function createDriverUserReqs(?int $userId): static
    {
        $this->setLastUsedUserId($userId);

        return $this;
    }

    public function label(): string
    {
        return $this->enum()->label();
    }

    public function name(): string
    {
        return $this->enum()->value;
    }

    public function creditKey(): string
    {
        return $this->creditEnum()->slug();
    }

    public function calculateCredit(): static
    {
        $this->ensureUserProvided();

        $this->calculatedInputCredit = $this->calculate();

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
