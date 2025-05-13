<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaystackPaymentInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'reference',
        'trans',
        'status',
        'message',
        'transaction',
        'trxref',
        'amount',
        'currency',
        'plan_code',
        'customer_code',
        'other',
    ];
}
