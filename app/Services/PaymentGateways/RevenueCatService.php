<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Actions\EmailPaymentConfirmation;
use App\Enums\Plan\FrequencyEnum;
use App\Models\Gateways;
use App\Models\RevenueCatProducts;
use App\Models\Setting;
use App\Models\User;
// use App\Models\SubscriptionItems;
use App\Models\UserOrder;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription as Subscriptions;

class RevenueCatService
{
    use CreditUpdater;

    public static function saveAllProducts()
    {
        return null;
    }

    public static function saveProduct()
    {
        return null;
    }

    public static function getSubscriptionDaysLeft()
    {
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            if ($activeSub->stripe_status == 'trialing') {
                return \Carbon\Carbon::parse($activeSub->trial_ends_at)->diffInDays();
            } else {
                return \Carbon\Carbon::parse($activeSub->ends_at)->diffInDays();
            }
        }

        return null;

    }

    public static function getSubscriptionRenewDate()
    {
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            if ($activeSub->stripe_status == 'trialing') {
                return \Carbon\Carbon::parse($activeSub->trial_ends_at)->format('F jS, Y');
            } else {
                return \Carbon\Carbon::parse($activeSub->ends_at)->format('F jS, Y');
            }
        }

        return null;

    }

    public static function getSubscriptionStatus($fromApi = false)
    {

        // / Developer Note:
        // / This functions returns true if user has an active subscription, otherwise returns false and null for errors
        // / If fromApi is true, then it returns json response instead of true/false/null
        // / Api responses are 4 types: 200, 404, 412, 500
        // / 200: Active subscription found
        // / 404: No active subscription found
        // / 412: Error occured
        // / 500: Error occured
        // / Mobile app displays returned error messages for 412 and 500 responses, but not for 404. Hence, do not change the messages and codes for these responses

        $user = Auth::user();
        $userId = $user->id;

        // Since this is RevenueCat (updates are made outside of our system), we need to get the subscription status from the RevenueCat API first
        // Get api key from gateway settings
        $gateway = Gateways::where('code', 'revenuecat')->first();
        if ($gateway == null) {
            Log::error('Gateway not found. RevenueCatService@getSubscriptionStatus');
            if ($fromApi) {
                return response()->json(['message' => 'Gateway not found.'], 412);
            } else {
                return null;
            }
        }

        $apiKey = $gateway->live_client_id;
        if ($apiKey == null) {
            Log::error('Gateway is not set properly. RevenueCatService@getSubscriptionStatus');
            if ($fromApi) {
                return response()->json(['message' => 'Gateway is not set properly.'], 412);
            } else {
                return null;
            }
        }

        $settings = Setting::getCache();

        // Get user's revenuecat id
        $user = Auth::user();
        $userId = $user->id;
        $userRevenueCatId = $user->revenuecat_id;

        if ($userRevenueCatId == null) {
            Log::error('User is not set properly. User must login at least once from mobile app. RevenueCatService@getSubscriptionStatus');
            if ($fromApi) {
                return response()->json(['message' => 'User is not set properly. User must login at least once from mobile app.'], 412);
            } else {
                return null;
            }
        }

        // Get subscription status from RevenueCat API
        $url = 'https://api.revenuecat.com/v1/subscribers/' . $userRevenueCatId;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response == null || $response == '' || $response == '{}') {
            Log::error('Error getting subscription status from RevenueCat API. RevenueCatService@getSubscriptionStatus');
            if ($fromApi) {
                return response()->json(['message' => 'Error getting subscription status from RevenueCat API.'], 500);
            } else {
                return null;
            }
        }

        $response = json_decode($response, true);

        $subscriber = $response['subscriber'];

        // Log::info('RevenueCat API Response: ' . json_encode($response));

        // /////////////////////////
        // / TOKEN PACKS
        // /////////////////////////
        $tokenPacks = $subscriber['non_subscriptions'];

        // check if all token packs are applied and saved to database
        /*
            "non_subscriptions": {
                "onetime": [
                    {
                    "id": "cadba0c81b",
                    "is_sandbox": true,
                    "purchase_date": "2019-04-05T21:52:45Z",
                    "store": "app_store"
                    }
                ],
            },
        */
        foreach ($tokenPacks as $identifier => $purchasedPacksArray) {

            foreach ($purchasedPacksArray as $pack) {

                $orders = UserOrder::where([['user_id', '=', $userId], ['order_id', '=', $pack['id']]])->get();

                if (count($orders) == 0) {
                    // Create order

                    $revenueCatProduct = RevenueCatProducts::where('google_id', $identifier)->first();
                    if ($revenueCatProduct == null) {
                        $revenueCatProduct = RevenueCatProducts::where('apple_id', $identifier)->first();
                        if ($revenueCatProduct == null) {
                            Log::error('RevenueCat product not found. Identifier: ' . $identifier);
                            if ($fromApi) {
                                return response()->json(['message' => 'RevenueCat product not found.'], 412);
                            } else {
                                return null;
                            }
                        }
                    }

                    $gatewayProduct = $revenueCatProduct->gateway_product;
                    if ($gatewayProduct == null) {
                        Log::error('Gateway product not found for RevenueCat. Identifier: ' . $identifier);
                        if ($fromApi) {
                            return response()->json(['message' => 'Gateway product not found.'], 412);
                        } else {
                            return null;
                        }
                    }

                    $plan = $revenueCatProduct->plan;
                    if ($plan == null) {
                        Log::error('Plan not found for RevenueCat. Identifier: ' . $identifier);
                        if ($fromApi) {
                            return response()->json(['message' => 'Plan not found.'], 412);
                        } else {
                            return null;
                        }
                    }

                    $order = new UserOrder;
                    $order->order_id = $pack['id'];
                    $order->plan_id = $plan->id;
                    $order->type = 'prepaid';
                    $order->user_id = $userId;
                    $order->payment_type = 'RevenueCat';
                    $order->price = $plan->price; // users must set coupons at stores - we are not allowed from external source
                    $order->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
                    $order->status = 'Success';
                    $order->country = $user->country ?? 'Unknown';
                    $order->save();

                    self::creditIncreaseSubscribePlan($user, $plan);

                    CreateActivity::for($user, __('Purchased'), $plan->name . ' ' . __('Token Pack'));
                    EmailPaymentConfirmation::create($user, $plan)->send();
                } else {
                    // / Order already exists, do nothing
                }

            }

        }

        // /////////////////////////
        // / SUBSCRIPTIONS
        // /////////////////////////
        $subscriptions = $subscriber['subscriptions'];
        /*
            "rc_promo_pro_cat_monthly": {
                "auto_resume_date": null,
                "billing_issues_detected_at": null,
                "expires_date": "2019-08-26T01:02:16Z",
                "grace_period_expires_date": null,
                "is_sandbox": false,
                "original_purchase_date": "2019-07-26T01:02:16Z",
                "ownership_type": "FAMILY_SHARED",
                "period_type": "normal",
                "purchase_date": "2019-07-26T01:02:16Z",
                "refunded_at": null,
                "store": "promotional",
                "unsubscribe_detected_at": null
            }
        */

        // / We are going to use "purchase_date" instead of "original_purchase_date" because "original_purchase_date" is not updated when user renews the subscription

        foreach ($subscriptions as $identifier => $subs) {

            $orderId = (string) $subs['purchase_date'];
            $orderId = str_replace('-', '', $orderId);
            $orderId = str_replace(':', '', $orderId);
            $orderId = str_replace(' ', '', $orderId);
            $orderId = str_replace('T', '', $orderId);
            $orderId = str_replace('Z', '', $orderId);

            $revenueCatProduct = RevenueCatProducts::where('google_id', $identifier)->first();
            if ($revenueCatProduct == null) {
                $revenueCatProduct = RevenueCatProducts::where('apple_id', $identifier)->first();
                if ($revenueCatProduct == null) {
                    Log::error('RevenueCat product not found. Identifier: ' . $identifier);
                    if ($fromApi) {
                        return response()->json(['message' => 'RevenueCat product not found.'], 412);
                    } else {
                        return null;
                    }
                }
            }

            $gatewayProduct = $revenueCatProduct->gateway_product;
            if ($gatewayProduct == null) {
                Log::error('Gateway product not found for RevenueCat. Identifier: ' . $identifier);
                if ($fromApi) {
                    return response()->json(['message' => 'Gateway product not found.'], 412);
                } else {
                    return null;
                }
            }

            $plan = $revenueCatProduct->plan;
            if ($plan == null) {
                Log::error('Plan not found for RevenueCat. Identifier: ' . $identifier);
                if ($fromApi) {
                    return response()->json(['message' => 'Plan not found.'], 412);
                } else {
                    return null;
                }
            }

            $planId = $plan->id;

            $orders = UserOrder::where([['user_id', '=', $userId], ['order_id', '=', $orderId]])->get();

            if (count($orders) == 0) {
                // Create order

                $order = new UserOrder;
                $order->order_id = $orderId;
                $order->plan_id = $planId;
                $order->type = 'subscription';
                $order->user_id = $userId;
                $order->payment_type = 'RevenueCat';
                $order->price = $plan->price; // users must set coupons at stores - we are not allowed from external source
                $order->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
                $order->status = 'Success';
                $order->country = $user->country ?? 'Unknown';
                $order->save();

                $status = 'cancelled';

                $isRefreshed = false;

                // / Check if there is currently active subscription (in case of refresh/renew, orderId will be updated but plan remains the same)
                // / This is to prevent multiple active subscriptions for the same plan
                $oldSubscription = Subscriptions::where([['user_id', '=', $userId], ['plan_id', '=', $planId], ['stripe_status', '=', 'active']])->first();
                if ($oldSubscription != null) {
                    // There is an active subscription, cancel it
                    $oldSubscription->stripe_status = 'cancelled';
                    $oldSubscription->save();
                    $isRefreshed = true;
                }

                if ($subs['unsubscribe_detected_at'] == null) {
                    // Subscription is active
                    $isTrial = $subs['period_type'] == 'trial';
                    $status = $subs['billing_issues_detected_at'] == null ? ($isTrial == true ? 'trialing' : 'active') : 'cancelled';
                    if ($subs['billing_issues_detected_at'] != null) {
                        Log::error('Billing issue detected at RevenueCat. User ID: ' . $userId . ' Subscription: ' . $identifier);
                    }

                    self::creditIncreaseSubscribePlan($user, $plan);

                    // / Plan is active, and we haven't added to orders before; so this is a new subscription. Hence add the plan to user's remaining words and images

                    CreateActivity::for($user, ! $isRefreshed ? __('Subscribed') : __('Renewed'), $plan->name . ' ' . __('Plan'));
                    EmailPaymentConfirmation::create($user, $plan)->send();
                } else {
                    // Subscription is cancelled
                    if ($subs['billing_issues_detected_at'] != null) {
                        Log::error('Billing issue detected at RevenueCat. User ID: ' . $userId . ' Subscription: ' . $identifier);
                    }
                    $planFrequency = $plan->frequency == FrequencyEnum::MONTHLY->value ? 'month' : 'year';
                    if (Carbon::parse($subs['unsubscribe_detected_at'])->isBefore(Carbon::parse($subs['purchase_date'])->add(1, $planFrequency))) {
                        // / Subscription is cancelled before the end date (user cancelled it)
                        // / Since user cancelled it, we need to remove the plan from user's remaining words and images
                        // / Check if subscription with orderId is still active (in case of cancelled before)
                        $subscription = Subscriptions::where([['user_id', '=', $userId], ['stripe_id', '=', $orderId]])->first();
                        if ($subscription != null) {
                            if ($subscription->stripe_status == 'active' || $subscription->stripe_status == 'trialing') {

                                self::creditDecreaseCancelPlan($user, $plan);
                                // / This is to cancel the subscription only once (orderId is unique and it is active/trial)
                                CreateActivity::for($user, __('Cancelled'), 'Subscription plan');
                            }
                        }
                    }
                }

                // / check if subscription is already added
                $subscription = Subscriptions::where([['user_id', '=', $userId], ['stripe_id', '=', $orderId]])->first();
                if ($subscription != null) {
                    // Subscription is already added, update it
                    $subscription->stripe_status = $status;
                    $subscription->trial_ends_at = Carbon::parse($subs['expires_date']);
                    $subscription->ends_at = Carbon::parse($subs['expires_date']);
                    $subscription->save();
                } else {
                    // Subscription is not added, add it
                    $subscription = new Subscriptions;
                    $subscription->user_id = $userId;
                    $subscription->name = $planId;
                    $subscription->stripe_id = $orderId;
                    $subscription->stripe_status = $status;
                    $subscription->stripe_price = $gatewayProduct->price_id;
                    $subscription->quantity = 1;
                    $subscription->trial_ends_at = Carbon::parse($subs['expires_date']);
                    $subscription->ends_at = Carbon::parse($subs['expires_date']);
                    $subscription->plan_id = $planId;
                    $subscription->paid_with = 'revenuecat';
                    $subscription->save();

                    // $subscriptionItem = new SubscriptionItems();
                    // $subscriptionItem->subscription_id = $subscription->id;
                    // $subscriptionItem->stripe_id = $orderId;
                    // $subscriptionItem->stripe_product = $gatewayProduct->product_id;
                    // $subscriptionItem->stripe_price = $gatewayProduct->price_id;
                    // $subscriptionItem->quantity = 1;
                    // $subscriptionItem->save();
                }

                $user->save();
            } else {
                // / Order already exists, but maybe subscription is cancelled

                $isTrial = $subs['period_type'] == 'trial';
                $status = ($subs['billing_issues_detected_at'] == null && $subs['unsubscribe_detected_at'] == null) ? ($isTrial == true ? 'trialing' : 'active') : 'cancelled';

                if ($subs['unsubscribe_detected_at'] != null) {
                    // Subscription is cancelled
                    if ($subs['billing_issues_detected_at'] != null) {
                        Log::error('Billing issue detected at RevenueCat. User ID: ' . $userId . ' Subscription: ' . $identifier);
                    }
                    $planFrequency = $plan->frequency == FrequencyEnum::MONTHLY->value ? 'month' : 'year';
                    if (Carbon::parse($subs['unsubscribe_detected_at'])->isBefore(Carbon::parse($subs['purchase_date'])->add(1, $planFrequency))) {
                        // / Subscription is cancelled before the end date (user cancelled it)
                        // / Since user cancelled it, we need to remove the plan from user's remaining words and images

                        // / Check if subscription with orderId is still active (in case of cancelled before)
                        $subscription = Subscriptions::where([['user_id', '=', $userId], ['stripe_id', '=', $orderId]])->first();
                        if ($subscription != null) {
                            if ($subscription->stripe_status == 'active' || $subscription->stripe_status == 'trialing') {

                                // / This is to cancel the subscription only once (orderId is unique and it is active/trial)

                                self::creditDecreaseCancelPlan($user, $plan);

                                CreateActivity::for($user, __('Cancelled'), 'Subscription plan');
                            }
                        }
                    }
                }

                // / check if subscription is already added
                $subscription = Subscriptions::where([['user_id', '=', $userId], ['stripe_id', '=', $orderId]])->first();
                if ($subscription == null) {
                    // Subscription is added while creating order
                    Log::error('Subscription is added while creating order. But not found on checks. User ID: ' . $userId . ' Subscription: ' . $identifier);
                } else {
                    // Subscription is already added, update it
                    $subscription->stripe_status = $status;
                    $subscription->trial_ends_at = Carbon::parse($subs['expires_date']);
                    $subscription->ends_at = Carbon::parse($subs['expires_date']);
                    $subscription->save();
                }

                $user->save();

            }

        }

        // /////////////////////////
        // / Prepare Result
        // /////////////////////////

        // / We have checked both subscriptions and token packs, now we need to check if there is any active subscription and return the result

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            if ($fromApi) {
                return response()->json(['message' => __('Active subscription found.'), 'status' => 'active'], 200);
            }

            return true;
        }
        if ($fromApi) {
            return response()->json(['message' => 'No active subscription found.'], 404);
        }

        return false;

    }

    public static function checkIfTrial()
    {
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            return $activeSub->stripe_status == 'trialing';
        }

        return false;
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            'code'                  => 'revenuecat',
            'title'                 => 'RevenueCat',
            'link'                  => 'https://www.revenuecat.com/',
            'active'                => 1,
            'available'             => 1,
            'img'                   => '/assets/img/payments/revenuecat.png',
            'whiteLogo'             => 0,
            'mode'                  => 0,
            'sandbox_client_id'     => 0,
            'sandbox_client_secret' => 0,
            'sandbox_app_id'        => 0,
            'live_client_id'        => 1,
            'live_client_secret'    => 0,
            'live_app_id'           => 0,
            'currency'              => 0,
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
