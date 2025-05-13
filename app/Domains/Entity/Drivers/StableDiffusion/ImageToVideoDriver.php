<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\StableDiffusion;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasImageToVideo;
use App\Domains\Entity\Concerns\Input\HasInputVideo;
use App\Domains\Entity\Contracts\Calculate\WithImageToVideoInterface;
use App\Domains\Entity\Contracts\Input\WithInputVideoInterface;
use App\Domains\Entity\Enums\EntityEnum;

/**
 * @method float calculateByImageToVideo()
 */
class ImageToVideoDriver extends BaseDriver implements WithImageToVideoInterface, WithInputVideoInterface
{
    use HasImageToVideo;
    use HasInputVideo;

    public function enum(): EntityEnum
    {
        return EntityEnum::IMAGE_TO_VIDEO;
    }
}
