<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class AIModelNotFoundException extends RuntimeException
{
    public static function forModel(string $model): self
    {
        return new self(sprintf('AI model not found: %s', $model));
    }
}
