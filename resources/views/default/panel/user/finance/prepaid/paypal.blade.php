@extends('panel.layout.app')
@section('title', __('Token Packs'))
@section('titlebar_actions', '')
@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-8 col-lg-8">
                    @include('panel.user.finance.coupon.index')
                    <div id="paypal-button-container"></div>
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
    @endphp
    <script src="{{ custom_theme_url('https://www.paypal.com/sdk/js?client-id=' . $clientId . '&currency=' . $currency) }}">
    </script>
    <script>
        let couponValue = null;
        var currentURL = window.location.href;
        if (currentURL.includes('coupon=')) {
            couponValue = getParameterByName('coupon', currentURL);
        }

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
                return fetch("{{ route('dashboard.user.payment.prepaid.checkout') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            orderID: data.orderID,
                            planID: "{{ $plan->id }}",
                            productId: "{{ $productId }}",
                            gateway: "paypal",
                            couponID: couponValue,
                        })
                    })
                    .then((response) => response.json())
                    .then((orderData) => {
                        const element = document.getElementById('paypal-button-container');
                        element.innerHTML = '<h3>{{ __('Thank you for your payment!') }}</h3>';
                        setTimeout(function() {
                            location.href = '/dashboard/user/payment/succesful';
                        }, 1000);
                    });
            }

        }).render('#paypal-button-container');


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
