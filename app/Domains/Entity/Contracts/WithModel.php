<?php

declare(strict_types=1);

namespace App\Domains\Entity\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface WithModel extends WithStatus
{
    public function model(): Builder|Model|null;
}
