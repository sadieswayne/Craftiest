<?php

namespace App\Models;

use App\Models\Concerns\HasCacheFirst;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingTwo extends Model
{
    use HasCacheFirst;
    use HasFactory;

    protected $guarded = [];

    protected $table = 'settings_two';

    public static int $cacheTtl = 3600 * 24;

    public static string $cacheKey = 'cache_setting_two';

    public $timestamps = false;
}
