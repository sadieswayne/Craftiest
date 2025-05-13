<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\StableDiffusion;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasImages;
use App\Domains\Entity\Concerns\Input\HasInputImage;
use App\Domains\Entity\Contracts\Calculate\WithImagesInterface;
use App\Domains\Entity\Contracts\Input\WithInputImageInterface;
use App\Domains\Entity\Enums\EntityEnum;

class XL1024V10Driver extends BaseDriver implements WithImagesInterface, WithInputImageInterface
{
    use HasImages;
    use HasInputImage;

    public function enum(): EntityEnum
    {
        return EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0;
    }
}
