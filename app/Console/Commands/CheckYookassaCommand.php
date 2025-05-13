<?php

namespace App\Console\Commands;

use App\Models\Finance\YokassaSubscription;
use App\Services\GatewaySelector;
use Illuminate\Console\Command;

class CheckYookassaCommand extends Command
{
    protected $signature = 'app:check-yookassa-command';

    protected $description = 'Command description';

    public function handle(): void
    {
        $items = YokassaSubscription::query()
            ->where('subscription_status', 'active')
            ->orWhere('subscription_status', 'yokassa_approved')
            ->get();
        foreach ($items as $activeSub) {
            $data_end_sub = $activeSub->next_pay_at;
            if (now()->gt($data_end_sub)) {
                $result = GatewaySelector::selectGateway('yokassa')::handleSubscribePay($activeSub->id);
            }
        }
    }
}
