<?php

namespace App\Models\Section;

use App\Models\Concerns\HasCache;
use Illuminate\Database\Eloquent\Model;

class BannerBottomText extends Model
{
    use HasCache;

    protected $table = 'banner_bottom_texts';

    protected $fillable = ['text'];

    public static string $cacheKey = 'cache_banner_bottom_texts';

    public static int $cacheTtl = 3600 * 24;
}
