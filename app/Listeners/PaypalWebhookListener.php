<?php

namespace App\Listeners;

use App\Events\PaypalWebhookEvent;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\WebhookHistory;
use App\Services\GatewaySelector;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use JsonException;
use Laravel\Cashier\Subscription as Subscriptions;
use Throwable;

class PaypalWebhookListener implements ShouldQueue
{
    use CreditUpdater;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    use InteractsWithQueue;

    public bool $afterCommit = true;

    public ?string $queue = 'default';

    public int $delay = 0; // 60

    /**
     * Handle the event.
     */
    public function handle(PaypalWebhookEvent $event): void
    {
        try {
            $settings = Setting::getCache();
            $incomingJson = json_decode($event->payload, false, 512, JSON_THROW_ON_ERROR);
            // Incoming data is verified at PayPalService handleWebhook function, which fires this event.
            $event_type = $incomingJson->event_type;
            $resource_id = $incomingJson->resource->id;
            // save incoming data
            $newData = new WebhookHistory;
            $newData->gatewaycode = 'paypal';
            $newData->webhook_id = $incomingJson->id;
            $newData->create_time = $incomingJson->create_time;
            $newData->resource_type = $incomingJson->resource_type;
            $newData->event_type = $event_type;
            $newData->summary = $incomingJson->summary;
            $newData->resource_id = $resource_id;
            $newData->resource_state = $incomingJson->resource->state ?? ($incomingJson->resource->status ?? null);
            if ($event_type === 'PAYMENT.SALE.COMPLETED') {
                $newData->parent_payment = $incomingJson->resource->parent_payment;
                $newData->amount_total = $incomingJson->resource->amount->total;
                $newData->amount_currency = $incomingJson->resource->amount->currency;
            }
            $newData->incoming_json = json_encode($incomingJson, JSON_THROW_ON_ERROR);
            $newData->status = 'check';
            $newData->save();
            // switch/check event type
            if ($event_type === 'BILLING.SUBSCRIPTION.CANCELLED') {
                // $resource_id is subscription id in this event.
                $currentSubscription = Subscriptions::where('stripe_id', $resource_id)->first();
                if ($currentSubscription->stripe_status !== 'cancelled') {
                    $currentSubscription->stripe_status = 'cancelled';
                    $currentSubscription->ends_at = Carbon::now();
                    $currentSubscription->save();
                    $newData->status = 'checked';
                    $newData->save();
                }
            } elseif ($event_type === 'PAYMENT.SALE.COMPLETED') {
                // $resource_id is transaction id in this event.
                // Hence we must make new request to get subscription id.
                $provider = GatewaySelector::selectGateway('paypal')::getPaypalProvider();
                $filters = [
                    'transaction_id' => $resource_id,
                    'start_date'     => Carbon::now()->subDays(7)->toIso8601String(),
                    'end_date'       => Carbon::now()->addDays(2)->toIso8601String(),
                ];
                // https://developer.paypal.com/docs/api/transaction-search/v1/#transactions_get
                $transactionList = $provider->listTransactions($filters);
                $transactions = json_decode($transactionList, false, 512, JSON_THROW_ON_ERROR);
                if (array_key_exists('error', $transactions) === false) {
                    foreach ($transactions->transaction_details as $transaction) {
                        // https://developer.paypal.com/docs/transaction-search/transaction-event-codes/
                        // T0002: Subscription payment. Either payment sent or payment received.
                        // S: The transaction successfully completed without a denial and after any pending statuses.
                        if ($transaction->transaction_info->transaction_event_code === 'T0002' and $transaction->transaction_status === 'S') {
                            $amountPaidValue = $transaction->transaction_info->transaction_amount->value;
                            $amountPaidCurrency = $transaction->transaction_info->transaction_amount->currency_code;
                            $email = $transaction->payer_info->email_address;
                            $name = $transaction->payer_info->given_name;
                            $surname = $transaction->payer_info->surname;
                            $transaction_id = $transaction->transaction_info->transaction_id;
                            // We can NOT get subscription id directly, thats why we are going to make a workaround.
                            // Get user
                            $user = User::where('email', $email)->first();
                            if ($user) {
                                $userId = $user->id;
                                $activeSub = getCurrentActiveSubscription($userId);
                                if ($activeSub) {
                                    $plan = Plan::where('id', $activeSub->plan_id)->first();
                                    if ($plan) {
                                        // Check if its price is equal to amountPaidValue.
                                        // amountPaidValue returns decimal with . (i.e. "value": "465.00" , "value": "-13.79")
                                        // we save price in plan as double (i.e. 10 , 19.9 (not 19.90))
                                        if (number_format((float) $amountPaidValue, 2, '.', '') === number_format((float) $plan->price, 2, '.', '')) {
                                            // check for duplication
                                            $duplicate = false;
                                            // check for first payment in subscription
                                            if (Carbon::parse($activeSub->created_at)->diffInMinutes(Carbon::parse($incomingJson->create_time)) < 5) {
                                                $duplicate = true;
                                            }
                                            if (! $duplicate) {
                                                // if it is trial then convert it to active ( Check status from gateway first )
                                                // if it is active and/or converted to active add plan word/image amount to the user
                                                if ($activeSub->stripe_status === 'trialing') {
                                                    $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);
                                                    if (isset($subscription['error'])) {
                                                        Log::error("PaypalWebhookListener::handle() -> getSubscriptionStatus() :\n" . json_encode($subscription, JSON_THROW_ON_ERROR));
                                                    } else {
                                                        if ($subscription['status'] === 'ACTIVE') {
                                                            $trial = false;
                                                            if (isset($subscription['billing_info']['cycle_executions'][0]['tenure_type']) && $subscription['billing_info']['cycle_executions'][0]['tenure_type'] === 'TRIAL') {
                                                                $trial = true;
                                                            }
                                                            if (! $trial) {
                                                                $activeSub->stripe_status = 'active';
                                                                $activeSub->save();
                                                            }
                                                            UserOrder::create([
                                                                'order_id'           => $transaction_id,
                                                                'user_id'            => $user->id,
                                                                'plan_id'            => $plan->id,
                                                                'payment_type'       => 'PayPal Recurring Payment',
                                                                'price'              => $plan->price,
                                                                'affiliate_earnings' => ($plan->price * $settings->affiliate_commission_percentage) / 100,
                                                                'status'             => 'Success',
                                                                'country'            => $user->country ?? 'Unknown',
                                                            ]);
                                                            self::creditIncreaseSubscribePlan($user, $plan);
                                                        } else {
                                                            $activeSub->stripe_status = 'cancelled';
                                                            $activeSub->ends_at = \Carbon\Carbon::now();
                                                            $activeSub->save();
                                                        }
                                                        $newData->status = 'checked';
                                                        $newData->save();
                                                    }
                                                }
                                            } else {
                                                // active or cancelled
                                                $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);
                                                if (isset($subscription['error'])) {
                                                    Log::error("PaypalWebhookListener::handle() -> getSubscriptionStatus() :\n" . json_encode($subscription, JSON_THROW_ON_ERROR));
                                                } else {
                                                    // check for duplication
                                                    $duplicate = false;
                                                    // check for first payment in subscription
                                                    if (Carbon::parse($activeSub->created_at)->diffInMinutes(Carbon::parse($incomingJson->create_time)) < 5) {
                                                        $duplicate = true;
                                                    }
                                                    if (! $duplicate) {
                                                        if ($subscription['status'] === 'ACTIVE') {
                                                            if ($activeSub->stripe_status === 'cancelled') {
                                                                $activeSub->stripe_status = 'active';
                                                                $activeSub->save();
                                                            }

                                                            UserOrder::create([
                                                                'order_id'           => $transaction_id,
                                                                'user_id'            => $user->id,
                                                                'plan_id'            => $plan->id,
                                                                'payment_type'       => 'PayPal Recurring Payment',
                                                                'price'              => $plan->price,
                                                                'affiliate_earnings' => ($plan->price * $settings->affiliate_commission_percentage) / 100,
                                                                'status'             => 'Success',
                                                                'country'            => $user->country ?? 'Unknown',
                                                            ]);
                                                            self::creditIncreaseSubscribePlan($user, $plan);
                                                        } else {
                                                            $activeSub->stripe_status = 'cancelled';
                                                            $activeSub->ends_at = \Carbon\Carbon::now();
                                                            $activeSub->save();
                                                        }
                                                        $newData->status = 'checked';
                                                        $newData->save();
                                                    }
                                                }
                                            }
                                        } else {
                                            Log::error('PaypalWebhookListener::handle() Error : Subscription prices do not match. || ' . json_encode($transactions));
                                        }
                                    } else {
                                        Log::error('PaypalWebhookListener::handle() Error : Membership Plan Not Found || ' . json_encode($transactions));
                                    }

                                } else {
                                    Log::error('PaypalWebhookListener::handle() Error : Subscription Not Found || ' . json_encode($transactions));
                                }
                            } else {
                                Log::error('PaypalWebhookListener::handle() Error : User Not Found || ' . json_encode($transactions));
                            }
                        }
                    }
                } else {
                    Log::error('PaypalWebhookListener::handle() Error : ' . $transactions->error->message);
                }
            }
            // save new order if required
            // on cancel we do not delete anything. just check if subs cancelled
        } catch (Exception $ex) {
            Log::error("PaypalWebhookListener::handle()\n" . $ex->getMessage());
        }
    }

    /**
     * Handle a job failure.
     *
     * @throws JsonException
     */
    public function failed(PaypalWebhookEvent $event, Throwable $exception): void
    {
        $space = '*****';
        $msg = '\n' . $space . '\n' . $space;
        $msg .= json_encode($event->payload, JSON_THROW_ON_ERROR);
        $msg .= '\n' . $space . '\n';
        $msg .= '\n' . $exception . '\n';
        $msg .= '\n' . $space . '\n' . $space;

        Log::error($msg);
    }
}
