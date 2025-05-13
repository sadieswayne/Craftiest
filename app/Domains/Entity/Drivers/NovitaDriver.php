<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasImages;
use App\Domains\Entity\Concerns\Input\HasInputImage;
use App\Domains\Entity\Contracts\Calculate\WithImagesInterface;
use App\Domains\Entity\Contracts\Input\WithInputImageInterface;
use App\Domains\Entity\Enums\EntityEnum;

class NovitaDriver extends BaseDriver implements WithImagesInterface, WithInputImageInterface
{
    use HasImages;
    use HasInputImage;

    public function enum(): EntityEnum
    {
        return EntityEnum::NOVITA;
    }
}
