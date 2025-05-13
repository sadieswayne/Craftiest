<?php

declare(strict_types=1);

namespace App\Enums\Plan;

use App\Enums\Contracts\WithStringBackedEnum;
use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum FrequencyEnum: string implements WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
    case LIFETIME_MONTHLY = 'lifetime_monthly';
    case LIFETIME_YEARLY = 'lifetime_yearly';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY          => __('Monthly'),
            self::YEARLY           => __('Yearly'),
            self::LIFETIME_MONTHLY => __('Lifetime - Monthly Renewal'),
            self::LIFETIME_YEARLY  => __('Lifetime - Yearly Renewal')
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MONTHLY          => '#06D4404D',
            self::YEARLY           => '#8185F44D',
            self::LIFETIME_MONTHLY => '#74DB84',
            self::LIFETIME_YEARLY  => '#42f5b0',
        };
    }
}
