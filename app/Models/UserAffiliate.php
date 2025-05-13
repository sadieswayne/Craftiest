<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAffiliate extends Model
{
    protected $table = 'user_affiliates';

    protected $fillable = [
        'user_id',
        'amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
