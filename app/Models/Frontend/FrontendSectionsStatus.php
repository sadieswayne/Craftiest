<?php

namespace App\Models\Frontend;

use App\Models\Concerns\HasCacheFirst;
use Illuminate\Database\Eloquent\Model;

class FrontendSectionsStatus extends Model
{
    use HasCacheFirst;

    protected $table = 'frontend_sections_statuses_titles';

    public static int $cacheTtl = 3600 * 24;

    public static string $cacheKey = 'cache_frontend_sections_statuses_titles';
}
