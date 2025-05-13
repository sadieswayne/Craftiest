<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum StatusEnum: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case ENABLED = 'enabled';

    case DISABLED = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::ENABLED  => __('Enabled'),
            self::DISABLED => __('Disabled'),
        };
    }
}
