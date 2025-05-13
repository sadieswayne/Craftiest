<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\OpenAI;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasWords;
use App\Domains\Entity\Concerns\Input\HasInput;
use App\Domains\Entity\Contracts\Calculate\WithWordsInterface;
use App\Domains\Entity\Contracts\Input\WithInputInterface;
use App\Domains\Entity\Enums\EntityEnum;

class GPT4OMiniSearchPreviewDriver extends BaseDriver implements WithInputInterface, WithWordsInterface
{
    use HasInput;
    use HasWords;

    public function enum(): EntityEnum
    {
        return EntityEnum::GPT_4_O_MINI_SEARCH_PREVIEW;
    }
}
