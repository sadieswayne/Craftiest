<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplates extends Model
{
    protected $table = 'email_templates'; // 'email_templates' is the name of the table in the database

    protected $fillable = [
        'title',
        'subject',
        'content',
        'system',
        'slug',
    ];
}
