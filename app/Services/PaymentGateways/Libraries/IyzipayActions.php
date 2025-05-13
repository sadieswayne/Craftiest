<?php

namespace App\Services\PaymentGateways\Libraries;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Support\Facades\Log;

// a class that makes subscription and payments with iyzipay
class IyzipayActions // extends Controller
{
    // api keys
    private $config;

    // locale
    private $locale;

    // currency
    private $currency;

    public function __construct($apiKey, $apiSecretKey, $baseUrl, $locale = \Iyzipay\Model\Locale::TR, $currency = \Iyzipay\Model\Currency::TL)
    {
        $this->config = new \Iyzipay\Options;
        $this->config->setApiKey($apiKey);
        $this->config->setSecretKey($apiSecretKey);
        $this->config->setBaseUrl($baseUrl);
        $this->locale = $locale;
        $this->currency = $currency;

        // Log::info("iyzipayActions constructor called : " . $apiKey . " " . $apiSecretKey . " " . $baseUrl . " " . $locale . " " . $currency);
    }

    // get config
    public function getConfig()
    {
        return $this->config;
    }

    // get locale
    public function getLocale()
    {
        return $this->locale;
    }

    // generate random string with given length
    public function generateRandomString($length = 12)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    // generate random number with given length
    public function generateRandomNumber($length = 12)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length);
    }

    // ************************************************

    // ******* SUBSCRIPTION CUSTOMER ACTIONS **********

    // ************************************************

    // create customer for \Iyzipay\Model\Customer
    public function createCustomer($request)
    {
        $customer = new \Iyzipay\Model\Customer;
        $customer->setName($request->name ?? '');
        $customer->setSurname($request->surname ?? '');
        $customer->setGsmNumber($request->gsmNumber ?? '');
        $customer->setEmail($request->email ?? '');
        $customer->setIdentityNumber($request->identityNumber ?? '');
        $customer->setShippingContactName($request->shippingContactName ?? '');
        $customer->setShippingCity($request->shippingCity ?? '');
        $customer->setShippingCountry($request->shippingCountry ?? '');
        $customer->setShippingAddress($request->shippingAddress ?? '');
        $customer->setShippingZipCode($request->shippingZipCode ?? '');
        $customer->setBillingContactName($request->billingContactName ?? '');
        $customer->setBillingCity($request->billingCity ?? '');
        $customer->setBillingCountry($request->billingCountry ?? '');
        $customer->setBillingAddress($request->billingAddress ?? '');
        $customer->setBillingZipCode($request->billingZipCode ?? '');

        return $customer;
    }

    // create a subscription customer for \Iyzipay\Request\Subscription\SubscriptionCreateCustomerRequest
    public function createSubscriptionCustomer($request)
    {
        $requestSubscriptionCustomer = new \Iyzipay\Request\Subscription\SubscriptionCreateCustomerRequest;
        $requestSubscriptionCustomer->setLocale($this->locale);
        $requestSubscriptionCustomer->setConversationId(self::generateRandomNumber());

        $customer = new \Iyzipay\Model\Customer;
        $customer->setName($request->name ?? '');
        $customer->setSurname($request->surname ?? '');
        $customer->setGsmNumber($request->gsmNumber ?? '');
        $customer->setEmail($request->email ?? '');
        $customer->setIdentityNumber($request->identityNumber ?? '');
        $customer->setShippingContactName($request->shippingContactName ?? '');
        $customer->setShippingCity($request->shippingCity ?? '');
        $customer->setShippingCountry($request->shippingCountry ?? '');
        $customer->setShippingAddress($request->shippingAddress ?? '');
        $customer->setShippingZipCode($request->shippingZipCode ?? '');
        $customer->setBillingContactName($request->billingContactName ?? '');
        $customer->setBillingCity($request->billingCity ?? '');
        $customer->setBillingCountry($request->billingCountry ?? '');
        $customer->setBillingAddress($request->billingAddress ?? '');
        $customer->setBillingZipCode($request->billingZipCode ?? '');

        $requestSubscriptionCustomer->setCustomer($customer);

        $subscriptionCustomer = \Iyzipay\Model\Subscription\SubscriptionCustomer::create($requestSubscriptionCustomer, $this->config);

        return $subscriptionCustomer;
    }

    // retrieve a subscription customer for \Iyzipay\Request\Subscription\SubscriptionRetrieveCustomerRequest
    public function retrieveSubscriptionCustomer($request)
    {
        $requestSubscriptionCustomer = new \Iyzipay\Request\Subscription\SubscriptionRetrieveCustomerRequest;
        $requestSubscriptionCustomer->setLocale($this->locale);
        $requestSubscriptionCustomer->setConversationId(self::generateRandomNumber());
        $requestSubscriptionCustomer->setCustomerReferenceCode($request->customerReferenceCode);

        $subscriptionCustomer = \Iyzipay\Model\Subscription\SubscriptionCustomer::retrieve($requestSubscriptionCustomer, $this->config);

        return $subscriptionCustomer;
    }

    // update a subscription customer for \Iyzipay\Request\Subscription\SubscriptionUpdateCustomerRequest
    public function updateSubscriptionCustomer($request)
    {
        $requestSubscriptionCustomer = new \Iyzipay\Request\Subscription\SubscriptionUpdateCustomerRequest;
        $requestSubscriptionCustomer->setLocale($this->locale);
        $requestSubscriptionCustomer->setConversationId(self::generateRandomNumber());
        $requestSubscriptionCustomer->setCustomerReferenceCode($request->customerReferenceCode);

        $customer = new \Iyzipay\Model\Customer;
        $customer->setName($request->name ?? '');
        $customer->setSurname($request->surname ?? '');
        $customer->setGsmNumber($request->gsmNumber ?? '');
        $customer->setEmail($request->email ?? '');
        $customer->setIdentityNumber($request->identityNumber ?? '');
        $customer->setShippingContactName($request->shippingContactName ?? '');
        $customer->setShippingCity($request->shippingCity ?? '');
        $customer->setShippingCountry($request->shippingCountry ?? '');
        $customer->setShippingDistrict($request->shippingDistrict ?? '');
        $customer->setShippingAddress($request->shippingAddress ?? '');
        $customer->setShippingZipCode($request->shippingZipCode ?? '');
        $customer->setBillingContactName($request->billingContactName ?? '');
        $customer->setBillingCity($request->billingCity ?? '');
        $customer->setBillingCountry($request->billingCountry ?? '');
        $customer->setBillingDistrict($request->billingDistrict ?? '');
        $customer->setBillingAddress($request->billingAddress ?? '');
        $customer->setBillingZipCode($request->billingZipCode ?? '');

        $requestSubscriptionCustomer->setCustomer($customer);

        $subscriptionCustomer = \Iyzipay\Model\Subscription\SubscriptionCustomer::update($requestSubscriptionCustomer, $this->config);

        return $subscriptionCustomer;
    }

    // delete a subscription customer for \Iyzipay\Request\Subscription\SubscriptionDeleteCustomerRequest
    public function deleteSubscriptionCustomer($request)
    {
        $requestSubscriptionCustomer = new \Iyzipay\Request\Subscription\SubscriptionDeleteCustomerRequest;
        $requestSubscriptionCustomer->setLocale($this->locale);
        $requestSubscriptionCustomer->setConversationId(self::generateRandomNumber());
        $requestSubscriptionCustomer->setCustomerReferenceCode($request->customerReferenceCode);

        $subscriptionCustomer = \Iyzipay\Model\Subscription\SubscriptionCustomer::delete($requestSubscriptionCustomer, $this->config);

        return $subscriptionCustomer;
    }

    // ************************************************

    // ******** SUBSCRIPTION PRODUCT ACTIONS **********

    // ************************************************

    // create a subscription product for \Iyzipay\Request\Subscription\SubscriptionCreateProductRequest
    public function createSubscriptionProduct($request)
    {

        $requestSubscriptionProduct = new \Iyzipay\Request\Subscription\SubscriptionCreateProductRequest;
        $requestSubscriptionProduct->setLocale($this->locale);
        $requestSubscriptionProduct->setConversationId(self::generateRandomNumber());
        $requestSubscriptionProduct->setName($request->name);
        $requestSubscriptionProduct->setDescription($request->description ?? '');

        $subscriptionProduct = \Iyzipay\Model\Subscription\SubscriptionProduct::create($requestSubscriptionProduct, $this->config);

        return $subscriptionProduct;
    }

    // retrieve a subscription product for \Iyzipay\Request\Subscription\SubscriptionRetrieveProductRequest
    public function retrieveSubscriptionProduct($request)
    {
        $requestSubscriptionProduct = new \Iyzipay\Request\Subscription\SubscriptionRetrieveProductRequest;
        $requestSubscriptionProduct->setLocale($this->locale);
        $requestSubscriptionProduct->setConversationId(self::generateRandomNumber());
        $requestSubscriptionProduct->setProductReferenceCode($request->productReferenceCode);

        $subscriptionProduct = \Iyzipay\Model\Subscription\SubscriptionProduct::retrieve($requestSubscriptionProduct, $this->config);

        return $subscriptionProduct;
    }

    // update a subscription product for \Iyzipay\Request\Subscription\SubscriptionUpdateProductRequest
    public function updateSubscriptionProduct($request)
    {
        $requestSubscriptionProduct = new \Iyzipay\Request\Subscription\SubscriptionUpdateProductRequest;
        $requestSubscriptionProduct->setLocale($this->locale);
        $requestSubscriptionProduct->setConversationId(self::generateRandomNumber());
        $requestSubscriptionProduct->setProductReferenceCode($request->productReferenceCode);
        $requestSubscriptionProduct->setName($request->name);
        $requestSubscriptionProduct->setDescription($request->description ?? '');

        $subscriptionProduct = \Iyzipay\Model\Subscription\SubscriptionProduct::update($requestSubscriptionProduct, $this->config);

        return $subscriptionProduct;
    }

    // delete a subscription product for \Iyzipay\Request\Subscription\SubscriptionDeleteProductRequest
    public function deleteSubscriptionProduct($request)
    {
        $requestSubscriptionProduct = new \Iyzipay\Request\Subscription\SubscriptionDeleteProductRequest;
        $requestSubscriptionProduct->setLocale($this->locale);
        $requestSubscriptionProduct->setConversationId(self::generateRandomNumber());
        $requestSubscriptionProduct->setProductReferenceCode($request->productReferenceCode);

        $subscriptionProduct = \Iyzipay\Model\Subscription\SubscriptionProduct::delete($requestSubscriptionProduct, $this->config);

        return $subscriptionProduct;
    }

    // get all subscription products for \Iyzipay\Request\Subscription\SubscriptionListProductsRequest
    public function listSubscriptionProducts($request)
    {
        $requestSubscriptionProduct = new \Iyzipay\Request\Subscription\SubscriptionListProductsRequest;
        $requestSubscriptionProduct->setPage($request->itemPage ?? 1);
        $requestSubscriptionProduct->setCount($request->itemCount ?? 50);

        $subscriptionProduct = \Iyzipay\Model\Subscription\RetrieveList::products($requestSubscriptionProduct, $this->config);

        return $subscriptionProduct;
    }

    // *****************************************************************

    // ************ SUBSCRIPTION PRICING PLAN ACTIONS ******************

    // *****************************************************************

    // create a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionCreatePricingPlanRequest
    public function createSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\Subscription\SubscriptionCreatePricingPlanRequest;
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(self::generateRandomNumber());
        $requestSubscriptionPricingPlan->setProductReferenceCode($request->productReferenceCode);
        $requestSubscriptionPricingPlan->setName($request->name ?? '');
        $requestSubscriptionPricingPlan->setCurrencyCode($this->currency);
        $requestSubscriptionPricingPlan->setPrice($request->price ?? 1);
        $requestSubscriptionPricingPlan->setPaymentInterval($request->paymentInterval ?? 'MONTHLY');
        $requestSubscriptionPricingPlan->setPaymentIntervalCount($request->paymentIntervalCount ?? 1);
        $requestSubscriptionPricingPlan->setPlanPaymentType($request->paymentType ?? 'RECURRING');
        $requestSubscriptionPricingPlan->setTrialPeriodDays($request->trialPeriodDays ?? 0);
        // $requestSubscriptionPricingPlan->setRecurrenceCount($request->recurrenceCount ?? 0);

        $subscriptionPricingPlan = \Iyzipay\Model\Subscription\SubscriptionPricingPlan::create($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }

    // retrieve a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionRetrievePricingPlanRequest
    public function retrieveSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\Subscription\SubscriptionRetrievePricingPlanRequest;
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(self::generateRandomNumber());
        $requestSubscriptionPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);

        $subscriptionPricingPlan = \Iyzipay\Model\Subscription\SubscriptionPricingPlan::retrieve($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }

    // update a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionUpdatePricingPlanRequest
    public function updateSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\Subscription\SubscriptionUpdatePricingPlanRequest;
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(self::generateRandomNumber());
        $requestSubscriptionPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestSubscriptionPricingPlan->setName($request->name ?? '');
        $requestSubscriptionPricingPlan->setTrialPeriodDays($request->trialPeriodDays ?? '');

        $subscriptionPricingPlan = \Iyzipay\Model\Subscription\SubscriptionPricingPlan::update($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }

    // delete a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionDeletePricingPlanRequest
    public function deleteSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\Subscription\SubscriptionDeletePricingPlanRequest;
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(self::generateRandomNumber());
        $requestSubscriptionPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);

        $subscriptionPricingPlan = \Iyzipay\Model\Subscription\SubscriptionPricingPlan::delete($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }

    // ************************************************

    // *************** BUYER ACTIONS ******************

    // ************************************************

    // create a buyer for \Iyzipay\Model\Buyer
    public function createBuyer($request)
    {
        $buyer = new \Iyzipay\Model\Buyer;
        $buyer->setId($request->id);
        $buyer->setName($request->name ?? '');
        $buyer->setSurname($request->surname ?? '');
        $buyer->setIdentityNumber($request->identityNumber ?? '');
        $buyer->setEmail($request->email ?? '');
        $buyer->setGsmNumber($request->gsmNumber ?? '');
        // $buyer->setRegistrationDate($request->registrationDate); //was optional so not added
        // $buyer->setLastLoginDate($request->lastLoginDate); //was optional so not added
        $buyer->setRegistrationAddress($request->registrationAddress ?? '');
        $buyer->setCity($request->city ?? '');
        $buyer->setCountry($request->country ?? '');
        $buyer->setZipCode($request->zipCode ?? '');
        $buyer->setIp($request->ip ?? '');

        return $buyer;
    }

    // ************************************************

    // ************ BASKET ITEM ACTIONS ******************

    // ************************************************

    // create a basket item for \Iyzipay\Model\BasketItem
    public function createBasketItem($request)
    {
        $basketItem = new \Iyzipay\Model\BasketItem;
        $basketItem->setId($request['basketItemId']);
        $basketItem->setName($request['name']);
        $basketItem->setCategory1($request['category1'] ?? '');
        // $basketItem->setCategory2($request['category2'] ?? "");
        $basketItem->setItemType($request['itemType'] ?? '');
        $basketItem->setPrice($request['price'] ?? '');

        return $basketItem;
    }

    // ************************************************

    // ************ ADDRESS ACTIONS ******************

    // ************************************************

    // create a address for \Iyzipay\Model\Address
    public function createAddress($request)
    {
        $address = new \Iyzipay\Model\Address;
        $address->setContactName($request->contactName ?? '');
        $address->setCity($request->city ?? '');
        $address->setCountry($request->country ?? '');
        $address->setAddress($request->address ?? '');
        $address->setZipCode($request->zipCode ?? '');

        return $address;
    }

    // ************************************************

    // ************ ONE TIME PAYMENT ACTIONS ******************

    // ************************************************

    // create a one time payment with \Iyzipay\Request\CreateCheckoutFormInitializeRequest
    public function createOneTimePayment($request)
    {
        $requestOneTimePayment = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest;
        $requestOneTimePayment->setLocale($this->locale);
        $requestOneTimePayment->setConversationId(self::generateRandomNumber());
        $requestOneTimePayment->setPrice($request->price ?? '');
        $requestOneTimePayment->setPaidPrice($request->paidPrice ?? '');
        $requestOneTimePayment->setCurrency($this->currency ?? '');
        $requestOneTimePayment->setBasketId($request->basketId ?? '');
        // $requestOneTimePayment->setPaymentGroup($request->paymentGroup ?? ""); // Optional so not used
        // $requestOneTimePayment->setPaymentSource($request->paymentSource ?? ""); // Optional so not used
        $requestOneTimePayment->setCallbackUrl($request->callbackUrl ?? '');
        $requestOneTimePayment->setEnabledInstallments($request->enabledInstallments ?? '');
        // $requestOneTimePayment->setDebitCardAllowed($request->debitCardAllowed ?? ""); // not in example page
        $requestOneTimePayment->setBuyer($request->buyer ?? '');
        $requestOneTimePayment->setShippingAddress($request->shippingAddress ?? '');
        $requestOneTimePayment->setBillingAddress($request->billingAddress ?? '');
        $requestOneTimePayment->setBasketItems($request->basketItems ?? '');

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($requestOneTimePayment, $this->config);

        return $checkoutFormInitialize;
    }

    // retrieve a one time payment result with \Iyzipay\Request\RetrieveCheckoutFormRequest
    public function retrieveOneTimePayment($request)
    {
        $requestOneTimePayment = new \Iyzipay\Request\RetrieveCheckoutFormRequest;
        $requestOneTimePayment->setLocale($this->locale);
        $requestOneTimePayment->setConversationId(self::generateRandomNumber());
        $requestOneTimePayment->setToken($request['token']);

        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($requestOneTimePayment, $this->config);

        return $checkoutForm;
    }

    // ************************************************

    // ************ SUBSCRIPTION ACTIONS ******************

    // ************************************************

    // create a subscription with \Iyzipay\Request\Subscription\SubscriptionCreateCheckoutFormRequest
    public function createSubscription($request)
    {
        $requestSubscription = new \Iyzipay\Request\Subscription\SubscriptionCreateCheckoutFormRequest;
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(self::generateRandomNumber());
        $requestSubscription->setCallbackUrl($request->callbackUrl);
        $requestSubscription->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestSubscription->setSubscriptionInitialStatus($request->subscriptionInitialStatus);
        $requestSubscription->setCustomer($request->customer);

        $checkoutFormInitialize = \Iyzipay\Model\Subscription\SubscriptionCheckoutForm::create($requestSubscription, $this->config);

        return $checkoutFormInitialize;
    }

    // get details of a subscription with \Iyzipay\Request\Subscription\SubscriptionDetailsRequest
    public function getSubscriptionDetails($request)
    {
        $requestSubscriptionDetails = new \Iyzipay\Request\Subscription\SubscriptionDetailsRequest;
        $requestSubscriptionDetails->setLocale($this->locale);
        $requestSubscriptionDetails->setConversationId(self::generateRandomNumber());
        $requestSubscriptionDetails->setSubscriptionReferenceCode($request->subscriptionReferenceCode);

        $subscriptionDetails = \Iyzipay\Model\Subscription\SubscriptionDetails::retrieve($requestSubscriptionDetails, $this->config);

        return $subscriptionDetails;
    }

    // cancel a subscription with \Iyzipay\Request\Subscription\SubscriptionCancelRequest
    public function cancelSubscription($request)
    {
        $requestSubscriptionCancel = new \Iyzipay\Request\Subscription\SubscriptionCancelRequest;
        $requestSubscriptionCancel->setLocale($this->locale);
        $requestSubscriptionCancel->setConversationId(self::generateRandomNumber());
        $requestSubscriptionCancel->setSubscriptionReferenceCode($request->subscriptionReferenceCode);

        $subscriptionCancel = \Iyzipay\Model\Subscription\SubscriptionCancel::cancel($requestSubscriptionCancel, $this->config);

        return $subscriptionCancel;
    }

    // ************************************************

    // ************ WEBHOOKS ACTIONS ******************

    // ************************************************

    // TODO: Will be implemented later

}
