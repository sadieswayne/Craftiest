<?php

declare(strict_types=1);

namespace App\Enums\Contracts;

/**
 * @mixin \StringBackedEnum
 *
 * @property string $value
 * @property string $name
 */
interface WithStringBackedEnum
{
    public function label(): string;

    public static function getLabel(WithStringBackedEnum $enum): string;

    public static function getLabels(?string $implode = null): array|string;
}
