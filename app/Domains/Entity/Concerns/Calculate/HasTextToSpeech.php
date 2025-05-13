<?php

declare(strict_types=1);

namespace App\Domains\Entity\Concerns\Calculate;

trait HasTextToSpeech
{
    public function calculate(): float
    {
        $voiceCount = $this->getInputVoiceCount();

        return $voiceCount * $this->getCreditIndex();
    }
}
