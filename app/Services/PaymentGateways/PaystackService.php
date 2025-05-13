<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Actions\EmailPaymentConfirmation;
use App\Enums\Plan\FrequencyEnum;
use App\Enums\Plan\TypeEnum;
use App\Events\PaystackWebhookEvent;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\CustomBilingPlans;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\OldGatewayProducts;
use App\Models\PaystackPaymentInfo;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\UserOrder;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;

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
class PaystackService
{
    use CreditUpdater;

    protected static $GATEWAY_CODE = 'paystack';

    protected static $GATEWAY_NAME = 'PayStack';

    protected static $client = 'https://api.paystack.co/';

    protected static $product_endpoint = 'product';

    protected static $plan_endpoint = 'plan';

    protected static $subscription_endpoint = 'subscription';

    protected static $transaction_verify_endpoint = 'transaction/verify/';

    // payment functions
    // tested
    public static function saveAllProducts()
    {
        try {
            // Get all membership plans
            $plans = Plan::where('active', 1)->get();
            foreach ($plans as $plan) {
                self::saveProduct($plan);
            }
        } catch (Exception $ex) {
            Log::error("paystack::saveAllProducts()\n" . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }

    }

    // tested
    public static function saveProduct($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);

        try {
            // 1 begain db transaction
            DB::beginTransaction();
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $taxValue = taxToVal($plan->price, $gateway->tax);
            $total = $plan->price + $taxValue;
            $price = (int) (((float) $total) * 100); // Must be in cents level for paystack
            $key = self::getKey($gateway);
            $oldProductId = null;
            // 2 check if product exists. No->create, Yes->create->assign old to $oldProductId. (Create product in every situation. maybe user updated stripe credentials.)
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();

            $data = [
                'name'        => $plan->name,
                'description' => $plan->name,
                'price'       => $price == 0 ? 1000 : $price,
                'currency'    => $currency,
            ];
            // Create product in every situation. maybe user updated paystack credentials.
            $newProduct = self::curl_req(self::$product_endpoint, $key, $data);
            if ($product != null) {
                if ($product->product_id != null) {
                    $oldProductId = $product->product_id; // Product has been created before
                } // ELSE Product has not been created before but record exists. Create new product and update record.
            } else {
                $product = new GatewayProducts;
                $product->plan_id = $plan->id;
                $product->gateway_code = self::$GATEWAY_CODE;
                $product->gateway_title = self::$GATEWAY_NAME;
            }
            $product->product_id = $newProduct['data']['product_code'];
            $product->plan_name = $plan->name;
            $product->save();
            // if not lifetime or free or onetime then create priceID
            if ($plan->price != 0 && $plan->type == TypeEnum::SUBSCRIPTION->value && $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value && $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value) {
                $interval = $plan->frequency == FrequencyEnum::MONTHLY->value ? FrequencyEnum::MONTHLY->value : 'annually';
                $billingPlan = self::curl_req(self::$plan_endpoint, $key, [
                    'name'        => $plan->name,
                    'interval'    => $interval,
                    'amount'      => $price,
                    'description' => $product->product_id,
                    'currency'    => $currency,
                ]);
                if ($product->price_id != null) {
                    $history = new OldGatewayProducts;
                    $history->plan_id = $plan->id;
                    $history->plan_name = $plan->name;
                    $history->gateway_code = self::$GATEWAY_CODE;
                    $history->product_id = $product->product_id;
                    $history->old_product_id = $oldProductId;
                    $history->old_price_id = $product->price_id;
                    $history->new_price_id = $billingPlan['data']['plan_code'];
                    $history->status = 'check';
                    $history->save();
                    $tmp = self::updateUserData();
                }
                $product->price_id = $billingPlan['data']['plan_code'];
            } else {
                $product->price_id = 'Not Needed';
            }
            $product->save();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . "-> saveProduct():\n" . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    // tested
    public static function subscribe($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);

        try {
            DB::beginTransaction();
            $planId = $plan->id;
            $settings = Setting::getCache();
            $exception = null;
            $orderId = Str::random(12);
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $key = self::getKey($gateway);
            $user = auth()->user();
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($plan->price, $taxRate);
            $coupon = checkCouponInRequest(); // if there a coupon in request it will return the coupin instanse

            $productId = self::getPaystackProductId($plan->id);
            $billingPlanId = self::getPaystackPriceId($plan->id);
            if ($productId == null) {
                $exception = __('Product ID is not set! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            if ($billingPlanId == null) {
                $exception = __('Product Price ID is not set! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            $newDiscountedPrice = $plan->price + $taxValue; // total with tax
            if ($coupon && $plan->price != 0) {
                $newDiscountedPrice -= ($plan->price * ($coupon->discount / 100));
                if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
                if ($plan->price != 0 && $plan->type == TypeEnum::SUBSCRIPTION->value && $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value && $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value) {
                    $interval = $plan->frequency == FrequencyEnum::MONTHLY->value ? FrequencyEnum::MONTHLY->value : 'annually';
                    $billingPlan = self::curl_req(self::$plan_endpoint, $key, [
                        'name'        => 'discount_item_' . time(),
                        'interval'    => $interval,
                        'amount'      => (int) (((float) $newDiscountedPrice) * 100),
                        'description' => 'coupon_' . $coupon->code . '_user_' . $user->id . '_plan_' . $plan->id,
                        'currency'    => $currency,
                    ]);
                    $billingPlanId = $billingPlan['data']['plan_code'];
                }
                // remove tax when coupon is applied because its included in the plan price.
                $newDiscountedPrice -= $taxValue;
            }
            $payment = new UserOrder;
            $payment->order_id = $orderId;
            $payment->plan_id = $plan->id;
            $payment->user_id = $user->id;
            $payment->payment_type = self::$GATEWAY_CODE;
            $payment->price = $newDiscountedPrice;
            $payment->affiliate_earnings = ($newDiscountedPrice * $settings->affiliate_commission_percentage) / 100;
            $payment->status = 'Waiting';
            $payment->country = $user->country ?? 'Unknown';
            $payment->save();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return back()->with(['message' => Str::before($ex->getMessage(), ':'), 'type' => 'error']);
        }

        return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE, compact('plan', 'taxRate', 'taxValue', 'newDiscountedPrice', 'billingPlanId', 'exception', 'orderId', 'productId', 'gateway', 'planId'));
    }

    // tested
    public static function subscribeCheckout(Request $request, $referral = null)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);

        try {
            $planId = $request->input('planID', null);
            $couponID = $request->input('couponID', null);
            $orderId = $request->input('orderID', null);
            $billingPlanId = $request->input('billingPlanId', null);
            $productId = $request->input('productID', null);
            DB::beginTransaction();

            $payment_response = json_decode($request->response, true);
            $payment_response_status = $payment_response['status'];
            $payment_response_message = $payment_response['message'];
            $payment_response_reference = $payment_response['reference'];

            $plan = Plan::where('id', $planId)->first();
            $payment = UserOrder::where('order_id', $orderId)->first();
            $user = auth()->user();

            $product = GatewayProducts::where(['plan_id' => $plan->id, 'product_id' => $productId, 'gateway_code' => self::$GATEWAY_CODE])->first();
            // check if $product->price_id != to $billingPlanId, if not thats means its custom plan then save it in custom plans table.
            if ($product->price_id != $billingPlanId) {
                $newcustom = new CustomBilingPlans;
                $newcustom->gateway = self::$GATEWAY_CODE;
                $newcustom->plan_id = $planId;
                $newcustom->main_plan_price_id = $product->price_id;
                $newcustom->custom_plan_price_id = $billingPlanId;
                $newcustom->save();
            }

            if ($gateway->mode == 'sandbox') {
                $key = $gateway->sandbox_client_secret;
            } else {
                $key = $gateway->live_client_secret;
            }

            // verify transaction with paystack if it was successful then continue
            $reqs = self::curl_req_get(self::$transaction_verify_endpoint . $payment_response_reference, $key);

            if ($reqs['status'] == false) { // if something went wrong with the request
                abort(404);
            }
            // failed or success
            if ($reqs['data']['status'] != 'success') { // if the transaction was not successful
                abort(400, $reqs['data']['gateway_response']);
            }

            // log the transaction data to database
            $info = new PaystackPaymentInfo;
            $info->user_id = $user->id;
            $info->email = $user->email;
            $info->reference = $payment_response['reference'] ?? '';
            $info->trans = $payment_response['trans'] ?? '';
            $info->status = $payment_response['status'] ?? '';
            $info->message = $payment_response['message'] ?? '';
            $info->transaction = $payment_response['transaction'] ?? '';
            $info->trxref = $payment_response['trxref'] ?? '';
            $info->amount = ($reqs['data']['amount'] / 100) ?? '';
            $info->customer_code = $reqs['data']['customer']['customer_code'] ?? '';
            $info->plan_code = ($reqs['data']['plan'] ?? '') . ' / ' . $planId;
            $info->currency = $reqs['data']['currency'] ?? '';
            $info->other = $reqs['data']['paidAt'] ?? '';
            $info->save();

            if ($payment != null) {

                $total = $plan->price;
                $taxValue = taxToVal(($plan->price - $gateway->tax), $gateway->tax);
                if ($couponID) {
                    $coupon = Coupon::where('code', $couponID)->first();
                    if ($coupon) {
                        $coupon->usersUsed()->attach($user->id);
                        $couponID = $coupon->discount;
                        $total -= ($plan->price * ($coupon->discount / 100));
                        if ($total != floor($total)) {
                            $total = number_format($total, 2);
                        }
                    }
                }
                $subscription = new Subscriptions;
                // if its lifetime subscription
                if ($billingPlanId == 'Not Needed') {
                    $subscription->stripe_id = 'PSLS-' . strtoupper(Str::random(13));
                    $subscription->stripe_price = $product->price_id;
                    $subscription->ends_at = $plan->frequency == FrequencyEnum::LIFETIME_MONTHLY->value ? \Carbon\Carbon::now()->addMonths(1) : \Carbon\Carbon::now()->addYears(1);
                    $subscription->auto_renewal = 1;
                    $subscription->stripe_status = 'paystack_approved';
                } else {
                    $bill_customer_id = $reqs['data']['customer']['id'];
                    $bill_plan_id = $reqs['data']['plan_object']['id'];
                    $subscription_billing = self::curl_req_get(self::$subscription_endpoint . '?customer=' . $bill_customer_id . '&plan=' . $bill_plan_id, $key);
                    if ($subscription_billing['status'] == false) { // if something went wrong with the request
                        abort(404);
                    }
                    $subscription_billing_code = $subscription_billing['data'][0]['subscription_code'];

                    $subscription->stripe_id = $subscription_billing_code;
                    $subscription->stripe_price = $billingPlanId;
                    $subscription->stripe_status = 'active';
                    $subscription->ends_at = $plan->trial_days != 0 ? \Carbon\Carbon::now()->addDays($plan->trial_days) : \Carbon\Carbon::now()->addDays(30);
                }

                $subscription->user_id = $user->id;
                $subscription->name = $planId;
                $subscription->quantity = 1;
                $subscription->plan_id = $planId;
                $subscription->paid_with = self::$GATEWAY_CODE;
                $subscription->tax_rate = $gateway->tax;
                $subscription->tax_value = $taxValue;
                $subscription->coupon = $couponID;
                $subscription->total_amount = $total;
                $subscription->save();

                $payment->status = 'Success';
                $payment->save();

                self::creditIncreaseSubscribePlan($user, $plan);

                CreateActivity::for($user, __('Subscribed'), $plan->name . ' ' . __('Plan'));
                EmailPaymentConfirmation::create($user, $plan)->send();
                \App\Models\Usage::getSingle()->updateSalesCount($total);
                DB::commit();

                if (class_exists('App\Extensions\Affilate\System\Events\AffiliateEvent')) {
                    event(new \App\Extensions\Affilate\System\Events\AffiliateEvent($total, $gateway->currency));
                }

                return redirect()->route('dashboard.' . $user->type->value . '.index')->with(['message' => __('Thank you for your purchase. Enjoy your remaining words and images.'), 'type' => 'success']);

            } else {
                DB::rollBack();
                $msg = 'PaystackController::subscribePay(): Could not find required payment order!';
                Log::error($msg);

                return redirect()->route('dashboard.' . $user->type->value . '.index')->with(['message' => $msg, 'type' => 'error']);
            }

        } catch (Exception $th) {
            DB::rollBack();
            Log::error('PaystackController::subscribePay(): ' . $th->getMessage());

            return redirect()->route('dashboard.' . $user->type->value . '.index')->with(['message' => $th->getMessage(), 'type' => 'error']);
        }
    }

    // tested
    public static function prepaid($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);

        try {
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($plan->price, $taxRate);

            $newDiscountedPrice = $plan->price;
            $couponCode = request()->input('coupon');
            if ($couponCode) {
                $coupone = Coupon::where('code', $couponCode)->first();
                if ($coupone) {
                    $newDiscountedPrice = $plan->price - ($plan->price * ($coupone->discount / 100));
                    if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                        $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                    }
                }
                // remove tax when coupon is applied because its included in the plan price.
                $newDiscountedPrice -= $taxValue;
            }
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $orderId = null;
            $exception = null;
            if (self::getPaystackProductId($plan->id) == null) {
                $exception = 'Product ID is not set! Please save Membership Plan again.';
            }
        } catch (Exception $th) {
            $exception = Str::before($th->getMessage(), ':');
        }

