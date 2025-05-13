<?php

namespace App\Console\Commands;

use App\Services\PaymentGateways\RazorpayService;
use Illuminate\Console\Command;

class CheckRazorpayCommand extends Command
{
    protected $signature = 'app:check-razorpay-command';

    protected $description = 'Command description';

    public function handle(): void
    {
        RazorpayService::checkPayments();
    }
}
