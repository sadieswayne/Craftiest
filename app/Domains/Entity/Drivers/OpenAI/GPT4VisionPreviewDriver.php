<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\OpenAI;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasVisionPreview;
use App\Domains\Entity\Concerns\Input\HasInput;
use App\Domains\Entity\Contracts\Calculate\WithVisionPreviewInterface;
use App\Domains\Entity\Contracts\Input\WithInputInterface;
use App\Domains\Entity\Enums\EntityEnum;

class GPT4VisionPreviewDriver extends BaseDriver implements WithInputInterface, WithVisionPreviewInterface
{
    use HasInput;
    use HasVisionPreview;

    public function enum(): EntityEnum
    {
        return EntityEnum::GPT_4_VISION_PREVIEW;
    }
}
