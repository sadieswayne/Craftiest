@extends('panel.layout.app')
@section('title', __('Token Packs'))
@section('titlebar_actions', '')

@section('content')

    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-8 col-lg-8">
                    <div id="payment-form"></div>
                    <p>{{ __('By purchasing you confirm our') }} <a
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
    <script src="{{ custom_theme_url('https://yookassa.ru/checkout-widget/v1/checkout-widget.js') }}"></script>
    <script>
        let couponValue = null;
        let url = `{{ route('dashboard.user.payment.prepaid.checkout', ['gateway' => ':gateway']) }}`;
        url = url.replace(':gateway', 'yokassa');
        var currentURL = window.location.href;
        if (currentURL.includes('coupon=')) {
            couponValue = getParameterByName('coupon', currentURL);
        }

        const checkout = new window.YooMoneyCheckoutWidget({
            confirmation_token: '{{ $confirmation_token }}', //Token that must be obtained from YooMoney before the payment process
            error_callback: function(error) {
                console.log(error)
            }
        });
        checkout.on('success', () => {
            var formData = new FormData();
            formData.append('paymentID', '{{ $payment_id }}');
            formData.append('planID', '{{ $plan->id }}');
            formData.append('couponID', couponValue);
            formData.append('gateway', 'yokassa');
            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    toastr.success("{{ __('Thanks for your purchase...') }}");
                    setTimeout(function() {
                        location.href = '/dashboard/user';
                    }, 1000);
                },
                error: function(data) {
                    var err = data.responseJSON.errors;
                    toastr.error(err);
                }
            });

            checkout.destory();
        })
        checkout.on('fail', () => {
            toastr.error("{{ __('There is a mistake for you purchaes. Please try again') }}");
            checkout.destroy();
        });
        //Display of payment form in the container
        checkout.render('payment-form');

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
