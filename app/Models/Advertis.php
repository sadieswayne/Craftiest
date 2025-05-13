<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertis extends Model
{
    use HasFactory;

    protected $table = 'advertis';

    protected $fillable = [
        'key',
        'title',
        'tracking_code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function trackingCode(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return json_decode($value, true);
            },
            set: function ($value) {
                return json_encode($value);
            }
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
