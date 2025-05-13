<?php

namespace App\Domains\Entity\Drivers\FalAI;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasVideoToVideo;
use App\Domains\Entity\Concerns\Input\HasInputVideo;
use App\Domains\Entity\Contracts\Calculate\WithVideoToVideoInterface;
use App\Domains\Entity\Contracts\Input\WithInputVideoInterface;
use App\Domains\Entity\Enums\EntityEnum;

class VideoUpscalerDriver extends BaseDriver implements WithInputVideoInterface, WithVideoToVideoInterface
{
    use HasInputVideo;
    use HasVideoToVideo;

    public function enum(): EntityEnum
    {
        return EntityEnum::VIDEO_UPSCALER;
    }
}
