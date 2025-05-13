<?php

namespace App\Models\Finance;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YokassaSubscription extends Model
{
    protected $table = 'subscriptions_yokassa';

    protected $guarded = [];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
