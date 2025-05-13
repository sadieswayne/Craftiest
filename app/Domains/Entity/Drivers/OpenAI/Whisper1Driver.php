<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\OpenAI;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasSpeechToText;
use App\Domains\Entity\Concerns\Input\HasInput;
use App\Domains\Entity\Contracts\Calculate\WithSpeechToTextInterface;
use App\Domains\Entity\Contracts\Input\WithInputInterface;
use App\Domains\Entity\Enums\EntityEnum;

class Whisper1Driver extends BaseDriver implements WithInputInterface, WithSpeechToTextInterface
{
    use HasInput;
    use HasSpeechToText;

    public function enum(): EntityEnum
    {
        return EntityEnum::WHISPER_1;
    }
}
