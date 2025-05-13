<?php

namespace App\Models\Integration;

use App\Models\Extension;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Integration extends Model
{
    protected $table = 'integrations';

    protected $fillable = [
        'app',
        'description',
        'image',
        'slug',
        'status',
    ];

    public function hasExtension(): HasOne
    {
        return $this->hasOne(Extension::class, 'slug', 'slug')->where('installed', 1);
    }

    public function extension(): HasOne
    {
        return $this->hasOne(Extension::class, 'slug', 'slug');
    }

    public function getFormClassName(): string
    {
        return match ($this->slug) {
            'wordpress' => \App\Extensions\Wordpress\System\Services\Wordpress::class,
        };
    }
}
