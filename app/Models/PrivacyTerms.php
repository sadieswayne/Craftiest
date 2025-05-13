<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyTerms extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'lang_code',
        'content',
    ];
}
