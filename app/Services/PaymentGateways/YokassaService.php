<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Actions\EmailPaymentConfirmation;
use App\Enums\Plan\FrequencyEnum;
use App\Events\YokassaWebhookEvent;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Finance\YokassaSubscription;
use App\Models\Gateways;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use YooKassa\Client;

/**
 * Base functions foreach payment gateway
 *
 * @param subscribe ($plan)                                     || Displays Payment Page of the gateway
 * @param subscribeCheckout (Request $request, $referral= null) || -
 * @param prepaid ($plan)                                       || Displays Payment Page of Yokassa gateway for prepaid plans
 * @param prepaidCheckout (Request $request, $referral= null)   || Handles payment action of Yokassa
 * @param handleSubscribePay ($activeSub_id)                    || handle payment with saved payment
 * @param getSubscriptionDaysLeft                               ||
 * @param subscribeCancel                                       || Cancels current subscription plan
 * @param checkIfTrial                                          ||
 * @param getSubscriptionRenewDate                              ||
 * @param cancelSubscribedPlan ($subscription)                  ||
 */
class YokassaService
{
    use CreditUpdater;

    protected static $GATEWAY_CODE = 'yokassa';

    protected static $GATEWAY_NAME = 'Yokassa';

    // payment functions
    public static function saveAllProducts()
    {
        return true;
    }

    public static function saveProduct($plan)
    {
        return true;
    }

    public static function subscribe($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $user = Auth::user();

        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->live_client_id;
            $key = $gateway->live_client_secret;
        }

