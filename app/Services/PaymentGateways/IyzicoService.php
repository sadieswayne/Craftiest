<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Actions\EmailPaymentConfirmation;
use App\Enums\Plan\FrequencyEnum;
use App\Enums\Plan\TypeEnum;
use App\Events\IyzicoWebhookEvent;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\CustomSettings;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\OldGatewayProducts;
// use App\Models\Subscriptions;
use App\Models\Plan;
// use App\Models\SubscriptionItems;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use App\Services\PaymentGateways\Libraries\IyzipayActions;
use Brick\Math\BigDecimal;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Iyzipay\Model\Locale;
use JsonException;
use Laravel\Cashier\Subscription as Subscriptions;

/**
 * Base functions foreach payment gateway
 *
 * @param saveAllProducts
 * @param saveProduct ($plan)
 * @param subscribe ($plan)
 * @param subscribeCheckout (Request $request, $referral= null)
 * @param prepaid ($plan)
 * @param prepaidCheckout (Request $request, $referral= null)
 * @param getSubscriptionStatus ($incomingUserId = null)
 * @param getSubscriptionDaysLeft
 * @param subscribeCancel
 * @param checkIfTrial
 * @param getSubscriptionRenewDate
 * @param cancelSubscribedPlan ($subscription)
 */
class IyzicoService
{
    use CreditUpdater;

    protected static $GATEWAY_CODE = 'iyzico';

    protected static $GATEWAY_NAME = 'Iyzico';

