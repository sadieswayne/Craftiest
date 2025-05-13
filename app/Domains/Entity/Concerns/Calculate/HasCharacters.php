<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Calculate;

use Illuminate\Support\Str;

trait HasCharacters
{
    public function calculate(): float
    {
        $charCount = Str::of($this->getInput())->length();

        return $charCount * $this->getCreditIndex();
    }
}
