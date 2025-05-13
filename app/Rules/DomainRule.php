<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DomainRule implements ValidationRule
{
    public function __construct(public bool $allowWildcard = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === '*' && $this->allowWildcard) {
            return;
        }

        if (! (bool) preg_match('/^(?:[a-z0-9](?:[a-z0-9-æøå]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/iu', $value)) {
            $fail('The :attribute is not a valid domain.');
        }
    }
}
