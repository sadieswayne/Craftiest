<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum Roles: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case USER = 'user';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::USER           => __('User'),
            self::ADMIN          => __('Admin'),
            self::SUPER_ADMIN    => __('Super Admin'),
        };
    }
}
