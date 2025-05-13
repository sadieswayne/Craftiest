<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts\Input;

interface WithInputVoiceInterface
{
    public function getInputVoiceCount(): int;

    public function inputVoiceCount(int $inputVoiceCount): static;
}