        return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate', 'orderId', 'gateway', 'exception', 'currency'));
    }

    // tested
    public static function prepaidCheckout(Request $request)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);

        try {
            DB::beginTransaction();
            $previousRequest = app('request')->create(url()->previous());
            $payment_response = json_decode($request->response, true);
            $payment_response_reference = $payment_response['reference'];

            if ($gateway->mode == 'sandbox') {
                $key = $gateway->sandbox_client_secret;
            } else {
                $key = $gateway->live_client_secret;
            }

            // verify transaction with paystack if it was successful then continue
            $reqs = self::curl_req_get(self::$transaction_verify_endpoint . $payment_response_reference, $key);
            if ($reqs['status'] == false) { // if something went wrong with the request
                abort(404);
            }
            // failed or success
            if ($reqs['data']['status'] != 'success') { // if the transaction was not successful
                abort(400, $reqs['data']['gateway_response']);
            }
            $user = auth()->user();
            // log the transaction data to database
            $info = new PaystackPaymentInfo;
            $info->user_id = $user->id;
            $info->email = $user->email;
            $info->reference = $payment_response['reference'] ?? '';
            $info->trans = $payment_response['trans'] ?? '';
            $info->status = $payment_response['status'] ?? '';
            $info->message = $payment_response['message'] ?? '';
            $info->transaction = $payment_response['transaction'] ?? '';
            $info->trxref = $payment_response['trxref'] ?? '';

            $info->amount = ($reqs['data']['amount'] / 100) ?? '';
            $info->customer_code = $reqs['data']['customer']['customer_code'] ?? '';
            $info->plan_code = ($reqs['data']['plan'] ?? '') . ' / ' . $request->planID;
            $info->currency = $reqs['data']['currency'] ?? '';
            $info->other = $reqs['data']['paidAt'] ?? '';
            $info->save();

            $plan = Plan::find($request->planID);
            $user = Auth::user();
            $settings = Setting::getCache();

            $newDiscountedPrice = $plan->price;
            if ($previousRequest->has('coupon')) {
                $coupon = Coupon::where('code', $previousRequest->input('coupon'))->first();
                if ($coupon) {
                    $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                    $coupon->usersUsed()->attach($user->id);
                }
            }

            $payment = new UserOrder;
            $payment->order_id = Str::random(12);
            $payment->plan_id = $plan->id;
            $payment->type = 'prepaid';
            $payment->user_id = $user->id;
            $payment->payment_type = 'Credit, Debit Card';
            $payment->price = $newDiscountedPrice;
            $payment->affiliate_earnings = ($newDiscountedPrice * $settings->affiliate_commission_percentage) / 100;
            $payment->status = 'Success';
            $payment->country = $user->country ?? 'Unknown';
            $payment->save();

            self::creditIncreaseSubscribePlan($user, $plan);

            CreateActivity::for($user, __('Purchased'), $plan->name . ' ' . __('Token Pack'));
            EmailPaymentConfirmation::create($user, $plan)->send();
            \App\Models\Usage::getSingle()->updateSalesCount($newDiscountedPrice);
            DB::commit();

            return redirect()->route('dashboard.' . $user->type->value . '.index')->with(['message' => __('Thank you for your purchase. Enjoy your remaining words and images.'), 'type' => 'success']);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error('PaystackController::subscribePay(): ' . $th->getMessage());

            return redirect()->route('dashboard.' . $user->type->value . '.index')->with(['message' => $th->getMessage(), 'type' => 'error']);
        }

    }

    // tested
    public static function getSubscriptionDaysLeft()
    {
        $gateway = Gateways::where('code', 'paystack')->first();
        if ($gateway == null) {
            return null;
        }
        if ($gateway->mode == 'sandbox') {
            $key = $gateway->sandbox_client_secret;
        } else {
            $key = $gateway->live_client_secret;
        }
        $user = auth()->user();
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($user->id);
        if ($activeSub != null) {
            if ($activeSub->stripe_price != 'Not Needed') {
                $reqs = self::curl_req_get(self::$subscription_endpoint . '/' . $activeSub->stripe_id, $key);
                if ($reqs['status'] == false) { // if something went wrong with the request
                    Log::error("PaystackController::getSubscriptionRenewDate() :\n" . json_encode($reqs));

                    return back()->with(['message' => 'Paystack Gateway : ' . json_encode($reqs), 'type' => 'error']);
                }
                if (isset($reqs['data']['next_payment_date'])) {
                    // return \Carbon\Carbon::parse($reqs['data']['next_payment_date'])->format('F jS, Y');
                    return \Carbon\Carbon::now()->diffInDays($reqs['data']['next_payment_date']);
                }
            } else {
                return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($activeSub->ends_at));
            }
        }

        return null;
    }

    // tested
    public static function getSubscriptionRenewDate()
    {
        $gateway = Gateways::where('code', 'paystack')->first();
        if ($gateway == null) {
            return null;
        }
        if ($gateway->mode == 'sandbox') {
            $key = $gateway->sandbox_client_secret;
        } else {
            $key = $gateway->live_client_secret;
        }
        $user = auth()->user();
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($user->id);
        if ($activeSub != null) {
            if ($activeSub->stripe_price != 'Not Needed') {
                $reqs = self::curl_req_get(self::$subscription_endpoint . '/' . $activeSub->stripe_id, $key);
                if ($reqs['status'] == false) { // if something went wrong with the request
                    Log::error("PaystackController::getSubscriptionRenewDate() :\n" . json_encode($reqs));

                    return back()->with(['message' => 'Paystack Gateway : ' . json_encode($reqs), 'type' => 'error']);
                }
                if (isset($reqs['data']['next_payment_date'])) {
                    return \Carbon\Carbon::parse($reqs['data']['next_payment_date'])->format('F jS, Y');
                } else {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = \Carbon\Carbon::now();
                    $activeSub->save();

                    return \Carbon\Carbon::now()->format('F jS, Y');
                }
            } else {
                return \Carbon\Carbon::createFromTimeStamp($activeSub->ends_at)->format('F jS, Y');
            }
        }

        return null;
    }

    // tested
    public static function getSubscriptionStatus()
    {
        $gateway = Gateways::where('code', 'paystack')->first();
        if ($gateway == null) {
            return null;
        }
        if ($gateway->mode == 'sandbox') {
            $key = $gateway->sandbox_client_secret;
        } else {
            $key = $gateway->live_client_secret;
        }
        $user = auth()->user();
        $activeSub = getCurrentActiveSubscription($user->id);
        if ($activeSub != null) {
            if ($activeSub->stripe_price != 'Not Needed') {
                $reqs = self::curl_req_get(self::$subscription_endpoint . '/' . $activeSub->stripe_id, $key);
                if ($reqs['status'] == false) { // if something went wrong with the request
                    Log::error("PaystackController::getSubscriptionStatus() :\n" . json_encode($reqs));

                    return back()->with(['message' => 'Paystack Gateway : ' . json_encode($reqs), 'type' => 'error']);
                }
                if ($reqs['data']['status'] == 'active') {
                    return true;
                } else {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = \Carbon\Carbon::now();
                    $activeSub->save();

                    return false;
                }
            } else {
                return true;
            }
        }

        return null;
    }

    // tested
    public static function cancelSubscribedPlan($planId, $subsId)
    {
        $currentSubscription = Subscriptions::where('id', $subsId)->first();
        if ($currentSubscription != null) {
            $plan = Plan::where('id', $planId)->first();
            $gateway = Gateways::where('code', 'paystack')->first();
            if ($gateway == null) {
                return null;
            }

            if ($gateway->mode == 'sandbox') {
                $key = $gateway->sandbox_client_secret;
            } else {
                $key = $gateway->live_client_secret;
            }

            if ($currentSubscription->stripe_price != 'Not Needed') {
                $get_subscribe_info = self::curl_req_get(self::$cancelSubscribedPlan . '/' . $currentSubscription->stripe_id, $key);
                if ($get_subscribe_info['status'] == false) { // if something went wrong with the request
                    Log::error("PaystackController::cancelSubscribedPlan() :\n" . json_encode($get_subscribe_info));

                    return back()->with(['message' => 'Paystack Gateway : ' . json_encode($get_subscribe_info), 'type' => 'error']);
                }

                $request = self::curl_req(self::$subscription_endpoint . '/disable', $key, [
                    'code'  => $currentSubscription->stripe_id,
                    'token' => $get_subscribe_info['data']['email_token'],
                ]);

                if ($request['status'] == true && $request['message'] == 'Subscription disabled successfully') {
                    $currentSubscription->stripe_status = 'cancelled';
                    $currentSubscription->ends_at = \Carbon\Carbon::now();
                    $currentSubscription->save();

                    return true;
                }
            } else {
                $currentSubscription->stripe_status = 'cancelled';
                $currentSubscription->ends_at = \Carbon\Carbon::now();
                $currentSubscription->save();

                return true;
            }
        }

        return false;
    }

    // tested
    public static function subscribeCancel()
    {
        $user = auth()->user();
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($user->id);
        if ($activeSub != null) {
            $plan = Plan::where('id', $activeSub->plan_id)->first();
            $gateway = Gateways::where('code', 'paystack')->first();
            if ($gateway == null) {
                abort(404);
            }
            if ($gateway->mode == 'sandbox') {
                $key = $gateway->sandbox_client_secret;
            } else {
                $key = $gateway->live_client_secret;
            }
            if ($activeSub->stripe_price != 'Not Needed') {
                $reqs = self::curl_req_get(self::$subscription_endpoint . '/' . $activeSub->stripe_id, $key);
                if ($reqs['status'] == false) { // if something went wrong with the request
                    abort(404, $reqs['message']);
                }
                $mailToken = $reqs['data']['email_token'];
                $request = self::curl_req(self::$subscription_endpoint . '/disable', $key, [
                    'code'  => $activeSub->stripe_id,
                    'token' => $mailToken,
                ]);
                if ($request['status'] == true && $request['message'] == 'Subscription disabled successfully') {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = \Carbon\Carbon::now();
                    $activeSub->save();

                    self::creditDecreaseCancelPlan($user, $plan);

                    CreateActivity::for($user, 'Cancelled', 'Subscription plan');

                    return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
                } else {
                    Log::error('PaystackController::disableOldSubscriptionAndReturnNew(): ' . $request['message']);

                    return back()->with(['message' => __('Your subscription could not cancelled.'), 'type' => 'error']);
                }
            } else {
                $activeSub->stripe_status = 'cancelled';
                $activeSub->ends_at = \Carbon\Carbon::now();
                $activeSub->save();

                self::creditDecreaseCancelPlan($user, $plan);

                CreateActivity::for($user, 'Cancelled', 'Subscription plan');

                return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
            }
        }

        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }

    // tested
    private static function isValidWebhookSignature($input, $secret, $signature)
    {
        $expectedSignature = hash_hmac('sha512', $input, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    // tested
    public static function handleWebhook(Request $request)
    {
        $input = $request->getContent();
        $secret = self::getKey();
        // Validate the Paystack webhook signature
        if (self::isValidWebhookSignature($input, $secret, $request->header('HTTP_X_PAYSTACK_SIGNATURE'))) {
            // Handle the webhook events
            $payload = json_decode($input, true);
            event(new PaystackWebhookEvent($payload));
        }

        // Invalid signature
        return response()->json(['status' => 'error'], 400);
    }

    // tested
    /**
     * curl post request template
     */
    public static function curl_req($second_url, $key, $data = [])
    {
        $fields_string = http_build_query($data);
        // open connection
        $ch = curl_init();
        // set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, self::$client . $second_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $key,
            'Cache-Control: no-cache',
        ]);
        // So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // execute post
        $request = curl_exec($ch);
        curl_close($ch);
        if ($request) {
            $result = json_decode($request, true);
            if (isset($result['status']) && $result['status'] !== true) {
                abort(400, 'Paystack: ' . $result['message']);
            }

            return $result;
        } else {
            abort(400, $result);
        }
    }

    // tested
    /**
     * curl get request template
     */
    public static function curl_req_get($param, $key)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::$client . $param,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $key,
                'Cache-Control: no-cache',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            abort(400, 'Paystack: ' . $err);
        } else {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] !== true) {
                abort(400, 'Paystack: ' . $result['message']);
            }

            return $result;
        }
    }

    // tested
    /**
     * Reads GatewayProducts table and returns price id of the given plan
     */
    public static function getPaystackPriceId($planId)
    {

        // check if plan exists
        $plan = Plan::where('id', $planId)->first();
        if ($plan != null) {
            $product = GatewayProducts::where(['plan_id' => $planId, 'gateway_code' => 'paystack'])->first();
            if ($product != null) {
                return $product->price_id;
            } else {
                return null;
            }
        }

        return null;
    }

    // tested
    /**
     * Reads GatewayProducts table and returns price id of the given plan
     */
    public static function getPaystackProductId($planId)
    {

        // check if plan exists
        $plan = Plan::where('id', $planId)->first();
        if ($plan != null) {
            $product = GatewayProducts::where(['plan_id' => $planId, 'gateway_code' => 'paystack'])->first();
            if ($product != null) {
                return $product->product_id;
            } else {
                return null;
            }
        }

        return null;
    }

    // tested
    /**
     * get key if sadbox or live
     */
    private static function getKey($gateway = null)
    {
        $theGateway = $gateway ?? Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        if ($theGateway->mode == 'sandbox') {
            $key = $theGateway->sandbox_client_secret;
        } else {
            $key = $theGateway->live_client_secret;
        }

        return $key;
    }

    // tested
    /**
     * Since price id (billing plan) is changed, we must update user data, i.e cancel current subscriptions.
     */
    public static function updateUserData()
    {
        $key = self::getKey();

        try {
            $history = OldGatewayProducts::where(['gateway_code' => self::$GATEWAY_CODE, 'status' => 'check'])->get();
            if ($history != null) {
                foreach ($history as $record) {
                    // check record current status from gateway
                    $lookingFor = $record->old_price_id; // billingPlan id in paystack
                    // get also subscription id and customer id and mail token
                    // search subscriptions for record
                    $subs = Subscriptions::where('paid_with', self::$GATEWAY_CODE)
                        ->where('stripe_status', 'active')
                        ->where('stripe_price', $lookingFor)
                        ->get();
                    foreach ($subs ?? [] as $sub) {
                        $subscriptionId = $sub->stripe_id;
                        $reqs = self::curl_req_get(self::$subscription_endpoint . '/' . $subscriptionId, $key);
                        if ($reqs['status'] == false) { // if something went wrong with the request
                            abort(404);
                        }
                        $mailToken = $reqs['data']['email_token'];
                        $customerId = $reqs['data']['customer']['customer_code'];
                        $planId = $reqs['data']['plan']['plan_code'];
                        // cancel old subscription from gateway
                        $new_subscription_code = self::disableOldSubscriptionAndReturnNew($subscriptionId, $mailToken, $customerId, $planId);
                        if ($new_subscription_code == false) {
                            Log::error('PaystackService::updateUserData(): Could not create new subscription for user: ' . $sub->user_id);

                            continue;
                        }
                        $sub->stripe_id = $new_subscription_code;
                        $sub->save();
                    }
                    $record->status = 'checked';
                    $record->save();
                }
            }
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . "-> updateUserData():\n" . $ex->getMessage());

            return ['result' => Str::before($ex->getMessage(), ':')];
        }

    }

    // tested
    public static function disableOldSubscriptionAndReturnNew($subscriptionId, $mail_token, $customerID, $planID)
    {
        $key = self::getKey();
        $request = self::curl_req(self::$subscription_endpoint . '/disable', $key, [
            'code'  => $subscriptionId,
            'token' => $mail_token,
        ]);
        if ($request['status'] == true && $request['message'] == 'Subscription disabled successfully') {
            // create new subscription insted of old one
            $req = self::curl_req(self::$subscription_endpoint, $key, [
                'customer' => $customerID,
                'plan'     => $planID,
            ]);
            if ($req['status'] == false) {
                Log::error('PaystackService::disableOldSubscriptionAndReturnNew(): ' . $req['message']);

                return false;
            }

            return $req['data']['subscription_code'];
        } else {
            Log::error('PaystackService::disableOldSubscriptionAndReturnNew(): ' . $request['message']);

            return false;
        }
    }

    // tested
    public static function checkIfTrial()
    {
        return false;
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            'code'                  => 'paystack',
            'title'                 => 'Paystack',
            'link'                  => 'https://paystack.com/',
            'active'                => 0,
            'available'             => 1,
            'img'                   => '/assets/img/payments/paystack-2.svg',
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
