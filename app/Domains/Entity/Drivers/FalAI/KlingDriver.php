<?php

namespace App\Domains\Entity\Drivers\FalAI;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasTextToVideo;
use App\Domains\Entity\Concerns\Input\HasInputVideo;
use App\Domains\Entity\Contracts\Calculate\WithTextToVideoInterface;
use App\Domains\Entity\Contracts\Input\WithInputVideoInterface;
use App\Domains\Entity\Enums\EntityEnum;

class KlingDriver extends BaseDriver implements WithInputVideoInterface, WithTextToVideoInterface
{
    use HasInputVideo;
    use HasTextToVideo;

    public function enum(): EntityEnum
    {
        return EntityEnum::KLING;
    }
}
