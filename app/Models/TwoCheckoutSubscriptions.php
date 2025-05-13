<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoCheckoutSubscriptions extends Model
{
    use HasFactory;

    protected $table = 'subscriptions_twocheckout';

    protected $fillable = [
        'subscription_status',
        'plan_id',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
