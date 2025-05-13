<?php

namespace App\Models\Team;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'user_id',
        'is_shared',
        'name',
        'allow_seats',
        'used_image_credit',
        'word_credit',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'team_id');
    }
}
