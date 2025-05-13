<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $table = 'prompt_library';

    use HasFactory;

    protected $guarded = [];
}
