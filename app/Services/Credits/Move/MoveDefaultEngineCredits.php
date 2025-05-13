<?php

declare(strict_types=1);

namespace App\Services\Credits\Move;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Facades\Entity;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

trait MoveDefaultEngineCredits
{
    public ?EngineEnum $oldEngine = null;

    public ?EngineEnum $newEngine = null;

    public ?bool $isAW = false;

    public function setOldEngine(?EngineEnum $oldEngine): static
    {
        $this->oldEngine = $oldEngine;

        return $this;
    }

    public function setNewEngine(?EngineEnum $newEngine): static
    {
        $this->newEngine = $newEngine;

        return $this;
    }

    public function setAW(bool $isAW): static
    {
        $this->isAW = $isAW;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function moveDefaultEngineCreditsForUsers(): void
    {
        $users = User::query()->get();
        if ($this->oldEngine === null || $this->newEngine === null) {
            Log::error('Old or New Engine is not set');
        }

        if ($this->isAW) {
            $settingTwo = SettingTwo::getCache();
            $oldDefaultModel = $this->oldEngine->getDefaultAWImageModel($settingTwo);
            $newDefaultModel = $this->newEngine->getDefaultAWImageModel($settingTwo);
        } else {
            $setting = Setting::getCache();
            $oldDefaultModel = $this->oldEngine->getDefaultWordModel($setting);
            $newDefaultModel = $this->newEngine->getDefaultWordModel($setting);
        }

        foreach ($users as $user) {
            try {
                $oldModelDriver = clone Entity::driver($oldDefaultModel)->forUser($user);
                $newModelDriver = clone Entity::driver($newDefaultModel)->forUser($user);

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
    public function moveDefaultEngineCreditsForPlans(): void
    {
        $plans = Plan::query()->get();
        if ($this->oldEngine === null || $this->newEngine === null) {
            Log::error('Old or New Entity is not set');
        }

        if ($this->isAW) {
            $settingTwo = SettingTwo::getCache();
            $oldEntity = $this->oldEngine->getDefaultAWImageModel($settingTwo);
            $newEntity = $this->newEngine->getDefaultAWImageModel($settingTwo);
        } else {
            $setting = Setting::getCache();
            $oldEntity = $this->oldEngine->getDefaultWordModel($setting);
            $newEntity = $this->newEngine->getDefaultWordModel($setting);
        }

        foreach ($plans as $plan) {
            try {
                $tmp = $plan->ai_models;
                $oldModelCredits = $tmp[$oldEntity->engine()->slug()][$oldEntity->slug()];
                $newModelCredits = $tmp[$newEntity->engine()->slug()][$newEntity->slug()];

                if ($oldModelCredits['isUnlimited']) {
                    $oldModelCredits['isUnlimited'] = false;
                    $newModelCredits['isUnlimited'] = true;
                }

                if ($oldModelCredits['credit'] > 0) {
                    $newModelCredits['credit'] += $oldModelCredits['credit'];
                    $oldModelCredits['credit'] = 0;
                }

                $tmp[$newEntity->engine()->slug()][$newEntity->slug()] = $newModelCredits;
                $tmp[$oldEntity->engine()->slug()][$oldEntity->slug()] = $oldModelCredits;

                $plan->ai_models = $tmp;
                $plan->save();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
