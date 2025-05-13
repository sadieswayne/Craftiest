<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Input;

use RuntimeException;

trait HasInput
{
    protected string $input;

    public function getInput(): string
    {
        if (! isset($this->input)) {
            throw new RuntimeException('Input is not provided');
        }

        return $this->input;
    }

    public function input(string $input): static
    {
        $this->input = $input;

        return $this;
    }
}
