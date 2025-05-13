<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts;

use App\Domains\Entity\Enums\EntityEnum;

interface WithCreditInterface
{
    public function creditBalance(): float;

    public function hasCreditBalance(): bool;

    public function setCredit(float $value = 1.00): bool;

    public function increaseCredit(float $value = 1.00);

    public function decreaseCredit(float $value = 1.00);

    public function getCreditIndex(): float;

    public function getCalculatedInputCredit(): float;

    public function hasCreditBalanceForInput(): bool;

    public function setCalculatedInputCredit(): static;

    public function creditEnum(): EntityEnum;

    public function getCredit(): array;

    public function redirectIfNoCreditBalance(): void;

    public function setAsUnlimited(bool $unlimited = true): bool;

    public function isUnlimitedCredit(): bool;

    public function setDefaultCreditForDemo(): bool;
}
