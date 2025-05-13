<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Input;

use RuntimeException;

trait HasInputImage
{
    protected int $inputImageCount;

    public function getInputImageCount(): int
    {
        if (! isset($this->inputImageCount)) {
            throw new RuntimeException('Input image count is not provided');
        }

        return $this->inputImageCount;
    }

    public function inputImageCount(int $inputImageCount): static
    {
        $this->inputImageCount = $inputImageCount;

        return $this;
    }
}
