<?php

namespace App\Models;

use App\Models\Concerns\HasCacheFirst;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasCacheFirst;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'free_open_ai_items' => 'array',
    ];

    public static int $cacheTtl = 3600 * 24;

    public static string $cacheKey = 'cache_setting';

    protected static function booted(): void
    {
        static::updated(function ($model) {
            if ($model->recaptcha_login && (empty($model->recaptcha_sitekey) || empty($model->recaptcha_secretkey))) {
                $model->update(['recaptcha_login' => false]);
            }

            if ($model->recaptcha_register && (empty($model->recaptcha_sitekey) || empty($model->recaptcha_secretkey))) {
                $model->update([
                    'recaptcha_register' => false,
                ]);
            }

        });
    }
}
