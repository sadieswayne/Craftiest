<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Actions\EmailPaymentConfirmation;
use App\Enums\Plan\FrequencyEnum;
use App\Enums\Plan\TypeEnum;
use App\Events\PaypalWebhookEvent;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\CustomBilingPlans;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\OldGatewayProducts;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\UserOrder;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

// use App\Models\Subscriptions;
// use App\Models\SubscriptionItems;

/**
 * Base functions foreach payment gateway
 *
 * @param saveAllProducts                                       || Used to generate new product id and price id of all saved membership plans in paypal gateway
 * @param saveProduct ($plan)                                   || Saves Membership plan product in the gateway
 * @param subscribe ($plan)                                     || Displays Payment Page of the gateway
 * @param subscribeCheckout (Request $request, $referral= null) || -
 * @param prepaid ($plan)                                       || Displays Payment Page of the gateway for prepaid plans
 * @param prepaidCheckout (Request $request, $referral= null)   || -
 * @param getSubscriptionStatus ($incomingUserId = null)        ||
 * @param getSubscriptionDaysLeft                               ||
 * @param subscribeCancel                                       ||
 * @param checkIfTrial                                          ||
 * @param getSubscriptionRenewDate                              ||
 * @param cancelSubscribedPlan ($subscription)                  ||
 */
class PayPalService
{
    use CreditUpdater;

    protected static $GATEWAY_CODE = 'paypal';

    protected static $GATEWAY_NAME = 'PayPal';

