<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\Llama;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasWords;
use App\Domains\Entity\Concerns\Input\HasInput;
use App\Domains\Entity\Contracts\Calculate\WithWordsInterface;
use App\Domains\Entity\Contracts\Input\WithInputInterface;
use App\Domains\Entity\Enums\EntityEnum;

class LlamaAIDriver extends BaseDriver implements WithInputInterface, WithWordsInterface
{
    use HasInput;
    use HasWords;

    public function enum(): EntityEnum
    {
        return EntityEnum::META_LLAMA_31_405B_INSTRUCT;
    }
}
