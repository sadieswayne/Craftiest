<?php

declare(strict_types=1);

namespace App\Domains\Entity;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Contracts\WithCreditInterface;
use App\Domains\Entity\Facades\Entity;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EntityStatItem
{
    private bool $includeDisabled = false;

    private bool $includeUnlisted = false;

    private ?Plan $plan = null;

    private ?EngineEnum $filterByEngine = null;

    public function __construct(private readonly string $type, private ?User $user = null) {}

    public function forUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function forPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    private function getUser(): Authenticatable|User|null
    {
        return $this->user ?? auth()->user();
    }

    public function list(): Collection
    {
        return once(function () {
            return $this->allEntities()
                ->filter(
                    fn (EntityDriverInterface $driver) => $driver instanceof $this->type
                )
                ->includeDisabled($this->includeDisabled)
                ->includeUnlisted($this->includeUnlisted);
        });
    }

    public function totalCredits(): float
    {
        return $this->list()->sum(
            fn (WithCreditInterface $driver) => $driver->forUser($this->getUser())->forPlan($this->plan)->creditBalance()
        );
    }

    public function totalCreditsForGroup(): float
    {
        return $this->list()->sum(
            fn (WithCreditInterface $driver) => $driver->forUser($this->getUser())->forPlan($this->plan)->getCreditBalance()
        );
    }

    public function checkIfThereUnlimited(): bool
    {
        return $this->list()->contains(
            fn (WithCreditInterface $driver) => $driver->forUser($this->getUser())->forPlan($this->plan)->isUnlimitedCredit()
        );
    }

    public function checkIfThereUnlimitedForGroup(): bool
    {
        return $this->list()->contains(
            fn (WithCreditInterface $driver) => $driver->forUser($this->getUser())->forPlan($this->plan)->getIsUnlimitedCredit()
        );
    }

    public function entityCount(): int
    {
        return $this->list()->count();
    }

    public function includeDisabled(bool $condition): static
    {
        $this->includeDisabled = $condition;

        return $this;
    }

    public function includeUnlisted(bool $condition): static
    {
        $this->includeUnlisted = $condition;

        return $this;
    }

    public function filterByEngine(EngineEnum $enum): static
    {
        $this->filterByEngine = $enum;

        return $this;
    }

    private function allEntities(): Collection
    {
        return once(function () {
            if (is_null($this->user) && ! $this->plan?->exists) {
                $this->forUser(Auth::user());
            }

            return Entity::all(filterByEngine: $this->filterByEngine, user: $this->user, plan: $this->plan);
        });
    }
}
