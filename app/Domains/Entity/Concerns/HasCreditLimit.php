<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns;

use App\Domains\Entity\Enums\EntityEnum;
use App\Enums\MagicResponse;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

trait HasCreditLimit
{
    protected ?float $calculatedInputCredit = null;

    public function creditEnum(): EntityEnum
    {
        return $this->enum()->creditBy();
    }

    protected function getPlanWithCredit(): ?Plan
    {
        $this->ensurePlanProvided();

        return $this->plan;
    }

    protected function getUserWithCredit(): null|User|Authenticatable
    {
        $this->ensureUserProvided();

        return $this->getUser();
    }

    public function getCredit(): array
    {
        return ($this->plan?->exists ? $this->getPlanWithCredit() : $this->getUserWithCredit())?->getCredit($this->engine()->slug(), $this->creditKey());
    }

    /**
     * @throws Exception
     */
    public function creditBalance(): float
    {
        return once(function () {
            return $this->getCreditBalance();
        });
    }

    /**
     * @throws Exception
     */
    public function isUnlimitedCredit(): bool
    {
        return once(function () {
            return $this->getIsUnlimitedCredit();
        });
    }

    public function getCreditBalance(): float
    {
        $credit = $this->getCredit()['credit'];

        if (is_string($credit)) {
            $credit = (float) $credit;
        }

        $aiFinances = app('ai_chat_model_plan');

        $engineDefaultModels = $this->engine()->getDefaultModels(Setting::getCache(), SettingTwo::getCache());
        $model = $this->model();
        if ($model && ! $model->is_selected &&
            ! in_array($model->key, $engineDefaultModels, true) &&
            ! in_array($model->id, $aiFinances, true)) {
            return 0;
        }

        return $credit;
    }

    /**
     * @throws Exception
     */
    public function getIsUnlimitedCredit(): bool
    {
        $aiFinances = app('ai_chat_model_plan');

        $engineDefaultModels = $this->engine()->getDefaultModels(Setting::getCache(), SettingTwo::getCache());
        $model = $this->model();
        if ($model && ! $model->is_selected &&
            ! in_array($model->key, $engineDefaultModels, true) &&
            ! in_array($model->id, $aiFinances, true)) {
            return false;
        }

        return $this->getCredit()['isUnlimited'];
    }

    /**
     * @throws Exception
     */
    public function hasCreditBalance(): bool
    {
        return $this->creditBalance() > 0 || $this->isUnlimitedCredit();
    }

    /**
     * @throws Exception
     */
    public function redirectIfNoCreditBalance(): void
    {
        if ($this->hasCreditBalance()) {
            return;
        }

        MagicResponse::NO_CREDITS_LEFT->exception();
    }

    /**
     * @throws Exception
     */
    public function setCredit(float $value = 1.00): bool
    {
        return $this->updateUserCredit($value, function ($creditBalance, $credit) {
            return $credit;
        }, skipCalculatedCredit: true);
    }

    /**
     * @throws Exception
     */
    public function setDefaultCreditForDemo(): bool
    {
        if ($this->getUserWithCredit()?->isAdmin()) {
            $this->setAsUnlimited();
        }

        return $this->setCredit($this->creditEnum()->defaultCreditForDemo());
    }

    public function setAsUnlimited(bool $unlimited = true): bool
    {
        $user = $this->getUserWithCredit();
        $creditKey = $this->creditKey();
        $engineKey = $this->engine()->slug();

        $creditsArr = $user?->entity_credits;

        $creditsArr[$engineKey][$creditKey] = [
            'credit'              => $creditsArr[$engineKey][$creditKey]['credit'] ?? 0.0,
            'isUnlimited'         => $unlimited,
        ];

        return $user?->update([
            'entity_credits' => $creditsArr,
        ]);
    }

    /**
     * @throws Exception
     */
    public function increaseCredit(float $value = 1.00): bool
    {
        return $this->updateUserCredit($value, function ($creditBalance, $credit) {
            return $creditBalance + $credit;
        });
    }

    /**
     * @throws Exception
     */
    public function decreaseCredit(float $value = 1.00): bool
    {
        if ($this->isUnlimitedCredit()) {
            return true;
        }

        return $this->updateUserCredit($value, function ($creditBalance, $credit) {
            return max(0, $creditBalance - $credit);
        });
    }

    /**
     * @throws Exception
     */
    private function updateUserCredit(float $value, Closure $callback, bool $skipCalculatedCredit = false): bool
    {
        $user = $this->getUserWithCredit();

        if ($skipCalculatedCredit) {
            $credit = $value;
        } else {
            $credit = $this->calculatedInputCredit ?: $value;
        }

        $creditKey = $this->creditKey();

        $engineKey = $this->engine()->slug();

        $creditsArr = $user?->entity_credits ?? User::getFreshCredits();

        $creditsArr[$engineKey][$creditKey] = [
            'credit'      => $callback($this->creditBalance(), $credit),
            'isUnlimited' => $creditsArr[$engineKey][$creditKey]['isUnlimited'],
        ];

        return $user?->update([
            'entity_credits' => $creditsArr,
        ]);
    }

    public function getCreditIndex(): float
    {
        return $this->creditEnum()->creditIndex();
    }

    public function getCalculatedInputCredit(): float
    {
        return $this->calculatedInputCredit;
    }

    /**
     * @throws Exception
     */
    public function hasCreditBalanceForInput(): bool
    {
        if ($this->isUnlimitedCredit()) {
            return true;
        }

        return $this->creditBalance() > $this->getCalculatedInputCredit();
    }

    public function setCalculatedInputCredit($value = 0.0): static
    {
        $this->calculatedInputCredit = $value;

        return $this;
    }
}
