<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\AiMlMinimax;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasTextToSpeech;
use App\Domains\Entity\Concerns\Input\HasInputVoice;
use App\Domains\Entity\Contracts\Calculate\WithTextToSpeechInterface;
use App\Domains\Entity\Contracts\Input\WithInputVoiceInterface;
use App\Domains\Entity\Enums\EntityEnum;

class Music01Driver extends BaseDriver implements WithInputVoiceInterface, WithTextToSpeechInterface
{
    use HasInputVoice;
    use HasTextToSpeech;

    public function enum(): EntityEnum
    {
        return EntityEnum::MUSIC_01;
    }
}
