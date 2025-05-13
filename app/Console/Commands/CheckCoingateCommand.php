<?php

namespace App\Console\Commands;

use App\Services\PaymentGateways\CoingateService;
use Illuminate\Console\Command;

class CheckCoingateCommand extends Command
{
    protected $signature = 'app:check-coingate-command';

    protected $description = 'Command description';

    public function handle(): void
    {
        CoingateService::checkPayments();
    }
}
