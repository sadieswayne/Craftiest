<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts\Input;

interface WithInputImageInterface
{
    public function getInputImageCount(): int;

    public function inputImageCount(int $inputImageCount): static;
}
