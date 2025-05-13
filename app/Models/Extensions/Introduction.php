<?php

namespace App\Models\Extensions;

use Illuminate\Database\Eloquent\Model;

class Introduction extends Model
{
    protected $fillable = [
        'key',
        'intro',
        'order',
        'status',
        'title',
        'file_url',
        'file_type',
        'file_path',
        'parent_id',
    ];

    protected $casts = [
        'key' => \App\Enums\Introduction::class,
    ];

    // Alt initialize itemları için ilişki
    public function children()
    {
        return $this->hasMany(Introduction::class, 'parent_id');
    }

    // Eğer parent ilişkisini de kullanıyorsanız:
    public function parent()
    {
        return $this->belongsTo(Introduction::class, 'parent_id');
    }

    public static function getFormattedSteps()
    {
        return self::query()
            ->whereNull('parent_id') // Sadece ana itemları al
            ->where('status', true)
            ->orderBy('order')
            ->with('children') // Alt itemları yükle
            ->get()
            ->map(function ($item) {
                $result = [
                    'intro'     => $item->intro,
                    'title'     => $item->title,
                    'file_url'  => $item->file_url,
                    'file_type' => $item->file_type,
                    'element'   => '[data-name="' . $item->key->value . '"]',
                ];

                // Eğer initialize itemıysa ve alt itemları varsa
                if ($item->key->value === 'initialize' && $item->children->count() > 0) {
                    $result['steps'] = $item->children->map(function ($child) {
                        return [
                            'intro'     => $child->intro,
                            'title'     => $child->title,
                            'file_url'  => $child->file_url,
                            'file_type' => $child->file_type,
                        ];
                    })->toArray();
                }

                return $result;
            });
    }
}
