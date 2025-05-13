<?php

namespace App\Models;

use App\Models\Concerns\HasCache;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasCache;

    protected $table = 'currencies';

    protected $guarded = [];

    public static string $cacheKey = 'cache_currency';

    public static int $cacheTtl = 3600 * 24;

    public static function cacheFromSetting(string $default_currency)
    {
        return self::getCache(static function () use ($default_currency) {
            return self::where('id', $default_currency)->first();
        }, ':id:' . $default_currency);
    }

    public static function cacheFirstId(?int $id = null)
    {
        if (is_null($id)) {
            return null;
        }

        $currencies = self::getCache(static function () {
            return self::all()->toArray();
        }, '_all');

        return $currencies->firstWhere('id', $id);
    }
}
