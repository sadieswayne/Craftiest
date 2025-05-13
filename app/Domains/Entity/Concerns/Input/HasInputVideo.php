<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Input;

use RuntimeException;

trait HasInputVideo
{
    protected int $inputVideoCount;

    public function getInputVideoCount(): int
    {
        if (! isset($this->inputVideoCount)) {
            throw new RuntimeException('Input video count is not provided');
        }

        return $this->inputVideoCount;
    }

    public function inputVideoCount(int $inputVideoCount): static
    {
        $this->inputVideoCount = $inputVideoCount;

        return $this;
    }
}
