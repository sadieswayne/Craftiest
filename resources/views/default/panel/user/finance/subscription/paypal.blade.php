@extends('panel.layout.app')
@section('title', __('Subscription Payment'))
@section('titlebar_actions', '')

@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-8 col-lg-8">
                    @include('panel.user.finance.coupon.index')
                    <div class="px-4" id="paypal-button-container"></div>
                    <br>
                    <p class="mt-3">{{ __('By purchasing you confirm our') }} <a
                            href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                </div>
                <div class="col-sm-4 col-lg-4">
                    @include('panel.user.finance.partials.plan_card')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @php
        $clientId = $gateway->mode == 'live' ? $gateway->live_client_id : $gateway->sandbox_client_id;
        $bilig = $billingPlanId == null ? '&currency=' . currency()->code : '&vault=true&intent=subscription';
    @endphp
    <script src="{{ custom_theme_url('https://www.paypal.com/sdk/js?client-id=' . $clientId . $bilig) }}"></script>
    <script>
        let couponValue = null;
        var bilingID = "{{ $billingPlanId ?? 'null' }}";
        var currentURL = window.location.href;
        if (currentURL.includes('coupon=')) {
            couponValue = getParameterByName('coupon', currentURL);
        }


        //its lifetime plan, take payment for one time
        if (bilingID == null) {
            paypal.Buttons({
                style: {
                    color: 'blue',
                    shape: 'pill',
                    label: 'pay',
                },
                createOrder() {
                    return fetch("{{ route('dashboard.user.payment.prepaid.createPayPalOrder') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                plan_id: "{{ $plan->id }}",
                                couponID: couponValue
                            }),
                        })
                        .then((response) => response.json())
                        .then((order) => order.id);
                },
                onApprove(data) {
                    return fetch("{{ route('dashboard.user.payment.subscription.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                paypalSubscriptionID: "lifetime",
                                billingPlanId: "lifetime",
                                planID: "{{ $plan->id }}",
                                productId: "{{ $productId }}",
                                gateway: "paypal",
                                couponID: couponValue,
                            })
                        })
                        .then((response) => response.json())
                        .then((response) => {
                            if (response.result == "OK") {
                                const element = document.getElementById('paypal-button-container');
                                element.innerHTML = '<h3>{{ __('Thank you for your payment!') }}</h3>';
                                setTimeout(function() {
                                    location.href = '/dashboard/user/payment/succesful';
                                }, 1000);
                            } else {
                                console.log(response.result);
                            }
                        });
                }
            }).render('#paypal-button-container');
        } else {
            paypal.Buttons({
                style: {
                    color: 'blue',
                    shape: 'pill',
                    label: 'pay',
                },
                createSubscription: function(data, actions) {
                    return actions.subscription.create({
                        'plan_id': '{{ $billingPlanId }}'
                    });
                },
                onApprove: function(data, actions) {
                    return fetch("{{ route('dashboard.user.payment.subscription.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                paypalSubscriptionID: data.subscriptionID,
                                billingPlanId: "{{ $billingPlanId }}",
                                planID: "{{ $plan->id }}",
                                productId: "{{ $productId }}",
                                gateway: "paypal",
                                couponID: couponValue,
                            })
                        })
                        .then((response) => response.json())
                        .then((response) => {
                            if (response.result == "OK") {
                                const element = document.getElementById('paypal-button-container');
                                element.innerHTML = '<h3>{{ __('Thank you for your payment!') }}</h3>';
                                setTimeout(function() {
                                    location.href = '/dashboard/user/payment/succesful';
                                }, 1000);
                            } else {
                                console.log(response.result);
                            }
                        });
                }
            }).render('#paypal-button-container');
        }

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
