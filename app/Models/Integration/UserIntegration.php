<?php

namespace App\Models\Integration;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIntegration extends Model
{
    protected $table = 'user_integrations';

    protected $fillable = ['integration_id', 'user_id', 'credentials'];

    protected $casts = [
        'credentials' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