    // payment functions
    public static function saveAllProducts()
    {
        $provider = self::getPaypalProvider();

        try {
            $plans = Plan::where('active', 1)->get();
            foreach ($plans as $plan) {
                self::saveProduct($plan);
            }
            // create webhooks after saving the products
            $tmp = self::createWebhook();
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> saveAllProducts(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function saveProduct($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $provider = self::getPaypalProvider($gateway);

        try {
            DB::beginTransaction();
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $settings = Setting::getCache();
            $user = Auth::user();
            $oldProductId = null;
            $plan = Plan::where('id', $plan->id)->first();
            // check product
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product != null) {
                // Create product in every situation. maybe user updated paypal credentials.
                if ($product->product_id !== null) {
                    $oldProductId = $product->product_id; // Product has been created before
                } // ELSE Product has not been created before but record exists. Create new product and update record.
            } else {
                $product = new GatewayProducts;
                $product->plan_id = $plan->id;
                $product->gateway_code = self::$GATEWAY_CODE;
                $product->gateway_title = self::$GATEWAY_NAME;
            }
            $data = [
                'name'        => $plan->name,
                'description' => $plan->name,
                'type'        => 'SERVICE',
                'category'    => 'SOFTWARE',
            ];
            $request_id = 'create-product-' . time();
            $newProduct = $provider->createProduct($data, $request_id);
            $product->plan_name = $plan->name;
            $product->product_id = $newProduct['id'];
            $product->save();

            if ($plan->price != 0 && $plan->type == TypeEnum::SUBSCRIPTION->value && $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value && $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value) {
                $interval = $plan->frequency == FrequencyEnum::MONTHLY->value ? 'MONTH' : 'YEAR';

                if ($plan->trial_days != 'undefined') {
                    $trials = $plan->trial_days ?? 0;
                } else {
                    $trials = 0;
                }
                $planData = self::createBillingPlanData($product->product_id, $plan->name, $trials, $currency, $interval, $plan->price, $gateway->tax);
                // This line is not in docs. but required in execution. Needed ~5 hours to fix.
                $request_id = 'create-plan-' . time();
                $billingPlan = $provider->createPlan($planData, $request_id);
                // check price_id ( Billing plans id ), if there one make it old
                if ($product->price_id != null) {
                    $history = new OldGatewayProducts;
                    $history->plan_id = $plan->id;
                    $history->plan_name = $plan->name;
                    $history->gateway_code = self::$GATEWAY_CODE;
                    $history->product_id = $product->product_id;
                    $history->old_product_id = $oldProductId;
                    $history->old_price_id = $product->price_id; // Deactivate old billing plan --> Moved to updateUserData()
                    $history->new_price_id = $billingPlan['id'];
                    $history->status = 'check';
                    $history->save();
                    $tmp = self::updateUserData();
                }
                $product->price_id = $billingPlan['id'];
            } else {
                $product->price_id = 'Not Needed';
            }
            $product->save();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> saveProduct(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribe($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $provider = self::getPaypalProvider($gateway);

        try {
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            $productId = $product->product_id;
            $settings = Setting::getCache();
            $user = Auth::user();
            $coupon = checkCouponInRequest();
            $newDiscountedPrice = $plan->price;
            if ($product->product_id == null) {
                $exception = __('Paypal product is not defined! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            if ($product->price_id == null) {
                $exception = __('Paypal product ID is not set! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            if ($coupon) {
                $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
            }
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($newDiscountedPrice, $taxRate);
            if ($plan->type == TypeEnum::SUBSCRIPTION->value && $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value && $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value) {
                // if there is a coupon discount then change the biling plan
                if ($newDiscountedPrice != $plan->price) {
                    $interval = $plan->frequency == FrequencyEnum::MONTHLY->value ? 'MONTH' : 'YEAR';
                    if ($plan->trial_days != 'undefined') {
                        $trials = $plan->trial_days ?? 0;
                    } else {
                        $trials = 0;
                    }
                    $planData = self::createBillingPlanData($product->product_id, $plan->name, $trials, $currency, $interval, $plan->price, $gateway->tax, $newDiscountedPrice);
                    // This line is not in docs. but required in execution. Needed ~5 hours to fix.
                    $request_id = 'create-plan-' . time();
                    $billingPlan = $provider->createPlan($planData, $request_id);
                    $billingPlanId = $billingPlan['id'];
                } else {
                    $billingPlanId = $product->price_id;
                }
            } else {
                // if lifetime plans
                $billingPlanId = null;
            }

            return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate', 'billingPlanId', 'productId', 'gateway'));
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }

    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);
        $couponID = $request->input('couponID', null);
        $paypalSubscriptionID = $request->input('paypalSubscriptionID', null);
        $billingPlanId = $request->input('billingPlanId', null);
        $productId = $request->input('productId', null);
        $settings = Setting::getCache();
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $provider = self::getPaypalProvider($gateway);
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $plan = Plan::where('id', $planID)->first();
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'product_id' => $productId, 'gateway_code' => self::$GATEWAY_CODE])->first();
            // check if $product->price_id != to $billingPlanId, if not thats means its custom plan then save it in custom plans table.
            if ($product->price_id != $billingPlanId) {
                $newcustom = new CustomBilingPlans;
                $newcustom->gateway = self::$GATEWAY_CODE;
                $newcustom->plan_id = $planID;
                $newcustom->main_plan_price_id = $product->price_id;
                $newcustom->custom_plan_price_id = $billingPlanId;
                $newcustom->save();
            }
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

            // new subscription
            $subscription = new Subscriptions;

            if ($billingPlanId == 'lifetime' && $paypalSubscriptionID == 'lifetime') {
                $subscription->stripe_id = 'PLS-' . strtoupper(Str::random(13));
                $subscription->stripe_price = $product->price_id;
                $subscription->ends_at = $plan->frequency == FrequencyEnum::LIFETIME_MONTHLY->value ? \Carbon\Carbon::now()->addMonths(1) : \Carbon\Carbon::now()->addYears(1);
                $subscription->auto_renewal = 1;
                $subscription->stripe_status = 'paypal_approved';
                $subscription->trial_ends_at = null;
            } else {
                $subscription->stripe_id = $paypalSubscriptionID;
                $subscription->stripe_price = $billingPlanId;
                $subscription->stripe_status = 'active';
                $subscription->ends_at = $plan->trial_days != 0 ? \Carbon\Carbon::now()->addDays($plan->trial_days) : \Carbon\Carbon::now()->addDays(30);
                $subscription->trial_ends_at = $plan->trial_days != 0 ? \Carbon\Carbon::now()->addDays($plan->trial_days) : null;
            }
            $subscription->user_id = $user->id;
            $subscription->name = $planID;
            $subscription->quantity = 1;
            $subscription->plan_id = $planID;
            $subscription->paid_with = self::$GATEWAY_CODE;
            $subscription->tax_rate = $gateway->tax;
            $subscription->tax_value = $taxValue;
            $subscription->coupon = $couponID;
            $subscription->total_amount = $total;
            $subscription->save();

            // new subscription item
            // $subscriptionItem = new SubscriptionItems();
            // $subscriptionItem->subscription_id = $subscription->id;
            // $subscriptionItem->stripe_id = $subscription->stripe_id;
            // $subscriptionItem->stripe_product = $productId;
            // $subscriptionItem->stripe_price = $subscription->stripe_price;
            // $subscriptionItem->quantity = 1;
            // $subscriptionItem->save();

            // new order
            $payment = new UserOrder;
            $payment->order_id = $subscription->stripe_id;
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
            \App\Models\Usage::getSingle()->updateSalesCount($total);

            DB::commit();

            if (class_exists('App\Extensions\Affilate\System\Events\AffiliateEvent')) {
                event(new \App\Extensions\Affilate\System\Events\AffiliateEvent($total, $gateway->currency));
            }

            return ['result' => 'OK'];
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return ['result' => $ex->getMessage()];
        }
    }

    public static function prepaid($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $provider = self::getPaypalProvider($gateway);

        try {
            $newDiscountedPrice = $plan->price;
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $coupon = checkCouponInRequest();
            $productId = self::getPaypalProductId($plan);
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
            if ($productId == null) {
                $exception = __('Paypal product is not defined! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }

            return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'productId', 'taxValue', 'taxRate', 'gateway', 'currency'));
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> prepaid(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);
        $orderID = $request->input('orderID', null);
        $couponID = $request->input('couponID', null);
        $productId = $request->input('productId', null);
        $settings = Setting::getCache();
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $provider = self::getPaypalProvider($gateway);
        $user = Auth::user();

        try {
            DB::beginTransaction();
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
            $payment->order_id = $orderID;
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

            CreateActivity::for($user, __('Purchased'), $plan->name . ' ' . __('Plan'));
            EmailPaymentConfirmation::create($user, $plan)->send();
            \App\Models\Usage::getSingle()->updateSalesCount($total);
            DB::commit();

            return ['result' => 'OK'];
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return ['result' => $ex->getMessage()];
        }

    }

    // other functions
    public static function cancelSubscribedPlan($subscription, $planId)
    {
        $user = Auth::user();
        $provider = self::getPaypalProvider();
        if ($subscription != null) {
            $plan = Plan::where('id', $planId)->first();

            if ($subscription->stripe_status == 'paypal_approved') {
                $subscription->stripe_status = 'cancelled';
                $subscription->ends_at = Carbon::now();
                $subscription->save();

                self::creditDecreaseCancelPlan($user, $plan);

                return true;
            } else {
                $response = $provider->cancelSubscription($subscription->stripe_id, 'Plan deleted by admin.');
                if ($response == '') {
                    $subscription->stripe_status = 'cancelled';
                    $subscription->ends_at = \Carbon\Carbon::now();
                    $subscription->save();

                    self::creditDecreaseCancelPlan($user, $plan);

                    return true;
                }
            }
        }

        return false;
    }

    public static function getSubscriptionRenewDate()
    {
        $provider = self::getPaypalProvider();
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            if ($activeSub->stripe_status == 'paypal_approved') {
                return \Carbon\Carbon::createFromTimeStamp($activeSub->ends_at)->format('F jS, Y');
            } else {
                $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);
                if (isset($subscription['error'])) {
                    Log::error("PayPalService::getSubscriptionRenewDate() :\n" . json_encode($subscription));

                    return back()->with(['message' => 'PayPal Gateway : ' . $subscription['error']['message'], 'type' => 'error']);
                }
                if ($subscription['billing_info']['next_billing_time']) {
                    return \Carbon\Carbon::parse($subscription['billing_info']['next_billing_time'])->format('F jS, Y');
                } else {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = \Carbon\Carbon::now();
                    $activeSub->save();

                    return \Carbon\Carbon::now()->format('F jS, Y');
                }
            }
        }

        return null;
    }

    public static function checkIfTrial()
    {
        $provider = self::getPaypalProvider();
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            if ($activeSub->stripe_status == 'paypal_approved') {
                return false;
            } else {
                $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);
                if (isset($subscription['error'])) {
                    Log::error("PayPalService::getSubscriptionStatus() :\n" . json_encode($subscription));

                    return back()->with(['message' => 'PayPal Gateway : ' . $subscription['error']['message'], 'type' => 'error']);
                }
                if (isset($subscription['billing_info']['cycle_executions'][0]['tenure_type'])) {
                    if ($subscription['billing_info']['cycle_executions'][0]['tenure_type'] == 'TRIAL') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function subscribeCancel($internalUser = null)
    {
        $user = $internalUser ?? Auth::user();
        $provider = self::getPaypalProvider();
        $userId = $user->id;
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            $plan = Plan::where('id', $activeSub->plan_id)->first();
            if ($activeSub->stripe_status == 'paypal_approved') {
                $activeSub->stripe_status = 'cancelled';
                $activeSub->ends_at = Carbon::now();
                $activeSub->save();

                self::creditDecreaseCancelPlan($user, $plan);

                CreateActivity::for($user, __('Cancelled'), 'Subscription plan');

                return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
            } else {
                $response = $provider->cancelSubscription($activeSub->stripe_id, 'Not satisfied with the service');
                if ($response == '') {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = \Carbon\Carbon::now();
                    $activeSub->save();

                    self::creditDecreaseCancelPlan($user, $plan);

                    CreateActivity::for($user, __('Cancelled'), 'Subscription plan');
                    if ($internalUser != null) {
                        return back()->with(['message' => __('User subscription is cancelled succesfully.'), 'type' => 'success']);
                    }

                    return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
                } else {
                    return back()->with(['message' => __('Your subscription could not cancelled.'), 'type' => 'error']);
                }
            }
        }

        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }

    public static function getSubscriptionDaysLeft()
    {
        $provider = self::getPaypalProvider();
        $userId = Auth::user()->id;
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            if ($activeSub->stripe_status == 'paypal_approved') {
                return Carbon::now()->diffInDays(Carbon::parse($activeSub->ends_at));
            } else {
                $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);
                if (! isset($subscription['error'])) {
                    // if user is in trial
                    if (isset($subscription['billing_info']['cycle_executions'][0]['tenure_type'])) {
                        if ($subscription['billing_info']['cycle_executions'][0]['tenure_type'] == 'TRIAL') {
                            return $subscription['billing_info']['cycle_executions'][0]['cycles_remaining'];
                        } else {
                            if (isset($subscription['billing_info']['next_billing_time'])) {
                                return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($subscription['billing_info']['next_billing_time']));
                            } else {
                                $activeSub->stripe_status = 'cancelled';
                                $activeSub->ends_at = \Carbon\Carbon::now();
                                $activeSub->save();

                                return \Carbon\Carbon::now()->format('F jS, Y');
                            }
                        }
                    }
                } else {
                    Log::error("PayPalService::getSubscriptionDaysLeft() :\n" . json_encode($subscription));
                }
            }
        }

        return null;
    }

