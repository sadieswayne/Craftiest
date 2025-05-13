<?php

namespace App\Models\Frontend;

use App\Models\Concerns\HasCacheFirst;
use Illuminate\Database\Eloquent\Model;

class FrontendSetting extends Model
{
    use HasCacheFirst;

    protected $table = 'frontend_footer_settings';

    public static int $cacheTtl = 3600 * 24;

    public static string $cacheKey = 'cache_frontend_footer_settings';
}
