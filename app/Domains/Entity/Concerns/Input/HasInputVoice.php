<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Input;

use RuntimeException;

trait HasInputVoice
{
    protected int $inputVoiceCount;

    public function getInputVoiceCount(): int
    {
        if (! isset($this->inputVoiceCount)) {
            throw new RuntimeException('Input video count is not provided');
        }

        return $this->inputVoiceCount;
    }

    public function inputVoiceCount(int $inputVoiceCount): static
    {
        $this->inputVoiceCount = $inputVoiceCount;

        return $this;
    }
}
