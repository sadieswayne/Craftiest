<?php

namespace App\Models;

use App\Models\Concerns\HasCache;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasCache;

    protected $fillable = [
        'type',
        'code',
        'status',
    ];

    public static string $cacheKey = 'cache_ad';

    public static int $cacheTtl = 3600 * 24;
}
