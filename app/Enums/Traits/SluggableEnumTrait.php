<?php

declare(strict_types=1);

namespace App\Enums\Traits;

use App\Helpers\Classes\EntityRemover;

trait SluggableEnumTrait
{
    public function slug(): string
    {
        return str_replace('.', '__', $this->value);
    }

    public static function fromSlug(string $value): self
    {
        $self = self::tryFrom(str_replace('__', '.', $value));

        if ($self === null) {
            EntityRemover::removeEntity($value);
        }

        return $self;
    }
}
