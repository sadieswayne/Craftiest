@extends('panel.layout.app')
@section('title', __('Subscription Payment'))
@section('titlebar_actions', '')

@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            @if ($exception != null)
                <h2 class="text-danger">{{ $exception }}</h2>
            @else
                <div class="row row-cards">

                    <div class="col-sm-8 col-lg-8">
                        @include('panel.user.finance.coupon.index')
                        <form id="paymentForm"
                            action="{{ route('dashboard.user.payment.subscription.checkout', ['gateway' => 'paystack']) }}"
                            method="post">
                            @csrf
                            <input type="hidden" name="gateway" value="paystack">
                            <input id="coupon" type="hidden" name="couponID">
                            <input type="hidden" name="planID" value="{{ $planId }}">
                            <input type="hidden" name="orderID" value="{{ $orderId }}">
                            <input type="hidden" name="productID" value="{{ $productId }}">
                            <input type="hidden" name="billingPlanId" value="{{ $billingPlanId }}">
                            <div class="form-submit">
                                <button class="btn btn-info w-full"
                                    @if ($app_is_demo) type="button" onclick="return toastr.info('This feature is disabled in Demo version.')" @else type="submit" @endif>
                                    <span id="button-text">{{ __('Pay') }}
                                        {!! displayCurr(currency()->symbol, $plan->price, $taxValue, $newDiscountedPrice) !!}
                                        {{ __('with') }} &nbsp;&nbsp;
                                        <img src="{{ custom_theme_url('/images/payment/paystack-2.svg') }}" height="60"
                                            alt="Paystack">
                                    </span>
                                </button>
                            </div>
                        </form>
                        <p class="mt-3">{{ __('By purchasing you confirm our') }} <a
                                href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                    </div>
                    <div class="col-sm-4 col-lg-4">
                        @include('panel.user.finance.partials.plan_card')
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('https://js.paystack.co/v1/inline.js') }}"></script>
    <script>
        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener("submit", payWithPaystack, false);

        function payWithPaystack(e) {
            e.preventDefault();
            let handlerOptions = {
                email: "{{ Auth::user()->email }}",
                onClose: function() {
                    // toastr.error("{{ __('Window closed') }}");
                },
                callback: function(response) {
                    let message = 'Payment complete! Reference: ' + response.reference;
                    toastr.success(message);
                    let res = document.createElement('input')
                    res.setAttribute('type', 'hidden')
                    res.setAttribute('name', 'response')
                    res.setAttribute('value', JSON.stringify(response))
                    paymentForm.appendChild(res)
                    paymentForm.submit();
                }
            };
            if (@json($gateway->mode) == 'live') {
                handlerOptions.key = "{{ $gateway->live_client_id }}";
            } else {
                handlerOptions.key = "{{ $gateway->sandbox_client_id }}";
            }
            if (@json($billingPlanId) != "Not Needed") {
                handlerOptions.plan = "{{ $billingPlanId }}";
            } else {
                handlerOptions.amount = "{{ $newDiscountedPrice * 100 }}";
                handlerOptions.currency = "{{ currency()->code }}";
            }
            let handler = PaystackPop.setup(handlerOptions);
            handler.openIframe();
        }
    </script>
    <script>
        $(document).ready(function() {
            var currentURL = window.location.href;
            if (currentURL.includes('coupon=')) {
                var couponValue = getParameterByName('coupon', currentURL);
                document.getElementById('coupon').value = couponValue;
            }
        });

        function getParameterByName(name, url) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }
    </script>
@endpush
