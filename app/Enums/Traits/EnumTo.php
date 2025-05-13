<?php

declare(strict_types=1);

namespace App\Enums\Traits;

use App\Enums\Contracts\WithIntBackedEnum;
use App\Enums\Contracts\WithStringBackedEnum;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;
use UnitEnum;

trait EnumTo
{
    public static function toArray(): array
    {
        return array_column(static::cases(), 'value');
    }

    public static function toOptions(null|string|int|WithIntBackedEnum|WithStringBackedEnum $selectedValue = '@none@', array $attributes = [], string $separator = PHP_EOL): string
    {
        if ($selectedValue instanceof UnitEnum) {
            $selectedValue = $selectedValue->value;
        }

        return collect(self::cases())->map(function ($enum) use ($attributes, $selectedValue) {

            $defaultAttrs = [
                'value' => $enum->value,
            ];

            if ($selectedValue !== '@none@' && $enum->value === $selectedValue) {
                $defaultAttrs['selected'] = true;
            }

            $processedAttributes = self::processAttributes($attributes, $enum);

            return sprintf(
                '<option %s>%s</option>',
                self::buildAttributes($defaultAttrs, $processedAttributes),
                $enum->label()
            );
        })->implode($separator);
    }

    private static function processAttributes(array $attributes, $enum): array
    {
        return Arr::map($attributes, static function ($value, $key) use ($enum) {
            return str_replace(
                ['%value%', '%label%'],
                [$enum->value, $enum->label()],
                $value
            );
        });
    }

    private static function buildAttributes(array $defaultAttrs, array $attributes): ComponentAttributeBag
    {
        return (new ComponentAttributeBag($defaultAttrs))->merge($attributes);
    }
}
