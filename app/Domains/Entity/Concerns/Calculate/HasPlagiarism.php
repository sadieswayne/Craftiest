<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Calculate;

use Exception;

trait HasPlagiarism
{
    /**
     * @throws Exception
     */
    public function calculate(): float
    {
        $wordCount = count(preg_split('/\PL+/u', $this->getInput(), -1, PREG_SPLIT_NO_EMPTY));

        return $wordCount * $this->getCreditIndex();
    }
}
