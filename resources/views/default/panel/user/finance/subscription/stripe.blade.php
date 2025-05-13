@extends('panel.layout.app')
@section('title', __('Subscription Payment'))
@section('titlebar_actions', '')

@section('additional_css')
    <style>
        #payment-form {
            width: 100%;
            /* min-width: 500px; */
            align-self: center;
            box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.1),
                0px 2px 5px 0px rgba(50, 50, 93, 0.1), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.07);
            border-radius: 7px;
            padding: 40px;
        }

        .hidden {
            display: none;
        }

        #payment-message {
            color: rgb(105, 115, 134);
            font-size: 16px;
            line-height: 20px;
            padding-top: 12px;
            text-align: center;
        }

        #payment-element {
            margin-bottom: 24px;
        }

        /* Buttons and links */
        button {
            background: #5469d4;
            font-family: Arial, sans-serif;
            color: #ffffff;
            border-radius: 4px;
            border: 0;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: block;
            transition: all 0.2s ease;
            box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
            width: 100%;
        }

        button:hover {
            filter: contrast(115%);
        }

        button:disabled {
            opacity: 0.5;
            cursor: default;
        }

        /* spinner/processing state, errors */
        .spinner,
        .spinner:before,
        .spinner:after {
            border-radius: 50%;
        }

        .spinner {
            color: #ffffff;
            font-size: 22px;
            text-indent: -99999px;
            margin: 0px auto;
            position: relative;
            width: 20px;
            height: 20px;
            box-shadow: inset 0 0 0 2px;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
        }

        .spinner:before,
        .spinner:after {
            position: absolute;
            content: "";
        }

        .spinner:before {
            width: 10.4px;
            height: 20.4px;
            background: #5469d4;
            border-radius: 20.4px 0 0 20.4px;
            top: -0.2px;
            left: -0.2px;
            -webkit-transform-origin: 10.4px 10.2px;
            transform-origin: 10.4px 10.2px;
            -webkit-animation: loading 2s infinite ease 1.5s;
            animation: loading 2s infinite ease 1.5s;
        }

        .spinner:after {
            width: 10.4px;
            height: 10.2px;
            background: #5469d4;
            border-radius: 0 10.2px 10.2px 0;
            top: -0.1px;
            left: 10.2px;
            -webkit-transform-origin: 0px 10.2px;
            transform-origin: 0px 10.2px;
            -webkit-animation: loading 2s infinite ease;
            animation: loading 2s infinite ease;
        }

        @-webkit-keyframes loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @media only screen and (max-width: 600px) {
            form {
                width: 80vw;
                min-width: initial;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-8 col-lg-8">
                    @include('panel.user.finance.coupon.index')
                    <form
                        id="payment-form"
                        action="{{ route('dashboard.user.payment.subscription.checkout', ['gateway' => 'stripe']) }}"
                        method="post"
                    >
                        @csrf
                        {{-- <input type="hidden" name="planID" value="{{ $plan->id }}">
                        <input type="hidden" name="couponID" id="coupon">
                        <input type="hidden" name="orderID" value="{{$order_id}}">
                        <input type="hidden" name="payment_method" class="payment-method">
                        <input type="hidden" name="gateway" value="stripe"> --}}
                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <div id="payment-element">
                                    <!--Stripe.js injects the Payment Element-->
                                </div>
                                <x-button
                                    class="w-full"
                                    id="submit"
                                    type="{{ $app_is_demo ? 'button' : 'submit' }}"
                                    onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                                >
                                    <div
                                        class="spinner hidden"
                                        id="spinner"
                                    ></div>
                                    @if ($plan->trial_days != 0 && $plan->frequency != 'lifetime_monthly' && $plan->frequency != 'lifetime_yearly' && $plan->price > 0)
                                        <span id="button-text">
                                            {{ __('Start free trial') }}
                                            {{ __('with') }}
                                            <img
                                                class="h-auto w-24"
                                                src="{{ custom_theme_url('/images/payment/stripe.svg') }}"
                                                height="29px"
                                                alt="Stripe"
                                            >
                                        </span>
                                    @else
                                        <span id="button-text">{{ __('Pay') }}
                                            {!! displayCurr(currency()->symbol, $plan->price, $taxValue, $newDiscountedPrice) !!}
                                            {{ __('with') }}
                                            <img
                                                class="h-auto w-24"
                                                src="{{ custom_theme_url('/images/payment/stripe.svg') }}"
                                                height="29px"
                                                alt="Stripe"
                                            >
                                        </span>
                                    @endif
                                </x-button>
                                <div
                                    class="hidden"
                                    id="payment-message"
                                ></div>
                            </div>
                        </div>
                    </form>
                    <br>
                    <p>{{ __('By purchasing you confirm our') }} <a href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                </div>
                <div class="col-sm-4 col-lg-4">
                    @include('panel.user.finance.partials.plan_card')
                </div>
            </div>
        </div>
    </div>
    {{-- addressModal --}}
    <div
        class="modal fade"
        id="addressModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addressModalLabel"
        aria-hidden="true"
    >
        <div
            class="modal-dialog modal-dialog-centered"
            role="document"
        >
            <div class="modal-content">
                <form
                    action="{{ route('dashboard.user.payment.updateAddressDetails', ['gateway' => 'stripe']) }}"
                    method="post"
                >
                    @csrf
                    <div class="modal-header">
                        <h5
                            class="modal-title"
                            id="addressModalLabel"
                        >{{ __('Please fill in your address') }}</h5>
                        <button
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div
                            class="alert alert-warning"
                            role="alert"
                        >
                            {{ __('As per Indian regulations, export transactions require a customer full address.') }}
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <x-forms.input
                                class="form-control"
                                id="phone"
                                data-mask="+0000000000000"
                                data-mask-visible="true"
                                type="text"
                                name="phone"
                                placeholder="+000000000000"
                                autocomplete="off"
                                value="{{ auth()->user()->phone }}"
                            />
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('Address Line 1') }}</label>
                            <x-forms.input
                                id="address"
                                type="text"
                                name="address"
                                value="{{ auth()->user()->address }}"
                            />
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('Postal Code') }}</label>
                            <x-forms.input
                                id="postal"
                                type="text"
                                name="postal"
                                value="{{ auth()->user()->postal }}"
                            />
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('City') }}</label>
                            <x-forms.input
                                id="city"
                                type="text"
                                name="city"
                                value="{{ auth()->user()->city }}"
                            />
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('State') }}</label>
                            <x-forms.input
                                id="state"
                                type="text"
                                name="state"
                                value="{{ auth()->user()->state }}"
                            />
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('Country') }}</label>
                            <select
                                class="form-select"
                                id="country"
                                type="text"
                                name="country"
                            >
                                @include('panel.admin.users.countries')
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            type="button"
                        >{{ __('Close') }}</button>
                        <button
                            class="btn btn-primary"
                            type="submit"
                        >{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('https://js.stripe.com/v3/') }}"></script>
    <script>
        (() => {
            "use strict";

            const stripe = Stripe(
                "{{ $gateway->mode == 'live' ? $gateway->live_client_id : $gateway->sandbox_client_id }}");
            let elements;
            initialize();
            if (!"{{ $paymentIntent['client_secret'] }}".startsWith("set")) {
                checkStatus();
            }
            document.querySelector("#payment-form").addEventListener("submit", handleSubmit);
            async function initialize() {
                const clientSecret = "{{ $paymentIntent['client_secret'] }}";
                elements = stripe.elements({
                    clientSecret
                });
                const paymentElementOptions = {
                    layout: "tabs",
                    business: {
                        name: "{{ config('app.name') }}"
                    },
                };
                const paymentElement = elements.create("payment", paymentElementOptions);
                paymentElement.mount("#payment-element");
            }
            async function handleSubmit(e) {
                e.preventDefault();
                setLoading(true);
                const secret = "{{ $paymentIntent['client_secret'] }}";
                let url = `{{ route('dashboard.user.payment.subscription.checkout', ['gateway' => ':gateway']) }}`;
                url = url.replace(':gateway', 'stripe');
                if (typeof rewardful !== 'undefined') {
                    rewardful('ready', function() {
                        if (Rewardful.referral) {
                            url =
                                `{{ route('dashboard.user.payment.subscription.checkout', ['gateway' => ':gateway'], ['referral' => ':referral']) }}`;
                            url = url.replace(':referral', Rewardful.referral);
                            url = url.replace(':gateway', 'stripe');
                        }
                    });
                }
                const confirmParams = {
                    elements,
                    confirmParams: {
                        return_url: url,
                        payment_method_data: {
                            billing_details: {
                                name: '{{ auth()->user()?->fullName() }}',
                                email: '{{ auth()->user()->email }}',
                                phone: '{{ auth()->user()->phone }}',
                                address: {
                                    country: '{{ \App\Services\CountryCodeService::getCountryCode(auth()->user()->country) }}',
                                    city: '{{ auth()->user()->city }}',
                                    line1: '{{ auth()->user()->address }}',
                                    postal_code: '{{ auth()->user()->postal }}',
                                    state: '{{ auth()->user()->state }}',
                                }
                            }
                        },
                    },
                };
                if (!secret.startsWith("set")) {
                    const error = await stripe.confirmPayment(confirmParams);
                } else {
                    const error = await stripe.confirmSetup(confirmParams);
                }
                const confirmFunction = secret.startsWith("set") ? stripe.confirmSetup : stripe.confirmPayment;
                const error = await confirmFunction(confirmParams);
                if (error.error.type === "invalid_request_error" && error.error.message.startsWith("As per Indian regulations,")) {
                    $('#addressModal').modal('show');
                } else {
                    showMessage(error.error.message);
                }
                setLoading(false);
            }
            async function checkStatus() {
                const clientSecret = "{{ $paymentIntent['client_secret'] }}";
                if (!clientSecret) {
                    return;
                }
                const {
                    paymentIntent
                } = await stripe.retrievePaymentIntent(clientSecret);

                switch (paymentIntent.status) {
                    case "succeeded":
                        showMessage("Payment succeeded!");
                        break;
                    case "processing":
                        showMessage("Your payment is processing.");
                        break;
                    case "requires_payment_method":
                        showMessage("Select a valid payment method to proceed.");
                        break;
                    default:
                        break;
                }
            }

            function showMessage(messageText) {
                const messageContainer = document.querySelector("#payment-message");
                messageContainer.classList.remove("hidden");
                messageContainer.textContent = messageText;
                setTimeout(() => {
                    messageContainer.classList.add("hidden");
                    messageContainer.textContent = "";
                }, 7000);
            }

            function setLoading(isLoading) {
                const submitButton = document.querySelector("#submit");
                const spinner = document.querySelector("#spinner");
                const buttonText = document.querySelector("#button-text");

                submitButton.disabled = isLoading;
                spinner.classList.toggle("hidden", !isLoading);
                buttonText.classList.toggle("hidden", isLoading);
            }

        })();
    </script>
@endpush
