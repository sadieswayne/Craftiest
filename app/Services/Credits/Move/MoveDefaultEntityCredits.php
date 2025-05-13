<?php

declare(strict_types=1);

namespace App\Services\Credits\Move;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Models\Plan;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

trait MoveDefaultEntityCredits
{
    public ?EntityEnum $oldEntity = null;

    public ?EntityEnum $newEntity = null;

    public function setOldEntity(?EntityEnum $oldEntity): static
    {
        $this->oldEntity = $oldEntity;

        return $this;
    }

    public function setNewEntity(?EntityEnum $newEntity): static
    {
        $this->newEntity = $newEntity;

        return $this;
    }

    public function moveDefaultEntityCreditsForUsers(): void
    {
        $users = User::query()->get();
        if ($this->oldEntity === null || $this->newEntity === null) {
            Log::error('Old or New Entity is not set');
        }

        foreach ($users as $user) {
            try {
                $oldModelDriver = clone Entity::driver($this->oldEntity)->forUser($user);
                $newModelDriver = clone Entity::driver($this->newEntity)->forUser($user);

                if ($oldModelDriver->isUnlimitedCredit()) {
                    $oldModelDriver->setAsUnlimited(false);
                    $newModelDriver->setAsUnlimited();
                }

                $oldModelCredits = $oldModelDriver->creditBalance();
                if ($oldModelCredits > 0) {
                    $newModelDriver->increaseCredit($oldModelCredits);
                    $oldModelDriver->setCredit(0);
                }

            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function moveDefaultEntityCreditsForPlans(): void
    {
        $plans = Plan::query()->get();
        if ($this->oldEntity === null || $this->newEntity === null) {
            Log::error('Old or New Entity is not set');
        }

        foreach ($plans as $plan) {
            try {
                $tmp = $plan->ai_models;
                $oldModelCredits = $tmp[$this->oldEntity->engine()->slug()][$this->oldEntity->slug()];
                $newModelCredits = $tmp[$this->newEntity->engine()->slug()][$this->newEntity->slug()];

                if ($oldModelCredits['isUnlimited']) {
                    $oldModelCredits['isUnlimited'] = false;
                    $newModelCredits['isUnlimited'] = true;
                }

                if ($oldModelCredits['credit'] > 0) {
                    $newModelCredits['credit'] += $oldModelCredits['credit'];
                    $oldModelCredits['credit'] = 0;
                }

                $tmp[$this->newEntity->engine()->slug()][$this->newEntity->slug()] = $newModelCredits;
                $tmp[$this->oldEntity->engine()->slug()][$this->oldEntity->slug()] = $oldModelCredits;

                $plan->ai_models = $tmp;
                $plan->save();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
