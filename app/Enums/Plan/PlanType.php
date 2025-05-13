<?php

declare(strict_types=1);

namespace App\Enums\Plan;

use App\Enums\Contracts\WithStringBackedEnum;
use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum PlanType: string implements WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case ALL = 'all';
    case PREMIUM = 'premium';
    case REGULAR = 'regular';

    public function label(): string
    {
        return match ($this) {
            self::ALL     => __('All'),
            self::PREMIUM => __('Premium'),
            self::REGULAR => __('Regular')
        };
    }

    public static function getValues(): array
    {
        return array_map(fn ($value) => $value->value, self::cases());
    }
}
