<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts\Calculate;

interface WithCalculate
{
    public function calculate(): float;
}