    // payment functions
    public static function saveAllProducts()
    {
        try {
            $gateway = Gateways::where('code', self::$GATEWAY_CODE)->first();
            if ($gateway == null) {
                return back()->with(['message' => __('Please enable iyzico'), 'type' => 'error']);
            }
            $plans = Plan::where('active', 1)->get();
            foreach ($plans as $plan) {
                self::saveProduct($plan);
            }
            // Create webhook of iyzico later
            // $tmp = self::createWebhook();
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> saveAllProducts(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public function planFrequencyControl(?Plan $plan): bool
    {
        return $plan->price > 0 &&
            $plan->type == TypeEnum::SUBSCRIPTION->value &&
            $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value &&
            $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value;
    }

    public static function saveProduct($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $iyzipayActions = self::retrieveGatewaySettings();

        try {
            DB::beginTransaction();
            $product = null;
            $oldProductId = null;
            // check if product exists
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            // if Subscription price and its not lifetime subscription
            if ($plan->price > 0 && $plan->type == TypeEnum::SUBSCRIPTION->value && $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value && $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value) {
                if ($product == null) {
                    $prd = json_decode(json_encode([
                        'name' => $plan->name,
                    ]));
                    $newProduct = $iyzipayActions->createSubscriptionProduct($prd);
                    if ($newProduct->getReferenceCode() != null) {
                        $product = new GatewayProducts;
                        $product->plan_id = $plan->id;
                        $product->plan_name = $plan->name;
                        $product->gateway_code = self::$GATEWAY_CODE;
                        $product->gateway_title = self::$GATEWAY_CODE;
                        $product->product_id = $newProduct->getReferenceCode();
                        $product->save();
                    } else {
                        Log::error('IyzicoService::saveProduct() - Product could not be created. Product : ' . json_encode($newProduct));
                    }
                } else {
                    $prd = json_decode(json_encode([
                        'productReferenceCode' => $product->product_id,
                    ]));
                    $checkProduct = $iyzipayActions->retrieveSubscriptionProduct($prd);
                    if ($checkProduct->getReferenceCode() == null) {
                        if ($product->product_id != null) {
                            // Product has been created before
                            $oldProductId = $product->product_id;
                        }
                        $prd = json_decode(json_encode([
                            'name' => $plan->name,
                        ]));
                        $newProduct = $iyzipayActions->createSubscriptionProduct($prd);
                        if ($newProduct->getReferenceCode() != null) {
                            $product->product_id = $newProduct->getReferenceCode();
                            $product->plan_name = $plan->name;
                            $product->save();
                        } else {
                            Log::error('IyzicoService::saveProduct() - Product could not be created. Product : ' . json_encode($newProduct));
                        }
                    }
                }
            } else {
                if ($product == null) {
                    $product = new GatewayProducts;
                    $product->plan_id = $plan->id;
                    $product->plan_name = $plan->name;
                    $product->gateway_code = self::$GATEWAY_CODE;
                    $product->gateway_title = self::$GATEWAY_CODE;
                }
                // one-Time price
                $product->product_id = 'Not Needed';
                $product->save();
            }
            // check if price exists
            $total = $plan->price + taxToVal($plan->price, $gateway->tax);
            if ($product->price_id != null) {
                // price_id exists so we dont need to create plans
                // if Subscription price and its not lifetime subscription
                if ($plan->price > 0 && $plan->type == TypeEnum::SUBSCRIPTION->value && $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value && $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value) {
                    // check if price exists in iyzico
                    $prd = json_decode(json_encode([
                        'pricingPlanReferenceCode' => $product->price_id,
                    ]));
                    $checkPrice = $iyzipayActions->retrieveSubscriptionPricingPlan($prd);
                    if ($checkPrice->getReferenceCode() == null) {
                        $oldPricingPlanId = $product->price_id;
                        // create new plan with new values
                        $interval = $plan->frequency == FrequencyEnum::MONTHLY->value ? 'MONTHLY' : 'YEARLY';
                        if ($plan->trial_days != 'undefined') {
                            $trials = $plan->trial_days ?? 0;
                        } else {
                            $trials = 0;
                        }

                        $pricingPlan = json_decode(json_encode([
                            'paymentInterval'      => $interval,
                            'paymentIntervalCount' => 1,
                            'paymentType'          => 'RECURRING',
                            'trialPeriodDays'      => $trials,
                            'productReferenceCode' => $product->product_id,
                            'price'                => BigDecimal::of($total)->toScale(2),
                            'name'                 => $product->plan_name,
                        ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
                        $subscriptionPricingPlan = $iyzipayActions->createSubscriptionPricingPlan($pricingPlan);
                        if ($subscriptionPricingPlan->getReferenceCode()) {
                            $product->price_id = $subscriptionPricingPlan->getReferenceCode();
                            $product->save();

                            $history = new OldGatewayProducts;
                            $history->plan_id = $plan->id;
                            $history->plan_name = $plan->name;
                            $history->gateway_code = self::$GATEWAY_CODE;
                            $history->product_id = $product->product_id;
                            $history->old_product_id = $oldProductId;
                            $history->old_price_id = $oldPricingPlanId;
                            $history->new_price_id = $subscriptionPricingPlan->getReferenceCode();
                            $history->status = 'check';
                            $history->save();

                            self::updateUserData();
                        } else {
                            Log::error('IyzicoService::saveProduct() - Pricing Plan could not be created. Pricing Plan : id: ' . $plan->id . ', name: ' . $plan->name . json_encode($subscriptionPricingPlan));
                        }
                    }

                } else {
                    // One-Time price || iyzico handles one time prices with payments, so we do not need to set anything for one-time payments.
                    $product->price_id = 'Not Needed';
                    $product->save();
                }
            } else {
                // price_id is null so we need to create plans
                // if Subscription price and its not lifetime subscription
                if ($plan->price > 0 && $plan->type == TypeEnum::SUBSCRIPTION->value && $plan->frequency !== FrequencyEnum::LIFETIME_MONTHLY->value && $plan->frequency !== FrequencyEnum::LIFETIME_YEARLY->value) {
                    $interval = $plan->frequency == FrequencyEnum::MONTHLY->value ? 'MONTHLY' : 'YEARLY';
                    if ($plan->trial_days !== 'undefined') {
                        $trials = $plan->trial_days ?? 0;
                    } else {
                        $trials = 0;
                    }
                    $pricingPlan = json_decode(json_encode([
                        'paymentInterval'      => $interval,
                        'paymentIntervalCount' => 1,
                        'paymentType'          => 'RECURRING',
                        'trialPeriodDays'      => $trials,
                        'productReferenceCode' => $product->product_id,
                        'price'                => BigDecimal::of($total)->toScale(2), // number_format($total, 2),
                        'name'                 => $product->plan_name,
                    ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
                    $subscriptionPricingPlan = $iyzipayActions->createSubscriptionPricingPlan($pricingPlan);
                    if ($subscriptionPricingPlan->getReferenceCode() != null) {
                        $product->price_id = $subscriptionPricingPlan->getReferenceCode();
                        $product->save();
                    } else {
                        Log::error('IyzicoService::saveProduct() - Pricing Plan could not be created. Pricing Plan : id: ' . $plan->id . ', name: ' . $plan->name . json_encode($subscriptionPricingPlan));
                    }
                } else {
                    // One-Time price || iyzico handles one time prices with orders, so we do not need to set anything for one-time payments.
                    $product->price_id = 'Not Needed';
                    $product->save();
                }
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error("IyzicoService::saveProduct()\n" . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribe($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);

        try {
            $exception = null;
            $coupon = checkCouponInRequest(); // if there a coupon in request it will return the coupin instanse
            $newDiscountedPrice = $plan->price;
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($plan->price, $taxRate);
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            if ($coupon) {
                $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                $newDiscountedPriceCents = (int) (((float) $newDiscountedPrice) * 100);
                if ($newDiscountedPrice !== floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
            }
            $newDiscountedPrice += $taxValue;
            $iyzicoPriceId = self::getIyzicoPriceId($plan->id);
            if (is_null($iyzicoPriceId)) {
                $exception = __('Product ID is not set! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }

            return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE . '.pre', compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate', 'gateway', 'exception', 'currency', 'iyzicoPriceId'));
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return back()->with(['message' => Str::before($ex->getMessage(), ':'), 'type' => 'error']);
        }
    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $iyzipayActions = self::retrieveGatewaySettings();
        $user = auth()->user();
        $planID = $request->input('planID', null);
        $couponID = $request->input('couponID', null);
        $plan = Plan::find($planID) ?? abort(404);
        $newDiscountedPrice = $plan->price + taxToVal($plan->price, $gateway->tax);
        $taxRate = $gateway->tax;
        $taxValue = taxToVal($plan->price, $taxRate);
        $exception = null;
        $checkoutform = null;

        try {
            if (is_null($request->all())) {
                return back()->with(['message' => __('Please fill all fields'), 'type' => 'error']);
            }
            $rules = [
                'name'                => 'required',
                'surname'             => 'required',
                'identityNumber'      => 'required',
                'email'               => 'required|email',
                'gsmNumber'           => 'required',
                'registrationAddress' => 'required',
                'city'                => 'required',
                'country'             => 'required',
                'zipCode'             => 'required',
                'iyzicoPriceId'       => 'required',
                'ip'                  => 'required',
            ];
            $messages = [
                'required' => __('Please fill all fields'),
                'email'    => __('Invalid email format'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if ($plan->type === TypeEnum::SUBSCRIPTION->value && ($plan->frequency === FrequencyEnum::LIFETIME_MONTHLY->value || $plan->frequency === FrequencyEnum::LIFETIME_YEARLY->value)) {
                // create a new instance of incoming $request for buyer
                $customerRequest = json_decode(json_encode([
                    'id'                  => Auth::user()->id,
                    'planId'              => $planID,
                    'name'                => $request->name,
                    'surname'             => $request->surname,
                    'identityNumber'      => $request->identityNumber,
                    'email'               => $request->email,
                    'gsmNumber'           => $request->gsmNumber,
                    'registrationAddress' => $request->registrationAddress,
                    'city'                => $request->city,
                    'country'             => $request->country,
                    'zipCode'             => $request->zipCode,
                    'ip'                  => $request->ip,
                ]));
                // create buyer from request data
                $buyer = $iyzipayActions->createBuyer($customerRequest);
                // create a new instance of incoming $request for address
                $addressRequest = json_decode(json_encode([
                    'contactName' => $request->name . ' ' . $request->surname,
                    'address'     => $request->registrationAddress,
                    'city'        => $request->city,
                    'country'     => $request->country,
                    'zipCode'     => $request->zipCode,
                ]));
                // create address from request data
                $address = $iyzipayActions->createAddress($addressRequest);
                $basketItemsArray = [];
                if ($couponID !== null) {
                    $coupon = checkCouponInRequest($couponID);
                    $couponID = $coupon->discount;
                    $newDiscountedPrice -= ($plan->price * ($coupon->discount / 100));
                    if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                        $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                    }
                    $coupon->usersUsed()->attach(auth()->user()->id);
                    session_start();
                    $_SESSION['applied_coupon'] = [
                        'coupon'  => $coupon,
                        'plan_id' => $plan->id,
                    ];
                    session_write_close();
                }
                $basketItems = [
                    'basketItemId' => $plan->id,
                    'name'         => $plan->name,
                    'category1'    => 'Token Packs',
                    'itemType'     => 'VIRTUAL',
                    'price'        => $newDiscountedPrice,
                ];
                $basketItem_0 = $iyzipayActions->createBasketItem($basketItems);
                // now we have everthing to create one time payment. Sum them to one request
                $paymentRequest = json_decode(json_encode([
                    'price'               => $newDiscountedPrice,
                    'paidPrice'           => $newDiscountedPrice,
                    'paymentGroup'        => 'PRODUCT',
                    'callbackUrl'         => route('dashboard.user.payment.iyzico.prepaid.callback'),
                    'enabledInstallments' => [1, 2, 3, 6, 9],
                    'buyer'               => $buyer,
                    'shippingAddress'     => $address,
                    'billingAddress'      => $address,
                    'basketItems'         => $basketItemsArray,
                ]));

                // create checkout form for one time payment with paymentRequest
                $requestOneTimePayment = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest;
                $requestOneTimePayment->setPrice($newDiscountedPrice);
                $requestOneTimePayment->setPaidPrice($newDiscountedPrice);
                $requestOneTimePayment->setCallbackUrl(route('dashboard.user.payment.iyzico.subscribe.callback'));
                $requestOneTimePayment->setEnabledInstallments([1, 2, 3, 6, 9]);
                $requestOneTimePayment->setBuyer($buyer);
                $requestOneTimePayment->setShippingAddress($address);
                $requestOneTimePayment->setBillingAddress($address);
                $requestOneTimePayment->setBasketItems([$basketItem_0]);

                $checkoutform = \Iyzipay\Model\CheckoutFormInitialize::create($requestOneTimePayment, $iyzipayActions->getConfig());
                if ($checkoutform->getStatus() === 'failure') {
                    $errorCode = $checkoutform->getErrorCode();
                    $errorMessage = $checkoutform->getErrorMessage();

                    return back()->with([
                        'message' => __('Please enter valid information!') . " Error Code: $errorCode - $errorMessage",
                        'type'    => 'error',
                    ]);
                }
                // function did not work out for now. may be we can turn back after
                // $checkoutform = $iyzipayActions->createOneTimePayment($paymentRequest);

                // Since we can not transfer anything except token id to callback page we must use a middle step
                // We are going to save token id to CustomSettings table and retrieve it from callback page.
                $customSettings = new CustomSettings;
                $customSettings->key = $checkoutform->getToken();
                $customSettings->value_str = strval($plan->id);
                $customSettings->save();
            } else {
                // create a new instance of incoming $request for subscription customer
                $customerRequest = json_decode(json_encode([
                    // "id" => $user->id,
                    'name'                => $request->name,
                    'surname'             => $request->surname,
                    'identityNumber'      => $request->identityNumber,
                    'email'               => $request->email,
                    'gsmNumber'           => $request->gsmNumber,
                    'shippingContactName' => $request->name . ' ' . $request->surname,
                    'shippingCity'        => $request->city,
                    'shippingCountry'     => $request->country,
                    'shippingAddress'     => $request->registrationAddress,
                    'shippingZipCode'     => $request->zipCode,
                    'billingContactName'  => $request->name . ' ' . $request->surname,
                    'billingCity'         => $request->city,
                    'billingCountry'      => $request->country,
                    'billingAddress'      => $request->registrationAddress,
                    'billingZipCode'      => $request->zipCode,
                ]));
                if ($user->iyzico_id != null) {
                    // retrieve customer from iyzico
                    $cst = json_decode(json_encode([
                        'customerReferenceCode' => $user->iyzico_id,
                    ]));
                    $customer = $iyzipayActions->retrieveSubscriptionCustomer($cst);
                    if ($customer->getReferenceCode() != null) {
                        // customer exists
                        Log::info('iyzico customer exists');
                        $customerRequest->customerReferenceCode = $user->iyzico_id;
                        $customer = $iyzipayActions->updateSubscriptionCustomer($customerRequest);
                    } else {
                        // customer does not exist
                        Log::info('iyzico customer does not exist');
                        $customer = $iyzipayActions->createCustomer($customerRequest);
                    }
                } else {
                    // create customer from request data
                    Log::info('iyzico customer does not exist');
                    $customer = $iyzipayActions->createCustomer($customerRequest);
                }
                // check if customer set
                if ($customer == null) {
                    return back()->with(['message' => __('Customer could not set'), 'type' => 'error']);
                }
                $newDPriceID = $request->iyzicoPriceId;

                if ($couponID !== null) {
                    $coupon = checkCouponInRequest($couponID);
                    $couponID = $coupon->discount;
                    $newDiscountedPrice -= ($plan->price * ($coupon->discount / 100));
                    if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                        $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                    }
                    $coupon->usersUsed()->attach(auth()->user()->id);
                    $prd = json_decode(json_encode([
                        'name' => 'discount_' . $coupon->code . '_' . $user->id . '_' . $plan->id . '_' . time(),
                    ]));
                    $newProduct = $iyzipayActions->createSubscriptionProduct($prd);
                    if ($newProduct->getReferenceCode() != null) {
                        $interval = $plan->frequency == FrequencyEnum::MONTHLY->value ? 'MONTHLY' : 'YEARLY';
                        if ($plan->trial_days != 'undefined') {
                            $trials = $plan->trial_days ?? 0;
                        } else {
                            $trials = 0;
                        }
                        $pricingPlan = json_decode(json_encode([
                            'paymentInterval'      => $interval,
                            'paymentIntervalCount' => 1,
                            'paymentType'          => 'RECURRING',
                            'trialPeriodDays'      => $trials,
                            'productReferenceCode' => $newProduct->getReferenceCode(),
                            'price'                => BigDecimal::of($newDiscountedPrice)->toScale(2),
                            'name'                 => $prd->name,
                        ]));
                        $subscriptionPricingPlan = $iyzipayActions->createSubscriptionPricingPlan($pricingPlan);
                        if ($subscriptionPricingPlan->getReferenceCode() == null) {
                            Log::error('IyzicoService::saveProduct() - Pricing Plan could not be created. Pricing Plan : id: ' . $plan->id . ', name: ' . $plan->name . json_encode($subscriptionPricingPlan));
                        }
                        session_start();
                        $_SESSION['applied_coupon'] = [
                            'coupon'  => $coupon,
                            'plan_id' => $plan->id,
                        ];
                        session_write_close();
                        $newDPriceID = $subscriptionPricingPlan->getReferenceCode();
                    }
                }
                $checkoutFormRequest = new \Iyzipay\Request\Subscription\SubscriptionCreateCheckoutFormRequest;
                $checkoutFormRequest->setConversationId($iyzipayActions->generateRandomNumber());
                $checkoutFormRequest->setLocale($iyzipayActions->getLocale());
                $checkoutFormRequest->setPricingPlanReferenceCode($newDPriceID);
                $checkoutFormRequest->setSubscriptionInitialStatus('ACTIVE');
                $checkoutFormRequest->setCallbackUrl(route('dashboard.user.payment.iyzico.subscribe.callback'));
                $checkoutFormRequest->setCustomer($customer);
                $checkoutform = \Iyzipay\Model\Subscription\SubscriptionCreateCheckoutForm::create($checkoutFormRequest, $iyzipayActions->getConfig());
                if ($checkoutform->getStatus() === 'failure') {
                    $errorCode = $checkoutform->getErrorCode();
                    $errorMessage = $checkoutform->getErrorMessage();

                    return back()->with([
                        'message' => __('Please enter valid information!') . " Error Code: $errorCode - $errorMessage",
                        'type'    => 'error',
                    ]);
                }
                Log::info('checkoutform : ' . json_encode($checkoutform));
                if ($checkoutform == null) {
                    $exception = 'Checkout form could not be created';

                    return back()->with(['message' => __('Please enter valid information!'), 'type' => 'error']);
                }
                // Since we can not transfer anything except token id to callback page we must use a middle step
                // We are going to save token id to CustomSettings table and retrieve it from callback page.
                $customSettings = new CustomSettings;
                $customSettings->key = $checkoutform->getToken();
                $customSettings->value_str = strval($planID);
                $customSettings->save();

            }
        } catch (Exception $th) {
            $exception = $th->getMessage();
            // $exception = Str::before($th->getMessage(),':');
        }

        return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE . '.pay', compact('plan', 'taxRate', 'taxValue', 'newDiscountedPrice', 'gateway', 'exception', 'currency', 'checkoutform'));
    }

    public static function iyzicoSubscribeCallback(Request $request)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $couponID = null;
        $plan = null;
        $exception = null;
        $settings = Setting::getCache();
        $user = Auth::user();
        $type = 'subscription';
        // check if request has token
        if ($request->token == null) {
            return back()->with(['message' => __('Token is missing'), 'type' => 'error']);
        }

        try {
            DB::beginTransaction();
            // get iyzipayActions class
            $iyzipayActions = self::retrieveGatewaySettings();

            // retrieve subscription result
            $checkoutresultrequest = new \Iyzipay\Request\Subscription\RetrieveSubscriptionCreateCheckoutFormRequest;
            $checkoutresultrequest->setCheckoutFormToken(strval($request->token));
            $checkoutresult = \Iyzipay\Model\Subscription\RetrieveSubscriptionCheckoutForm::retrieve($checkoutresultrequest, $iyzipayActions->getConfig());
            if ($checkoutresult->getStatus() == 'success' && ($checkoutresult->getSubscriptionStatus() == 'ACTIVE' || $checkoutresult->getSubscriptionStatus() == 'active')) {
                // Since we could not transfer anything except token id to callback page we must use a middle step
                // We saved token id to CustomSettings table and retrieve it now
                $customSettings = CustomSettings::where('key', $request->token)->first();
                if ($customSettings == null) {
                    // if we can't get plan id, just save it with a warning. So user can check from iyzico backend.
                    $payment = new UserOrder;
                    $payment->order_id = $checkoutresult->getReferenceCode();
                    $payment->plan_id = 'Missing Plan Id. Check with token and order id.';
                    $payment->type = 'prepaid';
                    $payment->user_id = $user->id;
                    $payment->payment_type = self::$GATEWAY_CODE;
                    $payment->price = 0;
                    $payment->affiliate_earnings = 0;
                    $payment->status = 'Success with token:' . $request->token;
                    $payment->country = $user->country ?? 'Unknown';
                    $payment->save();

                    return back()->with(['message' => __('Token is missing'), 'type' => 'error']);
                }
                // get plan id from CustomSettings table
                $planId = $customSettings->value_str;
                // get plan
                $plan = Plan::where('id', $planId)->first();
                $newDiscountedPrice = $plan->price;
                $taxRate = $gateway->tax;
                $taxValue = taxToVal($plan->price, $taxRate);
                $newDiscountedPrice = $plan->price;
                session_start(); // Start the session if not already started
                if (isset($_SESSION['applied_coupon'])) {
                    $appliedCouponData = $_SESSION['applied_coupon'];
                    if ($appliedCouponData['plan_id'] == $planId) {
                        $appliedCoupon = $appliedCouponData['coupon'];
                        $newDiscountedPrice = $plan->price - ($plan->price * ($appliedCoupon->discount / 100));
                        $couponID = $appliedCoupon->discount;
                    }
                    unset($_SESSION['applied_coupon']);
                }
                session_write_close();
                $newDiscountedPrice += $taxValue;

                // save checkout to orders
                $payment = new UserOrder;
                $payment->order_id = $request->token;
                $payment->plan_id = $plan->id;
                $payment->type = 'subscription';
                $payment->user_id = $user->id;
                $payment->payment_type = self::$GATEWAY_CODE;
                $payment->price = $newDiscountedPrice;
                $payment->affiliate_earnings = ($newDiscountedPrice * $settings->affiliate_commission_percentage) / 100;
                $payment->status = 'Success';
                $payment->country = $user->country ?? 'Unknown';
                $payment->tax_rate = $taxRate;
                $payment->tax_value = $taxValue;
                $payment->save();
                // get gateway product related to plan id
                $product = GatewayProducts::where(['plan_id' => $planId, 'gateway_code' => self::$GATEWAY_CODE])->first();

                // save subscription to database
                $subscription = new Subscriptions;

                $subscription->stripe_id = $checkoutresult->getReferenceCode();
                $subscription->stripe_status = 'active';
                $subscription->ends_at = $plan->trial_days != 0 ? Carbon::now()->addDays($plan->trial_days) : Carbon::now()->addDays(30);

                $subscription->user_id = $user->id;
                $subscription->name = $planId;
                $subscription->stripe_price = $product->price_id;
                $subscription->quantity = 1;
                $subscription->trial_ends_at = $plan->trial_days != 0 ? Carbon::now()->addDays($plan->trial_days) : null;
                $subscription->plan_id = $planId;
                $subscription->paid_with = self::$GATEWAY_CODE;
                $subscription->tax_rate = $taxRate;
                $subscription->tax_value = $taxValue;
                $subscription->coupon = $couponID;
                $subscription->total_amount = $newDiscountedPrice;
                $subscription->save();

                // $subscriptionItem = new SubscriptionItems();
                // $subscriptionItem->subscription_id = $subscription->id;
                // $subscriptionItem->stripe_id = $checkoutresult->getParentReferenceCode();
                // $subscriptionItem->stripe_product = $product->product_id;
                self::creditIncreaseSubscribePlan($user, $plan);

                // delete custom settings since we do not need it anymore
                $customSettings->delete();
                \App\Models\Usage::getSingle()->updateSalesCount($newDiscountedPrice);
                CreateActivity::for($user, __('Subscribed'), $plan->name . ' ' . __('Plan'));
                EmailPaymentConfirmation::create($user, $plan)->send();
            } else {
                // lifetime plan
                $checkoutRequest = [
                    'token' => $request->token,
                ];
                // retrieve one time payment result
                $checkoutresult = $iyzipayActions->retrieveOneTimePayment($checkoutRequest);
                if ($checkoutresult->getStatus() == 'success' && ($checkoutresult->getPaymentStatus() == 'SUCCESS' || $checkoutresult->getPaymentStatus() == 'success')) {
                    $type = 'prepaid';
                    // Since we could not transfer anything except token id to callback page we must use a middle step
                    // We saved token id to CustomSettings table and retrieve it now
                    $customSettings = CustomSettings::where('key', $request->token)->first();
                    if ($customSettings == null) {
                        // if we can't get plan id, just save it with a warning. So user can check from iyzico backend.
                        $payment = new UserOrder;
                        $payment->order_id = $checkoutresult->getPaymentId();
                        $payment->plan_id = 'Missing Plan Id. Check with token and order id.';
                        $payment->type = 'prepaid';
                        $payment->user_id = $user->id;
                        $payment->payment_type = self::$GATEWAY_CODE;
                        $payment->price = 0;
                        $payment->affiliate_earnings = 0;
                        $payment->status = 'Success with token:' . $request->token;
                        $payment->country = $user->country ?? 'Unknown';
                        $payment->save();

                        return back()->with(['message' => __('Token is missing'), 'type' => 'error']);
                    }
                    $planId = $customSettings->value_str;
                    $plan = Plan::where('id', $planId)->first();
                    if ($plan->type == 'subscription' && ($plan->frequency == FrequencyEnum::LIFETIME_MONTHLY->value || $plan->frequency == FrequencyEnum::LIFETIME_YEARLY->value)) {

                        $newDiscountedPrice = $plan->price;
                        $taxRate = $gateway->tax;
                        $taxValue = taxToVal($plan->price, $taxRate);
                        $newDiscountedPrice = $plan->price;
                        session_start(); // Start the session if not already started
                        if (isset($_SESSION['applied_coupon'])) {
                            $appliedCouponData = $_SESSION['applied_coupon'];
                            if ($appliedCouponData['plan_id'] == $planId) {
                                $appliedCoupon = $appliedCouponData['coupon'];
                                $newDiscountedPrice = $plan->price - ($plan->price * ($appliedCoupon->discount / 100));
                                $couponID = $appliedCoupon->discount;
                            }
                            unset($_SESSION['applied_coupon']);
                        }
                        session_write_close();
                        $newDiscountedPrice += $taxValue;

                        // save checkout to orders
                        $payment = new UserOrder;
                        $payment->order_id = $request->token;
                        $payment->plan_id = $plan->id;
                        $payment->type = 'subscription';
                        $payment->user_id = $user->id;
                        $payment->payment_type = self::$GATEWAY_CODE;
                        $payment->price = $newDiscountedPrice;
                        $payment->affiliate_earnings = ($newDiscountedPrice * $settings->affiliate_commission_percentage) / 100;
                        $payment->status = 'Success';
                        $payment->country = $user->country ?? 'Unknown';
                        $payment->tax_rate = $taxRate;
                        $payment->tax_value = $taxValue;
                        $payment->save();
                        // get gateway product related to plan id
                        $product = GatewayProducts::where(['plan_id' => $planId, 'gateway_code' => self::$GATEWAY_CODE])->first();

                        // save subscription to database
                        $subscription = new Subscriptions;
                        $subscription->stripe_id = 'ILS-' . strtoupper(Str::random(13));
                        $subscription->ends_at = $plan->frequency == 'lifetime_monthly' ? Carbon::now()->addMonths(1) : Carbon::now()->addYears(1);
                        $subscription->auto_renewal = 1;
                        $subscription->stripe_status = 'iyzico_approved';
                        $subscription->user_id = $user->id;
                        $subscription->name = $planId;
                        $subscription->stripe_price = $product->price_id;
                        $subscription->quantity = 1;
                        $subscription->trial_ends_at = $plan->trial_days != 0 ? Carbon::now()->addDays($plan->trial_days) : null;
                        $subscription->plan_id = $planId;
                        $subscription->paid_with = self::$GATEWAY_CODE;
                        $subscription->tax_rate = $taxRate;
                        $subscription->tax_value = $taxValue;
                        $subscription->coupon = $couponID;
                        $subscription->total_amount = $newDiscountedPrice;
                        $subscription->save();

                        // $subscriptionItem = new SubscriptionItems();
                        // $subscriptionItem->subscription_id = $subscription->id;
                        // $subscriptionItem->stripe_id = $subscription->stripe_id;
                        // $subscriptionItem->stripe_product = $product->product_id;
                        // $subscriptionItem->stripe_price = $product->price_id;
                        // $subscriptionItem->quantity = 1;
                        // $subscriptionItem->save();
                        self::creditIncreaseSubscribePlan($user, $plan);
                        // delete custom settings since we do not need it anymore
                        $customSettings->delete();
                        \App\Models\Usage::getSingle()->updateSalesCount($newDiscountedPrice);
                        CreateActivity::for($user, __('Subscribed'), $plan->name . ' ' . __('Plan'));
                        EmailPaymentConfirmation::create($user, $plan)->send();
                    }
                }
            }
            DB::commit();

            return view('panel.user.finance.' . $type . '.' . self::$GATEWAY_CODE . '.result', compact('plan', 'type', 'gateway', 'exception', 'currency', 'checkoutresult'));
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> iyzicoSubscribeCallback(): ' . $ex->getMessage());

            return back()->with(['message' => Str::before($ex->getMessage(), ':'), 'type' => 'error']);
        }

    }

    public static function prepaid($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $newDiscountedPrice = $plan->price;
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $coupon = checkCouponInRequest();
        $taxRate = $gateway->tax;
        $taxValue = taxToVal($newDiscountedPrice, $taxRate);
        if (self::getIyzicoPriceId($plan->id) === null) {
            $exception = 'Product ID is not set! Please save Membership Plan again.';

            return back()->with(['message' => $exception, 'type' => 'error']);
        }

        try {
            if ($coupon) {
                $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                if ($newDiscountedPrice !== floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
            }
            if ($taxValue > 0) {
                $newDiscountedPrice += $taxValue;
            }

            return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE . '.pre', compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate', 'gateway', 'currency'));
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> prepaid(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $iyzipayActions = self::retrieveGatewaySettings();
        $user = auth()->user();
        $planID = $request->input('planID', null);
        $couponID = $request->input('couponID', null);
        $plan = Plan::find($planID) ?? abort(404);
        $newDiscountedPrice = $plan->price + taxToVal($plan->price, $gateway->tax);
        $taxRate = $gateway->tax;
        $taxValue = taxToVal($plan->price, $taxRate);
        $exception = null;
        $checkoutform = null;

        try {
            if (is_null($request->all())) {
                return back()->with(['message' => __('Please fill all fields'), 'type' => 'error']);
            }
            $rules = [
                'name'                => 'required',
                'surname'             => 'required',
                'identityNumber'      => 'required',
                'email'               => 'required|email',
                'gsmNumber'           => 'required',
                'registrationAddress' => 'required',
                'city'                => 'required',
                'country'             => 'required',
                'zipCode'             => 'required',
                'ip'                  => 'required',
            ];
            $messages = [
                'required' => __('Please fill all fields'),
                'email'    => __('Invalid email format'),
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // create a new instance of incoming $request for buyer
            $customerRequest = json_decode(json_encode([
                'id'                  => Auth::user()->id,
                'planId'              => $planID,
                'name'                => $request->name,
                'surname'             => $request->surname,
                'identityNumber'      => $request->identityNumber,
                'email'               => $request->email,
                'gsmNumber'           => $request->gsmNumber,
                'registrationAddress' => $request->registrationAddress,
                'city'                => $request->city,
                'country'             => $request->country,
                'zipCode'             => $request->zipCode,
                'ip'                  => $request->ip,
            ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
            // create buyer from request data
            $buyer = $iyzipayActions->createBuyer($customerRequest);
            // create a new instance of incoming $request for address
            $addressRequest = json_decode(json_encode([
                'contactName' => $request->name . ' ' . $request->surname,
                'address'     => $request->registrationAddress,
                'city'        => $request->city,
                'country'     => $request->country,
                'zipCode'     => $request->zipCode,
            ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
            // create address from request data
            $address = $iyzipayActions->createAddress($addressRequest);
            $basketItemsArray = [];
            if ($couponID !== null) {
                $coupon = checkCouponInRequest($couponID);
                $couponID = $coupon->discount;
                $newDiscountedPrice -= ($plan->price * ($coupon->discount / 100));
                if ($newDiscountedPrice !== floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
                $coupon->usersUsed()->attach(auth()->user()->id);
                session_start();
                $_SESSION['applied_coupon'] = [
                    'coupon'  => $coupon,
                    'plan_id' => $plan->id,
                ];
                session_write_close();
            }
            $basketItems = [
                'basketItemId' => $plan->id,
                'name'         => $plan->name,
                'category1'    => 'Token Packs',
                'itemType'     => 'VIRTUAL',
                'price'        => $newDiscountedPrice,
            ];
            $basketItem_0 = $iyzipayActions->createBasketItem($basketItems);
            // now we have everthing to create one time payment. Sum them to one request
            $paymentRequest = json_decode(json_encode([
                'price'               => $newDiscountedPrice,
                'paidPrice'           => $newDiscountedPrice,
                'paymentGroup'        => 'PRODUCT',
                'callbackUrl'         => route('dashboard.user.payment.iyzico.prepaid.callback'),
                'enabledInstallments' => [1, 2, 3, 6, 9],
                'buyer'               => $buyer,
                'shippingAddress'     => $address,
                'billingAddress'      => $address,
                'basketItems'         => $basketItemsArray,
            ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);

            // create checkout form for one time payment with paymentRequest
            $requestOneTimePayment = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest;
            $requestOneTimePayment->setPrice($newDiscountedPrice);
            $requestOneTimePayment->setPaidPrice($newDiscountedPrice);
            $requestOneTimePayment->setCallbackUrl(route('dashboard.user.payment.iyzico.prepaid.callback'));
            $requestOneTimePayment->setEnabledInstallments([1, 2, 3, 6, 9]);
            $requestOneTimePayment->setBuyer($buyer);
            $requestOneTimePayment->setShippingAddress($address);
            $requestOneTimePayment->setBillingAddress($address);
            $requestOneTimePayment->setBasketItems([$basketItem_0]);

            $checkoutform = \Iyzipay\Model\CheckoutFormInitialize::create($requestOneTimePayment, $iyzipayActions->getConfig());
            if ($checkoutform->getStatus() === 'failure') {
                $errorCode = $checkoutform->getErrorCode();
                $errorMessage = $checkoutform->getErrorMessage();

                return back()->with([
                    'message' => __('Please enter valid information!') . " Error Code: $errorCode - $errorMessage",
                    'type'    => 'error',
                ]);
            }
            // function did not work out for now. may be we can turn back after
            // $checkoutform = $iyzipayActions->createOneTimePayment($paymentRequest);

            // Since we can not transfer anything except token id to callback page we must use a middle step
            // We are going to save token id to CustomSettings table and retrieve it from callback page.
            $customSettings = new CustomSettings;
            $customSettings->key = $checkoutform->getToken();
            $customSettings->value_str = strval($plan->id);
            $customSettings->save();
        } catch (Exception $th) {
            $exception = Str::before($th->getMessage(), ':');
        }

        return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE . '.pay', compact('plan', 'taxRate', 'taxValue', 'newDiscountedPrice', 'gateway', 'exception', 'currency', 'checkoutform'));
    }

    public static function iyzicoPrepaidCallback(Request $request)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $couponID = null;
        $plan = null;
        $exception = null;
        $settings = Setting::getCache();
        $user = Auth::user();
        if ($request->token === null) {
            return back()->with(['message' => __('Token is missing'), 'type' => 'error']);
        }

        try {
            DB::beginTransaction();
            $iyzipayActions = self::retrieveGatewaySettings();
            // create request of one time payment result
            $checkoutRequest = [
                'token' => $request->token,
            ];
            // retrieve one time payment result
            $checkoutresult = $iyzipayActions->retrieveOneTimePayment($checkoutRequest);
            if ($checkoutresult->getStatus() === 'success' && ($checkoutresult->getPaymentStatus() === 'SUCCESS' || $checkoutresult->getPaymentStatus() === 'success')) {
                // Since we could not transfer anything except token id to callback page we must use a middle step
                // We saved token id to CustomSettings table and retrieve it now
                $customSettings = CustomSettings::where('key', $request->token)->first();
                if ($customSettings === null) {
                    // if we can't get plan id, just save it with a warning. So user can check from iyzico backend.
                    $payment = new UserOrder;
                    $payment->order_id = $checkoutresult->getPaymentId();
                    $payment->plan_id = 'Missing Plan Id. Check with token and order id.';
                    $payment->type = 'prepaid';
                    $payment->user_id = $user->id;
                    $payment->payment_type = self::$GATEWAY_CODE;
                    $payment->price = 0;
                    $payment->affiliate_earnings = 0;
                    $payment->status = 'Success with token:' . $request->token;
                    $payment->country = $user->country ?? 'Unknown';
                    $payment->save();

                    return back()->with(['message' => __('Token is missing'), 'type' => 'error']);
                }
                // get plan id from CustomSettings table
                $planId = $customSettings->value_str;
                // get plan
                $plan = Plan::where('id', $planId)->first();
                $newDiscountedPrice = $plan->price;
                $taxRate = $gateway->tax;
                $taxValue = taxToVal($plan->price, $taxRate);
                $newDiscountedPrice = $plan->price;
                session_start(); // Start the session if not already started
                if (isset($_SESSION['applied_coupon'])) {
                    $appliedCouponData = $_SESSION['applied_coupon'];
                    if ($appliedCouponData['plan_id'] == $planId) {
                        $appliedCoupon = $appliedCouponData['coupon'];
                        $newDiscountedPrice = $plan->price - ($plan->price * ($appliedCoupon->discount / 100));
                        $couponID = $appliedCoupon->discount;
                    }
                    unset($_SESSION['applied_coupon']);
                }
                session_write_close();
                $newDiscountedPrice += $taxValue;

                // save checkout to orders
                $payment = new UserOrder;
                $payment->order_id = $checkoutresult->getPaymentId();
                $payment->plan_id = $plan->id;
                $payment->type = 'prepaid';
                $payment->user_id = $user->id;
                $payment->payment_type = self::$GATEWAY_CODE;
                $payment->price = $newDiscountedPrice;
                $payment->affiliate_earnings = ($newDiscountedPrice * $settings->affiliate_commission_percentage) / 100;
                $payment->status = 'Success';
                $payment->country = $user->country ?? 'Unknown';
                $payment->tax_rate = $taxRate;
                $payment->tax_value = $taxValue;
                $payment->save();

                self::creditIncreaseSubscribePlan($user, $plan);
                // delete custom settings since we do not need it anymore
                $customSettings->delete();
                \App\Models\Usage::getSingle()->updateSalesCount($newDiscountedPrice);
                CreateActivity::for($user, __('Purchased'), $plan->name . ' ' . __('Token Pack'));
                EmailPaymentConfirmation::create($user, $plan)->send();
            }
            DB::commit();

            return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE . '.result', compact('plan', 'gateway', 'exception', 'currency', 'checkoutresult'));
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> iyzicoPrepaidCallback(): ' . $ex->getMessage());

            return back()->with(['message' => Str::before($ex->getMessage(), ':'), 'type' => 'error']);
        }
    }

    // other functions
    public static function subscribeCancel($internalUser = null): ?RedirectResponse
    {
        $user = $internalUser ?? Auth::user();
        $iyzipayActions = self::retrieveGatewaySettings();
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $plan = Plan::where('id', $activeSub->plan_id)->first();

            try {
                if ($activeSub->stripe_status === 'iyzico_approved') {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = Carbon::now();
                    $activeSub->save();
                    self::creditDecreaseCancelPlan($user, $plan);
                    CreateActivity::for($user, __('Cancelled'), 'Subscription plan');
                    if ($internalUser !== null) {
                        return back()->with(['message' => __('User subscription is cancelled succesfully.'), 'type' => 'success']);
                    }

                    return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
                }
                $cancelSubscriptionRequest = json_decode(json_encode([
                    'subscriptionReferenceCode' => $activeSub->stripe_id,
                ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
                $cancelSubscription = $iyzipayActions->cancelSubscription($cancelSubscriptionRequest);
                if ($cancelSubscription) {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = Carbon::now();
                    $activeSub->save();
                    self::creditDecreaseCancelPlan($user, $plan);
                    CreateActivity::for($user, __('Cancelled'), $plan->name);
                    if ($internalUser) {
                        return back()->with(['message' => __('User subscription is cancelled succesfully.'), 'type' => 'success']);
                    }

                    return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
                }

                return back()->with(['message' => __('Your subscription could not be cancelled.'), 'type' => 'error']);

            } catch (Exception $ex) {
                Log::error(self::$GATEWAY_CODE . '-> saveAllProducts(): ' . $ex->getMessage());

                return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
            }
        }

        return back()->with(['message' => __('Your subscription not found.'), 'type' => 'error']);
    }

    public static function getDaysLeft($timestamp)
    {
        // function that returns days left from now to given timestamp, if timestamp is null or days left is less than 0, returns null
        if ($timestamp === null) {
            return null;
        }
        // Convert millisecond timestamp to seconds as iyzico sends timestamp in milliseconds
        $timestampInSeconds = $timestamp / 1000;
        $now = Carbon::now();
        $ends = Carbon::createFromTimestamp($timestampInSeconds);
        $daysLeft = $now->diffInDays($ends, false);
        if ($daysLeft < 0) {
            return null;
        }

        return $daysLeft;
    }

    /**
     * @throws JsonException
     */
    public static function getSubscriptionDaysLeft(): ?int
    {
        $iyzipayActions = self::retrieveGatewaySettings();
        $userId = Auth::user()->id;
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $plan = Plan::where('id', $activeSub->plan_id)->first();
            if ($activeSub->stripe_status === 'iyzico_approved') {
                return Carbon::now()->diffInDays(Carbon::parse($activeSub->ends_at));
            }
            $subscriptionRequest = json_decode(json_encode([
                'subscriptionReferenceCode' => $activeSub->stripe_id,
            ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
            $subscription = $iyzipayActions->getSubscriptionDetails($subscriptionRequest);
            if (Str::lower($subscription->getSubscriptionStatus()) === 'active') {
                if ($subscription->getTrialEndDate()) {
                    $trialDaysLeft = self::getDaysLeft($subscription->getTrialEndDate());
                    if ($trialDaysLeft) {
                        return $trialDaysLeft;
                    }
                } else {
                    $orders = $subscription->getOrders();
                    foreach ($orders as $iValue) {
                        if ($iValue->orderStatus === 'WAITING') {
                            // do nothing
                        } else {
                            $daysLeft = self::getDaysLeft($iValue->endPeriod);
                            if ($daysLeft) {
                                return $daysLeft;
                            }

                            return 0;
                        }
                    }
                }
            } else {
                return 0;
            }
        }

        return null;
    }

    /**
     * @throws JsonException
     */
    public static function checkIfTrial(): bool
    {
        $iyzipayActions = self::retrieveGatewaySettings();
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            if ($activeSub->stripe_status === 'iyzico_approved') {
                return false;
            }
            $plan = Plan::where('id', $activeSub->plan_id)->first();
            // get subscription
            $subscriptionRequest = json_decode(json_encode([
                'subscriptionReferenceCode' => $activeSub->stripe_id,
            ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
            $subscription = $iyzipayActions->getSubscriptionDetails($subscriptionRequest);
            if ((Str::lower($subscription->getSubscriptionStatus()) === 'active') && $subscription->getTrialEndDate()) {
                $trialDaysLeft = self::getDaysLeft($subscription->getTrialEndDate());

                return $trialDaysLeft !== null;
            }

            return false;
        }

        return false;
    }

    /**
     * @throws JsonException
     */
    public static function getSubscriptionRenewDate(): ?string
    {
        $iyzipayActions = self::retrieveGatewaySettings();
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            if ($activeSub->stripe_status === 'iyzico_approved') {
                return Carbon::createFromTimeStamp($activeSub->ends_at)->format('F jS, Y');
            }

            $plan = Plan::where('id', $activeSub->plan_id)->first();
            $subscriptionRequest = json_decode(json_encode([
                'subscriptionReferenceCode' => $activeSub->stripe_id,
            ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
            $subscription = $iyzipayActions->getSubscriptionDetails($subscriptionRequest);
            if (Str::lower($subscription->getSubscriptionStatus()) === 'active') {
                $orders = $subscription->getOrders();
                foreach ($orders as $iValue) {
                    if ($iValue->orderStatus === 'WAITING') {
                        //
                    } else {
                        return Carbon::createFromTimestamp($iValue->endPeriod / 1000)->format('F jS, Y');

                        break;
                    }
                }

                return Carbon::now()->format('F jS, Y');
            }

            $activeSub->stripe_status = 'cancelled';
            $activeSub->ends_at = Carbon::now();
            $activeSub->save();

            return Carbon::now()->format('F jS, Y');
        }

        return null;
    }

    /**
     * @throws JsonException
     */
    public static function getSubscriptionStatus(): ?bool
    {
        $iyzipayActions = self::retrieveGatewaySettings();
        $userId = Auth::user()->id;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $plan = Plan::where('id', $activeSub->plan_id)->first();
            if ($activeSub->stripe_status === 'iyzico_approved') {
                // later we can renew from here or from command
                return true;
            } else {
                $subscriptionRequest = json_decode(json_encode([
                    'subscriptionReferenceCode' => $activeSub->stripe_id,
                ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
                $subscription = $iyzipayActions->getSubscriptionDetails($subscriptionRequest);
                if (Str::lower($subscription->getSubscriptionStatus()) === 'active') {
                    return true;
                } else {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = Carbon::now();
                    $activeSub->save();

                    return false;
                }
            }
        }

        return null;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public static function cancelSubscribedPlan($planId, $subsId)
    {
        $iyzipayActions = self::retrieveGatewaySettings();
        $user = Auth::user();
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $plan = Plan::where('id', $planId)->first();
            if ($activeSub->stripe_status === 'iyzico_approved') {
                $activeSub->stripe_status = 'cancelled';
                $activeSub->ends_at = Carbon::now();
                $activeSub->save();
                self::creditDecreaseCancelPlan($user, $plan);

                return true;
            } else {
                // cancel subscription
                $cancelSubscriptionRequest = json_decode(json_encode([
                    'subscriptionReferenceCode' => $activeSub->stripe_id,
                ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
                $cancelSubscription = $iyzipayActions->cancelSubscription($cancelSubscriptionRequest);
                if ($cancelSubscription) {
                    $activeSub->stripe_status = 'cancelled';
                    $activeSub->ends_at = Carbon::now();
                    $activeSub->save();
                    self::creditDecreaseCancelPlan($user, $plan);

                    return true;
                }
            }
        }

        return false;
    }

    public static function updateUserData(): void
    {
        $history = OldGatewayProducts::where([
            'gateway_code' => self::$GATEWAY_CODE,
            'status'       => 'check',
        ])->get();
        if ($history) {
            $iyzipayActions = self::retrieveGatewaySettings();
            foreach ($history as $record) {
                // check record current status from gateway
                $lookingFor = $record->old_price_id;
                // search subscriptions for record
                $subs = Subscriptions::where([
                    'stripe_status' => 'active',
                    'stripe_price'  => $lookingFor,
                ])->get();
                if ($subs != null) {
                    foreach ($subs as $sub) {
                        // cancel subscription
                        $cancelSubscriptionRequest = json_decode(json_encode([
                            'subscriptionReferenceCode' => $sub->stripe_id,
                        ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
                        $cancelSubscription = $iyzipayActions->cancelSubscription($cancelSubscriptionRequest);
                        // cancel subscription from our database
                        $sub->stripe_status = 'cancelled';
                        $sub->ends_at = Carbon::now();
                        $sub->save();
                    }
                }
                $record->status = 'checked';
                $record->save();
            }
        }
    }

    /**
     * @throws JsonException
     */
    public static function iyzicoProductsList(): bool|string
    {
        $iyzipayActions = self::retrieveGatewaySettings();
        $req = json_decode(json_encode([
            'itemPage'  => 1,
            'itemCount' => 100,
        ], JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
        $products = $iyzipayActions->listSubscriptionProducts($req);

        return json_encode($products, JSON_THROW_ON_ERROR);
    }

    public static function verifyIncomingJson(Request $request): bool
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->first();
        if ($gateway === null) {
            abort(404);
        }

        $currentSubscription = getCurrentActiveSubscription();

        try {
            // below check mechanism is set from https://dev.iyzipay.com/tr/webhooks to check regular webhooks
            // but after consulting to customer service of iyzico, we learned that we get different json data from regular webhooks for recurring payments.
            // as of last mail - pasted below - we can not use this validation mechanism for recurring payments.
            // hence we can only check if incoming json is valid - contains all fields - or not.
            // -------------------------------------------------------
            /*
                Webhook için farklı bir dokümanımız maalesef bulunmamaktadır. Mevcut standart webhook bildirimi için validasyon gerçekleştirebilirsiniz ancak subscription tekrarlı ödemelerin webhook bildiriminde dönen değerler farklı olduğundan dolayı bu kısımda ek olarak bir doküman bulunmamaktadır.
                https://docs.iyzico.com/ek-servisler/webhook
                https://dev.iyzipay.com/tr/webhooks
            */
            // -------------------------------------------------------
            $payload = json_decode($request->getContent(), false, 512, JSON_THROW_ON_ERROR);
            if (! $payload->orderReferenceCode || ! $payload->customerReferenceCode || ! $payload->subscriptionReferenceCode || ! $payload->iyziReferenceCode || ! $payload->iyziEventType || ! $payload->iyziEventTime) {
                return false;
            }
            if (Carbon::parse($currentSubscription->created_at)->diffInMinutes(Carbon::parse($payload->create_time)) < 5) {
                return false;
            }

            // / below code is not applicable for recurring payments, please see the comment above
            if ($request->hasHeader('X-IYZ-SIGNATURE')) {
                $incoming_signature = $request->header('X-IYZ-SIGNATURE');
            } else {
                return false;
            }
            $payload = json_decode($request->getContent());
            // get secret key from gateway according to mode
            $secretKey = $gateway->mode === 'sandbox' ? $gateway->sandbox_client_secret : $gateway->live_client_secret;
            // get iyziEventType from payload
            if ($payload->iyziEventType) {
                $iyziEventType = $payload->iyziEventType;
            } else {
                return false;
            }
            // get iyziReferenceCode from payload
            if ($payload->iyziReferenceCode) {
                $iyziReferenceCode = $payload->iyziReferenceCode;
            } else {
                return false;
            }
            // Concatenate the values
            $stringToBeHashed = $secretKey . $iyziEventType . $iyziReferenceCode;
            // Log::info("stringToBeHashed : " . $stringToBeHashed);
            // Hash the concatenated string using SHA-1 and then base64 encode it
            $hash = base64_encode(sha1($stringToBeHashed, true));

            // Log::info("hash : " . $hash);
            // Compare the hash with the incoming signature
            return $hash === $incoming_signature;
        } catch (Exception $th) {
            Log::error('(Webhooks) IyzicoService::verifyIncomingJson(): ' . $th->getMessage());
        }

        return false;
    }

    public function handleWebhook(Request $request)
    {
        $verified = self::verifyIncomingJson($request);
        if ($verified) {
            // Retrieve the JSON payload
            $payload = $request->getContent();
            // Fire the event with the payload
            event(new IyzicoWebhookEvent($payload));

            return response()->json(['success' => true]);
        }
        abort(404);
    }

    // helper functions
    private static function getIyzicoPriceId($planId)
    {
        $plan = Plan::where('id', $planId)->first();
        if ($plan) {
            $product = GatewayProducts::where(['plan_id' => $planId, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product) {
                return $product->price_id;
            }

            return null;
        }

        return null;
    }

    private static function retrieveGatewaySettings($gateway = null)
    {
        $gateway = $gateway ?? Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $data = [
            'apiKey'       => $gateway->mode === 'live' ? $gateway->live_client_id : $gateway->sandbox_client_id,
            'apiSecretKey' => $gateway->mode === 'live' ? $gateway->live_client_secret : $gateway->sandbox_client_secret,
            'baseUrl'      => $gateway->mode === 'live' ? $gateway->base_url : $gateway->sandbox_url,
            'currency'     => $currency,
        ];

        return new IyzipayActions($data['apiKey'], $data['apiSecretKey'], $data['baseUrl'], Locale::TR, $data['currency']);
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            'code'                  => 'iyzico',
            'title'                 => 'iyzico',
            'link'                  => 'https://www.iyzico.com/',
            'active'                => 0,
            'available'             => 1,
            'img'                   => '/assets/img/payments/iyzico.svg',
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
            'base_url'              => 1,
            'sandbox_url'           => 1,
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
