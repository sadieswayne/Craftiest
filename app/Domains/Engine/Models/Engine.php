<?php

declare(strict_types=1);

namespace App\Domains\Engine\Models;

use App\Domains\Engine\Enums\EngineEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Engine extends Model
{
    protected $fillable = [
        'key',
        'status',
    ];

    protected $casts = [
        'key'            => EngineEnum::class,
        'status'         => StatusEnum::class,
    ];

    public function scopeIsEnabled(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::ENABLED);
    }
}
