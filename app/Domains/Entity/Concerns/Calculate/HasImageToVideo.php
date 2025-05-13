<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Calculate;

trait HasImageToVideo
{
    public function calculate(): float
    {
        $videoCount = $this->getInputVideoCount();

        return $videoCount * $this->getCreditIndex();
    }
}
