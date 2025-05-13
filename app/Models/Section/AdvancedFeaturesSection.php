<?php

namespace App\Models\Section;

use Illuminate\Database\Eloquent\Model;

class AdvancedFeaturesSection extends Model
{
    protected $table = 'advanced_features_section';

    protected $fillable = [
        'title',
        'description',
        'image',
    ];
}