        try {
            $client = new Client;
            $client->setAuth($shop_id, $key);

            $coupon = checkCouponInRequest(); // if there a coupon in request it will return the coupin instanse
            $newDiscountedPrice = $plan->price;
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($plan->price, $taxRate);
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            if ($coupon) {
                $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                $newDiscountedPriceCents = (int) (((float) $newDiscountedPrice) * 100);
                if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
            }
            $newDiscountedPrice += $taxValue;
            $payment = $client->createPayment(
                [
                    'amount' => [
                        'value'    => $newDiscountedPrice,
                        'currency' => $currency,
                    ],
                    'confirmation' => [
                        'type' => 'embedded',
                    ],
                    'capture'             => true,
                    'description'         => 'Order No. 1',
                    'save_payment_method' => true,
                ],
                uniqid('', true)
            );
            $confirmation_token = $payment->confirmation->confirmation_token;
            $payment_id = $payment->id;

            return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate', 'gateway', 'payment_id', 'confirmation_token'));
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }

    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);
        $couponID = $request->input('couponID', null);
        $paymentId = $request->input('paymentID', null);
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->live_client_id;
            $key = $gateway->live_client_secret;
        }
        $user = Auth::user();
        $settings = Setting::getCache();

        try {
            DB::beginTransaction();
            $plan = Plan::where('id', $planID)->first();
            $client = new Client;
            $client->setAuth($shop_id, $key);
            $payment = $client->getPaymentInfo($paymentId);
            $total = $plan->price;
            $taxValue = taxToVal($plan->price, $gateway->tax);
            if ($couponID) {
                $coupon = Coupon::where('code', $couponID)->first();
                if ($coupon) {
                    $coupon->usersUsed()->attach(auth()->user()->id);
                    $couponID = $coupon->discount;
                    $total -= ($plan->price * ($coupon->discount / 100));
                    if ($total != floor($total)) {
                        $total = number_format($total, 2);
                    }
                }
            }
            $total += $taxValue;
            if ($payment->paid == true) {
                $paymentMethod = $payment->payment_method->type;
                $payment_method_id = $payment->payment_method->id;
                // new subscription
                $subscription = new YokassaSubscription;
                $subscription->plan_id = $plan->id;
                $subscription->user_id = $user->id;
                $subscription->name = $plan->id;
                $subscription->payment_method_id = $payment_method_id;

                if ($plan->frequency == FrequencyEnum::LIFETIME_MONTHLY->value || $plan->frequency == FrequencyEnum::LIFETIME_YEARLY->value) {
                    $subscription->next_pay_at = $plan->frequency == FrequencyEnum::LIFETIME_MONTHLY->value ? \Carbon\Carbon::now()->addMonths(1) : \Carbon\Carbon::now()->addYears(1);
                    $subscription->auto_renewal = 1;
                    $subscription->subscription_status = 'yokassa_approved';
                } else {
                    $subscription->subscription_status = 'active';
                    $subscription->next_pay_at = $plan->trial_days != 0 ? \Carbon\Carbon::now()->addDays($plan->trial_days) : \Carbon\Carbon::now()->addDays(30);
                }
                $subscription->tax_rate = $gateway->tax;
                $subscription->tax_value = $taxValue;
                $subscription->coupon = $couponID;
                $subscription->total_amount = $total;
                $subscription->save();

                // new order
                $payment = new UserOrder;
                $payment->order_id = $subscription->payment_method_id;
                $payment->plan_id = $plan->id;
                $payment->user_id = $user->id;
                $payment->payment_type = self::$GATEWAY_CODE;
                $payment->price = $total;
                $payment->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
                $payment->status = 'Success';
                $payment->country = $user->country ?? 'Unknown';
                $payment->tax_rate = $gateway->tax;
                $payment->tax_value = $taxValue;
                $payment->save();

                self::creditIncreaseSubscribePlan($user, $plan);

                CreateActivity::for($user, __('Subscribed'), $plan->name . ' ' . __('Plan'));
                EmailPaymentConfirmation::create($user, $plan)->send();
                DB::commit();
            } else {
                DB::rollBack();

                return redirect()->route('dashboard.' . auth()->user()->type . '.index')->with(['message' => __('You are failed your purchase. If you paid for this, please cantact us'), 'type' => 'failed']);
            }

            return redirect()->route('dashboard.user.payment.succesful')->with(['message' => __('Thank you for your purchase. Enjoy your remaining words and images.'), 'type' => 'success']);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return redirect()->route('dashboard.' . auth()->user()->type . '.index')->with(['message' => __('You are failed your purchase. If you paid for this, please cantact us'), 'type' => 'failed']);
        }
    }

    public static function handleSubscribePay($activeSub_id)
    {
        $user = Auth::user();
        $settings = Setting::getCache();
        $activeSub = YokassaSubscription::where('id', '=', $activeSub_id)->first();
        $plan_id = $activeSub->plan_id;
        $plan = Plan::where('id', $plan_id)->first();
        $payment_method_id = $activeSub->payment_method_id;
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->live_client_id;
            $key = $gateway->live_client_secret;
        }
        $taxValue = taxToVal($plan->price, $gateway->tax);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $total = $plan->price + $taxValue;
        if ($activeSub->subscription_status == 'yokassa_approved') {
            $user = User::where('id', '=', $activeSub->user_id)->first();

            self::creditIncreaseSubscribePlan($user, $plan);
            $activeSub->next_pay_at = Carbon::now()->addMonth();
            $activeSub->save();

            return 'success';
        } else {
            $client = new Client;
            $client->setAuth($shop_id, $key);
            $payment = $client->createPayment(
                [
                    'amount' => [
                        'value'    => $total,
                        'currency' => $currency,
                    ],
                    'capture'           => true,
                    'payment_method_id' => $payment_method_id,
                    'description'       => 'Auto payment',
                ],
                uniqid('', true)
            );
            if ($payment->paid == true) {
                $payment = new UserOrder;
                $payment->order_id = Str::random(12);
                $payment->plan_id = $plan->id;
                $payment->user_id = $user->id;
                $payment->payment_type = 'Credit, Debit Card';
                $payment->price = $total;
                $payment->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
                $payment->status = 'Success';
                $payment->country = $user->country ?? 'Unknown';
                $payment->tax_rate = $gateway->tax;
                $payment->tax_value = $taxValue;
                $payment->save();

                $user = User::where('id', '=', $activeSub->user_id)->first();

                self::creditIncreaseSubscribePlan($user, $plan);

                \App\Models\Usage::getSingle()->updateSalesCount($total);

                $activeSub->next_pay_at = Carbon::now()->addMonth();
                $activeSub->save();

                return 'success';
            } else {
                $activeSub->payment_method_id = '';
                $activeSub->subscription_status = '';
                $activeSub->save();

                return 'false';
            }
        }
    }

    public static function prepaid($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);

        try {
            $newDiscountedPrice = $plan->price;
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            if ($gateway->mode == 'sandbox') {
                $shop_id = $gateway->sandbox_client_id;
                $key = $gateway->sandbox_client_secret;
            } else {
                $shop_id = $gateway->live_client_id;
                $key = $gateway->live_client_secret;
            }
            $coupon = checkCouponInRequest();
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($newDiscountedPrice, $taxRate);
            if ($coupon) {
                $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
            }
            if ($taxValue > 0) {
                $newDiscountedPrice += $taxValue;
            }

            $client = new Client;
            $client->setAuth($shop_id, $key);
            $payment = $client->createPayment(
                [
                    'amount' => [
                        'value'    => $newDiscountedPrice,
                        'currency' => $currency,
                    ],
                    'confirmation' => [
                        'type' => 'embedded',
                    ],
                    'capture'     => true,
                    'description' => 'Order No. 1',
                ],
                uniqid('', true)
            );
            $confirmation_token = $payment->confirmation->confirmation_token;
            $payment_id = $payment->id;

            return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'payment_id', 'confirmation_token', 'taxValue', 'taxRate', 'gateway', 'currency'));
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> prepaid(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);
        $couponID = $request->input('couponID', null);
        $paymentId = $request->input('paymentID', null);
        $settings = Setting::getCache();
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->live_client_id;
            $key = $gateway->live_client_secret;
        }
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $client = new Client;
            $client->setAuth($shop_id, $key);
            $payment = $client->getPaymentInfo($paymentId);
            if ($payment->paid == true) {
                $plan = Plan::where('id', $planID)->first();
                $total = $plan->price;
                $taxValue = taxToVal($plan->price, $gateway->tax);
                if ($couponID) {
                    $coupon = Coupon::where('code', $couponID)->first();
                    if ($coupon) {
                        $coupon->usersUsed()->attach(auth()->user()->id);
                        $couponID = $coupon->discount;
                        $total -= ($plan->price * ($coupon->discount / 100));
                        if ($total != floor($total)) {
                            $total = number_format($total, 2);
                        }
                    }
                }
                $total += $taxValue;

                // new order
                $payment = new UserOrder;
                $payment->order_id = 'YPO-' . strtoupper(Str::random(13));
                $payment->plan_id = $plan->id;
                $payment->type = 'prepaid';
                $payment->user_id = $user->id;
                $payment->payment_type = self::$GATEWAY_CODE;
                $payment->price = $total;
                $payment->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
                $payment->status = 'Success';
                $payment->country = $user->country ?? 'Unknown';
                $payment->tax_rate = $gateway->tax;
                $payment->tax_value = $taxValue;
                $payment->save();

                self::creditIncreaseSubscribePlan($user, $plan);

                CreateActivity::for($user, __('Purchased'), $plan->name . ' ' . __('Token Pack'));
                EmailPaymentConfirmation::create($user, $plan)->send();
                DB::commit();
                \App\Models\Usage::getSingle()->updateSalesCount($total);

                return redirect()->route('dashboard.user.payment.succesful')->with(['message' => __('Thank you for your purchase. Enjoy your remaining words and images.'), 'type' => 'success']);
            }

            return redirect()->route('dashboard.' . auth()->user()->type . '.index')->with(['message' => __('You are failed your purchase. If you paid for this, please cantact us'), 'type' => 'failed']);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> prepaidCheckout(): ' . $ex->getMessage());

            return redirect()->route('dashboard.' . auth()->user()->type . '.index')->with(['message' => __('You are failed your purchase. If you paid for this, please cantact us'), 'type' => 'failed']);
        }

    }

    public static function getSubscriptionDaysLeft()
    {
        $userId = Auth::user()->id;
        $activeSub = YokassaSubscription::where([['subscription_status', '=', 'active'], ['user_id', '=', $userId]])->orWhere([['subscription_status', '=', 'yokassa_approved'], ['user_id', '=', $userId]])->first();
        if ($activeSub != null) {
            return \Carbon\Carbon::now()->diffInDays($activeSub->next_pay_at);
        }

        return null;
    }

    public static function subscribeCancel($internalUser = null): RedirectResponse
    {
        $user = $internalUser ?? Auth::user();

        $activeSub = YokassaSubscription::where([['subscription_status', '=', 'active'], ['user_id', '=', $user->id]])->orWhere([['subscription_status', '=', 'yokassa_approved'], ['user_id', '=', $user->id]])->first();

        if ($activeSub !== null) {
            $plan = Plan::findOrFail($activeSub->plan_id);
            $activeSub->update([
                'subscription_status' => 'cancelled',
                'next_pay_at'         => Carbon::now(),
            ]);

            self::creditDecreaseCancelPlan($user, $plan);

            CreateActivity::for($user, 'Cancelled', 'Subscription plan');

            if ($internalUser != null) {
                return back()->with(['message' => __('User subscription is cancelled succesfully.'), 'type' => 'success']);
            }

            return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
        }

        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }

    public static function getSubscriptionRenewDate()
    {
        $userId = Auth::user()->id;
        $activeSub = YokassaSubscription::where([['subscription_status', '=', 'active'], ['user_id', '=', $userId]])->orWhere([['subscription_status', '=', 'yokassa_approved'], ['user_id', '=', $userId]])->first();
        if ($activeSub != null) {
            return \Carbon\Carbon::createFromTimeStamp($activeSub->next_pay_at)->format('F jS, Y');
        }

        return null;
    }

    public static function getSubscriptionStatus($user_id = null): ?bool
    {
        $userId = $user_id ?? Auth::user()->id;
        $activeSub = YokassaSubscription::where([['subscription_status', '=', 'active'], ['user_id', '=', $userId]])->orWhere([['subscription_status', '=', 'yokassa_approved'], ['user_id', '=', $userId]])->first();
        if ($activeSub) {
            // when lifetime
            if ($activeSub->subscription_status === 'yokassa_approved') {
                // TODO: we can renew from here or from command
                return true;
            }

            if ($activeSub['subscription_status'] === 'active') {
                return true;
            }

            $activeSub->subscription_status = 'cancelled';
            $activeSub->next_pay_at = \Carbon\Carbon::now();
            $activeSub->save();

            return false;
        }

        return null;
    }

    public static function checkIfTrial()
    {
        $userId = Auth::user()->id;
        $activeSub = YokassaSubscription::where([['subscription_status', '=', 'active'], ['user_id', '=', $userId]])->orWhere([['subscription_status', '=', 'yokassa_approved'], ['user_id', '=', $userId]])->first();
        if ($activeSub != null) {
            return false;
        }

        return false;
    }

    public static function cancelSubscribedPlan($subscription, $planId)
    {
        $user = Auth::user();
        if ($subscription != null) {
            $plan = Plan::where('id', $planId)->first();

            $subscription->subscription_status = 'cancelled';
            $subscription->next_pay_at = Carbon::now();
            $subscription->save();

            self::creditDecreaseCancelPlan($user, $plan);

            return true;
        }

        return false;
    }

    // webhook functions
    public function verifyIncomingJson(Request $request)
    {
        try {
            $gateway = Gateways::where('code', self::$GATEWAY_CODE)->first();
            $webhook_event = $request->getContent();
            if ($webhook_event == null) {
                return false;
            }
            if (json_validate($webhook_event) == false) {
                return false;
            }
            Log::info('+++++++++++++');
            Log::info($request);
            $payload = $request->getContent();
            $yokassa_ip_array = [
                '185.71.76.0/27',
                '185.71.77.0/27',
                '77.75.153.0/25',
                '77.75.156.11',
                '77.75.156.35',
                '77.75.154.128/25',
                '2a02:5180::/32',
            ];
            $ip_address = $request->ip();
            if (in_array($ip_address, $yokassa_ip_array)) {
                return $payload;
            } else {
                return false;
            }
        } catch (Exception $th) {
            Log::error('(Webhooks) Yokassa::verifyIncomingJson(): ' . $th->getMessage());
        }

        return false;
    }

    public function handleWebhook(Request $request)
    {

        // Log::info($request->getContent());
        // $verified = $request->getContent();

        $verified = self::verifyIncomingJson($request);

        if ($verified != null) {

            // Retrieve the JSON payload
            $payload = $verified;

            // Fire the event with the payload
            event(new YokassaWebhookEvent($payload));

            return response()->json(['success' => true]);

        } else {
            // Incoming json is NOT verified
            abort(404);
        }
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            'code'                  => 'yokassa',
            'title'                 => 'Yokassa',
            'link'                  => 'https://yokassa.ru/',
            'active'                => 0,
            'available'             => 1,
            'img'                   => '/assets/img/payments/yokassa.svg',
            'whiteLogo'             => 0,
            'mode'                  => 1,
            'sandbox_client_id'     => 1,
            'sandbox_client_secret' => 1,
            'sandbox_app_id'        => 0,
            'live_client_id'        => 1,
            'live_client_secret'    => 1,
            'live_app_id'           => 0,
            'currency'              => 1,
            'currency_locale'       => 0,
            'notify_url'            => 0,
            'base_url'              => 0,
            'sandbox_url'           => 0,
            'locale'                => 0,
            'validate_ssl'          => 0,
            'webhook_secret'        => 0,
            'logger'                => 0,
            'tax'                   => 1,              // Option in settings
            'bank_account_details'  => 0,
            'bank_account_other'    => 0,
        ];
    }
}
