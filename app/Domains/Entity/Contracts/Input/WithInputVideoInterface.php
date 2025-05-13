<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts\Input;

interface WithInputVideoInterface
{
    public function getInputVideoCount(): int;

    public function inputVideoCount(int $inputVideoCount): static;
}
