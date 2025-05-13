<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum Storage: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case PUBLIC = 'public';

    case S3 = 's3';

    case R2 = 'r2';

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC   => __('Public'),
            self::S3       => __('AWS S3'),
            self::R2       => __('Cloudflare R2'),
        };
    }
}
