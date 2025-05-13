<?php

declare(strict_types=1);

namespace App\Enums\Traits;

use App\Enums\Contracts\WithStringBackedEnum;
use Illuminate\Support\Arr;

trait StringBackedEnumTrait
{
    public function label(): string
    {
        return $this->value;
    }

    public static function getLabel(WithStringBackedEnum $enum): string
    {
        return $enum->label();
    }

    /**
     * @template T of string|null
     * @phpstan-param T $implode
     *
     * @return (T is null ? array : string)
     */
    public static function getLabels(?string $implode = null): array|string
    {
        $labels = Arr::map(self::cases(), static fn ($enum) => $enum->label());

        if (! is_null($implode)) {
            return implode($implode, $labels);
        }

        return $labels;
    }
}
