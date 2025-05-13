<?php

declare(strict_types=1);

namespace App\Enums\Traits;

use App\Enums\Contracts\WithIntBackedEnum;
use Illuminate\Support\Arr;

trait IntBackedEnumTrait
{
    public static function fromName(string $name): static
    {
        $cases = collect(self::cases())->mapWithKeys(fn ($case) => [$case->name => $case]);

        return $cases->get($name);
    }

    public function label(): string
    {
        return $this->name;
    }

    public static function getLabel(WithIntBackedEnum $enum): string
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
