<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateways extends Model
{
    protected $guarded = [];

    public function isSandbox(): bool
    {
        return $this->mode === 'sandbox';
    }
}
