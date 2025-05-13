<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasPlagiarism;
use App\Domains\Entity\Concerns\Input\HasInput;
use App\Domains\Entity\Contracts\Calculate\WithPlagiarismInterface;
use App\Domains\Entity\Contracts\Input\WithInputInterface;
use App\Domains\Entity\Enums\EntityEnum;

class PlagiarismCheckDriver extends BaseDriver implements WithInputInterface, WithPlagiarismInterface
{
    use HasInput;
    use HasPlagiarism;

    public function enum(): EntityEnum
    {
        return EntityEnum::PLAGIARISMCHECK;
    }
}
