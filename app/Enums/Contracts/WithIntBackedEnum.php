<?php

declare(strict_types=1);

namespace App\Enums\Contracts;

/**
 * @mixin \IntBackedEnum
 *
 * @property int $value
 * @property string $name
 */
interface WithIntBackedEnum
{
    public function label(): string;

    public static function getLabel(WithIntBackedEnum $enum): string;

    /**
     * @template T of string|null
     * @phpstan-param T $implode
     *
     * @return (T is null ? array : string)
     */
    public static function getLabels(?string $implode = null): array|string;
}
