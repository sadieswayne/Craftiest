<?php

declare(strict_types=1);

namespace App\Domains\Engine\Contracts;

use App\Domains\Entity\Contracts\WithStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface WithModel extends WithStatus
{
    public function model(): Builder|Model|null;
}
