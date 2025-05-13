<?php

namespace App\Enums\Plan;

use App\Enums\Contracts\WithStringBackedEnum;
use App\Enums\Traits\EnumTo;
use App\Enums\Traits\SluggableEnumTrait;
use App\Enums\Traits\StringBackedEnumTrait;

enum TypeEnum: string implements WithStringBackedEnum
{
    use EnumTo;
    use SluggableEnumTrait;
    use StringBackedEnumTrait;
    use StringBackedEnumTrait;

    case SUBSCRIPTION = 'subscription';
    case TOKEN_PACK = 'prepaid';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => __('Subscription'),
            self::TOKEN_PACK   => __('Token Pack')
        };
    }
}
