<?php

namespace App\Models\Section;

use Illuminate\Database\Eloquent\Model;

class ComparisonSectionItems extends Model
{
    protected $table = 'comparison_section_items';

    protected $fillable = [
        'label', 'others', 'ours',
    ];
}
