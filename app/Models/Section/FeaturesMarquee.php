<?php

namespace App\Models\Section;

use App\Models\Concerns\HasCache;
use Illuminate\Database\Eloquent\Model;

class FeaturesMarquee extends Model
{
    use HasCache;

    public static string $cacheKey = 'cache_features_marquees';

    public static int $cacheTtl = 3600 * 24;

    protected $table = 'features_marquees';

    protected $fillable = [
        'title', 'position',
    ];
}
