<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folders extends Model
{
    protected $fillable = [
        'name',
        'created_by',
        'team_id',
    ];

    public function userOpenais(): HasMany
    {
        return $this->hasMany(UserOpenai::class, 'folder_id');
    }
}
