<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'menu_group_id',
        'parent_id',
        'key',
        'route',
        'route_slug',
        'label',
        'icon',
        'svg',
        'order',
        'is_active',
        'params',
        'type',
        'extension',
        'bolt_menu',
        'bolt_background',
        'bolt_foreground',
        'letter_icon',
        'letter_icon_bg',
        'custom_menu',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'params'    => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }
}
