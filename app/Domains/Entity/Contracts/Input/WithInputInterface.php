<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts\Input;

interface WithInputInterface
{
    public function getInput(): string;

    public function input(string $input): static;
}
