<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Calculate;

trait HasImages
{
    public function calculate(): float
    {
        $imageCount = $this->getInputImageCount();

        return $imageCount * $this->getCreditIndex();
    }
}