    public static function getSubscriptionStatus()
    {
        $provider = self::getPaypalProvider();
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            // when lifetime
            if ($activeSub->stripe_status == 'paypal_approved') {
                // TODO: we can renew from here or from command
                return true;
            } else {
                $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);
                if (isset($subscription['error'])) {
                    Log::error("PayPalService::getSubscriptionStatus() :\n" . json_encode($subscription));

                    return back()->with(['message' => 'PayPal Gateway : ' . $subscription['error']['message'], 'type' => 'error']);
                }
                if ($subscription['status'] == 'ACTIVE') {
                    return true;
                } else {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = \Carbon\Carbon::now();
                    $activeSub->save();

                    return false;
                }
            }
        }

        return null;
    }

    public static function createPayPalOrder(Request $request)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $provider = self::getPaypalProvider($gateway);

        try {
            $couponID = $request->input('couponID', null);
            $planID = $request->input('plan_id', null);
            $plan = Plan::where('id', $planID)->first();
            $newDiscountedPrice = $plan->price;
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($newDiscountedPrice, $taxRate);
            if ($couponID) {
                $coupon = Coupon::where('code', $couponID)->first();
                if ($coupon) {
                    $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                    if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                        $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                    }
                }
            }
            if ($taxValue > 0) {
                $newDiscountedPrice += $taxValue;
            }
            $data = [
                'intent'         => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => $currency,
                            'value'         => strval($newDiscountedPrice),
                        ],
                    ],
                ],
            ];
            $order = $provider->createOrder($data);

            return response()->json(['id' => $order['id']]);
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> createPayPalOrder(): ' . $ex->getMessage());

            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }

        $plan = Plan::where('id', $request->plan_id)->first();
        $newPrice = $plan->price;

        $previousRequest = app('request')->create(url()->previous());
        if ($previousRequest->has('coupon')) {
            $coupon = Coupon::where('code', $previousRequest->input('coupon'))->first();
            if ($coupon) {
                $newPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
            }
        }

        $user = Auth::user();
        $settings = Setting::getCache();

        $provider = self::getPaypalProvider();

        $data = [
            'intent'         => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $request->currency,
                        'value'         => strval($newPrice),
                    ],
                ],
            ],
        ];

        $order = $provider->createOrder($data);

        $orderId = $order['id'];

        $payment = new UserOrder;
        $payment->order_id = $orderId;
        $payment->plan_id = $plan->id;
        $payment->type = 'prepaid';
        $payment->user_id = $user->id;
        $payment->payment_type = 'PayPal';
        $payment->price = $newPrice;
        $payment->affiliate_earnings = ($newPrice * $settings->affiliate_commission_percentage) / 100;
        $payment->status = 'Waiting';
        $payment->country = $user->country ?? 'Unknown';
        $payment->save();

        return $order;

    }

    // webhook functions
    public static function createWebhook()
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $provider = self::getPaypalProvider($gateway);

        try {
            $user = Auth::user();
            $webhooks = $provider->listWebHooks();
            if (count($webhooks['webhooks']) > 0) {
                // There is/are webhook(s) defined. Remove existing.
                foreach ($webhooks['webhooks'] as $key => $hook) {
                    $provider->deleteWebHook($hook['id']);
                }
            }
            // Create new webhook
            $url = url('/') . '/webhooks/paypal';
            $events = [
                'PAYMENT.SALE.COMPLETED',          // A payment is made on a subscription.
                'BILLING.SUBSCRIPTION.CANCELLED',   // A subscription is cancelled.
            ];
            // 'BILLING.SUBSCRIPTION.EXPIRED',      # A subscription expires.
            // 'BILLING.SUBSCRIPTION.SUSPENDED'     # A subscription is suspended.
            $response = $provider->createWebHook($url, $events);
            $gateway->webhook_id = $response['id'];
            $gateway->save();
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> createWebhook(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    private static function verifyIncomingJson(Request $request)
    {

        try {
            $gateway = Gateways::where('code', 'paypal')->first();

            if ($gateway->mode == 'sandbox') {
                // Paypal does not support verification on sandbox mode
                return true;
            }

            if ($request->hasHeader('PAYPAL-AUTH-ALGO') == true) {
                $auth_algo = $request->header('PAYPAL-AUTH-ALGO');
            } else {
                return false;
            }

            if ($request->hasHeader('PAYPAL-CERT-URL') == true) {
                $cert_url = $request->header('PAYPAL-CERT-URL');
            } else {
                return false;
            }

            if ($request->hasHeader('PAYPAL-TRANSMISSION-ID') == true) {
                $transmission_id = $request->header('PAYPAL-TRANSMISSION-ID');
            } else {
                return false;
            }

            if ($request->hasHeader('PAYPAL-TRANSMISSION-SIG') == true) {
                $transmission_sig = $request->header('PAYPAL-TRANSMISSION-SIG');
            } else {
                return false;
            }

            if ($request->hasHeader('PAYPAL-TRANSMISSION-TIME') == true) {
                $transmission_time = $request->header('PAYPAL-TRANSMISSION-TIME');
            } else {
                return false;
            }

            $webhook_event = $request->getContent();
            if ($webhook_event == null) {
                return false;
            }
            if (json_validate($webhook_event) == false) {
                return false;
            }

            $webhook_id = $gateway->webhook_id;
            if ($webhook_id == null) {
                return false;
            }

            $data = [
                'auth_algo'         => $auth_algo,
                'cert_url'          => $cert_url,
                'transmission_id'   => $transmission_id,
                'transmission_sig'  => $transmission_sig,
                'transmission_time' => $transmission_time,
                'webhook_id'        => $webhook_id,
                'webhook_event'     => $webhook_event,
            ];

            $provider = self::getPaypalProvider();

            $response = $provider->verifyWebHook($data);

            if (json_decode($response)->verification_status == 'SUCCESS') {
                return true;
            }

        } catch (Exception $th) {
            Log::error('(Webhooks) PayPalService::verifyIncomingJson(): ' . $th->getMessage());
        }

        return false;
    }

    public static function handleWebhook(Request $request)
    {

        $verified = self::verifyIncomingJson($request);
        if ($verified == true) {
            // Retrieve the JSON payload
            $payload = $request->getContent();
            // Fire the event with the payload
            event(new PayPalWebhookEvent($payload));

            return response()->json(['success' => true]);
        } else {
            // Incoming json is NOT verified
            abort(404);
        }
    }

    public static function simulateWebhookEvent()
    {
        $url = url('/') . '/webhooks/paypal';
        $testJson = [
            'event_type'       => 'PAYMENT.SALE.COMPLETED',
            'url'              => $url,
            'resource_version' => '1.0',
        ];
        $provider = self::getPaypalProvider();
        $filters = [
            'start_date' => Carbon::now()->subDays(7)->toIso8601String(),
            'end_date'   => Carbon::now()->addDays(2)->toIso8601String(),
        ];

        return $provider->listTransactions($filters);
    }

    // helper private functions
    public static function getPaypalProvider($gateway = null)
    {
        $gateway = $gateway ?? Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $settings = Setting::getCache();
        $config = null;
        if ($gateway->mode == 'sandbox') {
            $config = [
                'mode'    => 'sandbox',
                'sandbox' => [
                    'client_id'     => $gateway->sandbox_client_id,
                    'client_secret' => $gateway->sandbox_client_secret,
                    'app_id'        => $gateway->live_app_id,
                ],

                'payment_action' => 'Sale',
                'currency'       => $currency,
                'notify_url'     => $settings->site_url . '/paypal/notify',
                'locale'         => $gateway->currency_locale,
                'validate_ssl'   => false,
            ];
        } else {
            $config = [
                'mode' => 'live',
                'live' => [
                    'client_id'     => $gateway->live_client_id,
                    'client_secret' => $gateway->live_client_secret,
                    'app_id'        => $gateway->live_app_id,
                ],

                'payment_action' => 'Sale',
                'currency'       => $currency,
                'notify_url'     => $settings->site_url . '/paypal/notify',
                'locale'         => $gateway->currency_locale,
                'validate_ssl'   => true,
            ];
        }
        $provider = new PayPalClient($config);
        $provider->getAccessToken();

        return $provider;
    }

    private static function getPaypalProductId($plan)
    {
        if ($plan != null) {
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product != null) {
                return $product->product_id;
            } else {
                return null;
            }
        }

        return null;
    }

    private static function getPaypalPriceId($plan)
    {
        if ($plan != null) {
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product != null) {
                return $product->price_id;
            } else {
                return null;
            }
        }

        return null;
    }

    private static function updateUserData()
    {
        // Since price id (billing plan) is changed, we must update user data, i.e cancel current subscriptions.
        $history = OldGatewayProducts::where([
            'gateway_code' => self::$GATEWAY_CODE,
            'status'       => 'check',
        ])->get();
        if ($history) {
            $provider = self::getPaypalProvider();
            foreach ($history as $record) {
                // check record current status from gateway
                $lookingFor = $record->old_price_id; // billingPlan id in paypal
                // if active disable it
                $oldBillingPlan = $provider->deactivatePlan($lookingFor);
                if ($oldBillingPlan == '') {
                    // deactivated billing plan from gateway
                } else {
                    Log::error(self::$GATEWAY_CODE . "-> updateUserData(): \n" . json_encode($oldBillingPlan));
                }
                // search subscriptions for record
                $subs = Subscriptions::where([
                    'paid_with'     => self::$GATEWAY_CODE,
                    'stripe_status' => 'active',
                    'stripe_price'  => $lookingFor,
                ])->get();
                if ($subs != null) {
                    foreach ($subs as $sub) {
                        // if found get order id
                        $orderId = $sub->stripe_id;
                        // cancel subscription order from gateway
                        $response = $provider->cancelSubscription($orderId, 'New plan created by admin.');
                        // cancel subscription from our database
                        $sub->stripe_status = 'cancelled';
                        $sub->ends_at = \Carbon\Carbon::now();
                        $sub->save();
                    }
                }
                $record->status = 'checked';
                $record->save();
            }
        }

    }

    private static function createBillingPlanData($productId, $productName, $trials, $currency, $interval, $price, $tax, $discountedPrice = null)
    {
        if ($trials == 0) {
            if ($discountedPrice != null) {
                $planData = [
                    'product_id'     => $productId,
                    'name'           => $productName,
                    'description'    => 'Billing Plan of ' . $productName,
                    'status'         => 'ACTIVE',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit'  => $interval,
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'TRIAL',
                            'sequence'       => 1,
                            'total_cycles'   => 1,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => strval($discountedPrice),
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                        [
                            'frequency' => [
                                'interval_unit'  => $interval,
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'REGULAR',
                            'sequence'       => $discountedPrice == null ? 1 : 2,
                            'total_cycles'   => 0,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => strval($price),
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee'             => [
                            'value'         => '0',
                            'currency_code' => $currency,
                        ],
                        'setup_fee_failure_action'  => 'CANCEL',
                        'payment_failure_threshold' => 3,
                    ],
                ];
            } else {
                $planData = [
                    'product_id'     => $productId,
                    'name'           => $productName,
                    'description'    => 'Billing Plan of ' . $productName,
                    'status'         => 'ACTIVE',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit'  => $interval,
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'REGULAR',
                            'sequence'       => $discountedPrice == null ? 1 : 2,
                            'total_cycles'   => 0,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => strval($price),
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee'             => [
                            'value'         => '0',
                            'currency_code' => $currency,
                        ],
                        'setup_fee_failure_action'  => 'CANCEL',
                        'payment_failure_threshold' => 3,
                    ],
                ];
            }
        } else {
            if ($discountedPrice != null) {
                $planData = [
                    'product_id'     => $productId,
                    'name'           => $productName,
                    'description'    => 'Billing Plan of ' . $productName,
                    'status'         => 'ACTIVE',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit'  => 'DAY',
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'TRIAL',
                            'sequence'       => 1,
                            'total_cycles'   => $trials,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => 0,
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                        [
                            'frequency' => [
                                'interval_unit'  => $interval,
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'TRIAL',
                            'sequence'       => 2,
                            'total_cycles'   => 1,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => strval($discountedPrice),
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                        [
                            'frequency' => [
                                'interval_unit'  => $interval,
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'REGULAR',
                            'sequence'       => $discountedPrice == null ? 2 : 3,
                            'total_cycles'   => 0,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => strval($price),
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee'             => [
                            'value'         => '0',
                            'currency_code' => $currency,
                        ],
                        'setup_fee_failure_action'  => 'CANCEL',
                        'payment_failure_threshold' => 3,
                    ],
                ];
            } else {
                $planData = [
                    'product_id'     => $productId,
                    'name'           => $productName,
                    'description'    => 'Billing Plan of ' . $productName,
                    'status'         => 'ACTIVE',
                    'billing_cycles' => [
                        [
                            'frequency' => [
                                'interval_unit'  => 'DAY',
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'TRIAL',
                            'sequence'       => 1,
                            'total_cycles'   => $trials,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => 0,
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                        [
                            'frequency' => [
                                'interval_unit'  => $interval,
                                'interval_count' => 1,
                            ],
                            'tenure_type'    => 'REGULAR',
                            'sequence'       => $discountedPrice == null ? 2 : 3,
                            'total_cycles'   => 0,
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'value'         => strval($price),
                                    'currency_code' => $currency,
                                ],
                            ],
                        ],
                    ],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee'             => [
                            'value'         => '0',
                            'currency_code' => $currency,
                        ],
                        'setup_fee_failure_action'  => 'CANCEL',
                        'payment_failure_threshold' => 3,
                    ],
                ];
            }
        }
        if ($tax > 0) {
            $planData['taxes'] = [
                'percentage' => $tax,
                'inclusive'  => false,
            ];
        }

        return $planData;
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            'code'                  => 'paypal',
            'title'                 => 'PayPal',
            'link'                  => 'https://www.paypal.com/',
            'active'                => 0,
            'available'             => 1,
            'img'                   => '/assets/img/payments/paypal.svg',
            'whiteLogo'             => 0,
            'mode'                  => 1,
            'sandbox_client_id'     => 1,
            'sandbox_client_secret' => 1,
            'sandbox_app_id'        => 0,
            'live_client_id'        => 1,
            'live_client_secret'    => 1,
            'live_app_id'           => 1,
            'currency'              => 1,
            'currency_locale'       => 1,
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
