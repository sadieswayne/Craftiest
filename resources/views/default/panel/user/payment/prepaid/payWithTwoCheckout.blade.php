@extends('panel.layout.app')
@section('title', __('Token Packs'))
@section('titlebar_actions', '')

@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            @if ($exception != null)
                <h2 class="text-danger">{{ $exception }}</h2>
            @else
                <div class="row row-cards">

                    <div class="col-sm-8 col-lg-8 justify-content-md-center">
                        {{-- @include('panel.user.finance.coupon.index') --}}

                        <div class="card">
                            <div class="card-body">
                                <form
                                    class="text-center"
                                    id="payment-form"
                                    type="post"
                                    action="{{ route('dashboard.user.payment.twocheckoutPrepaidPay') }}"
                                >
                                    @csrf
                                    <div class="form-group mb-2 me-3 text-start">
                                        <label
                                            class="label control-label"
                                            for="name"
                                        >{{ __('Name') }}</label>
                                        <input
                                            class="field form-control"
                                            id="name"
                                            type="text"
                                        >
                                    </div>

                                    <div id="card-element">
                                        <!-- A TCO IFRAME will be inserted here. -->
                                    </div>
                                    {{-- <input id="token" name="token" type="hidden" value=""> --}}
                                    <button
                                        class="btn btn-primary submit mt-4"
                                        type="submit"
                                    >{{ __('Pay with 2checkout') }}</button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="row d-flex justify-content-center text-center">
                                <div
                                    class=""
                                    style="width: 360px;"
                                >
                                    <div id="twocheckout-button-container"></div>
                                </div>
                                <p class="mt-3">{{ __('By purchasing you confirm our') }} <a href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-lg-4">
                        <div class="card card-md w-full border-0 bg-[#f3f5f8] text-center text-heading-foreground group-[.theme-dark]/body:!bg-[rgba(255,255,255,0.02)]">
                            @if ($plan->is_featured == 1)
                                <div class="ribbon ribbon-top ribbon-bookmark bg-green">
                                    <svg
                                        class="icon icon-filled"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            stroke="none"
                                            d="M0 0h24v24H0z"
                                            fill="none"
                                        />
                                        <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="card-body flex flex-col !p-[45px_50px_50px] text-center">
                                <div class="mb-[15px] mt-0 flex w-full items-end justify-center text-[50px] leading-none text-heading-foreground">
                                    @if (currencyShouldDisplayOnRight(currency()->symbol))

                                        @if ($plan->price !== ($newDiscountedPrice ?? $plan->price))
                                            <small class="mb-[0.3em] inline-flex text-[0.35em] font-normal"><span
                                                    style="text-decoration: line-through;">{{ $plan->price }}</span>{{ currency()->symbol }}</small>
                                            &nbsp;
                                            {{ $newDiscountedPrice }}<small class="mb-[0.3em] inline-flex text-[0.35em] font-normal">{{ currency()->symbol }}</small>
                                        @else
                                            {{ $plan->price }}
                                            <small class="mb-[0.3em] inline-flex text-[0.35em] font-normal">{{ currency()->symbol }}</small>
                                        @endif
                                    @else
                                        @if ($plan->price !== ($newDiscountedPrice ?? $plan->price))
                                            <small class="mb-[0.3em] inline-flex text-[0.35em] font-normal">{{ currency()->symbol }}<span
                                                    style="text-decoration: line-through;">{{ $plan->price }}</span></small>
                                            &nbsp;
                                            <small class="mb-[0.3em] inline-flex text-[0.35em] font-normal">{{ currency()->symbol }}</small>{{ $newDiscountedPrice }}
                                        @else
                                            <small class="mb-[0.3em] inline-flex text-[0.35em] font-normal">{{ currency()->symbol }}</small>{{ $plan->price }}
                                        @endif

                                    @endif
                                    <small class="mb-[0.3em] inline-flex text-[0.35em] font-normal">/
                                        {{ __('One time') }}</small>
                                </div>
                                <div class="mx-auto inline-flex rounded-full bg-white p-[0.85em_1.2em] text-[15px] font-medium leading-none text-[#2D3136]">
                                    {{ __($plan->name) }}</div>

                                <x-plan-details-card
                                    :plan="$plan"
                                    :period="$plan->frequency"
                                />
                                <div class="mt-auto text-center">
                                    <a
                                        class="btn w-full rounded-md p-[1.15em_2.1em] text-[15px] group-[.theme-dark]/body:!bg-[rgba(255,255,255,1)] group-[.theme-dark]/body:!text-[rgba(0,0,0,0.9)]"
                                        href="{{  (route('dashboard.user.payment.subscription')) }}"
                                    >{{ __('Change Plan') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js') }}"></script>
    <script
        type="text/javascript"
        src="https://2pay-js.2checkout.com/v1/2pay.js"
    ></script>
    <script>
        window.addEventListener('load', function() {
            // Initialize the 2Pay.js client.
            let jsPaymentClient = new TwoPayClient('{!! $merchant_code !!}');

            // Create the component that will hold the card fields.
            let component = jsPaymentClient.components.create('card');

            // Mount the card fields component in the desired HTML tag. This is where the iframe will be located.
            component.mount('#card-element');

            var myForm = document.getElementById('payment-form');

            // Handle form submission.
            document.getElementById('payment-form').addEventListener('submit', (event) => {
                event.preventDefault();

                const submitButton = document.querySelector('.submit');
                submitButton.disabled = true;
                // Extract the Name field value
                const billingDetails = {
                    name: document.querySelector('#name').value
                };

                // Call the generate method using the component as the first parameter
                // and the billing details as the second one
                jsPaymentClient.tokens.generate(component, billingDetails).then((response) => {
                    // myForm.token.value = response.token;
                    var formData = new FormData();
                    formData.append('token', response.token);
                    formData.append('plan', '{!! $planId !!}');
                    document.querySelector('.card-body button').disabled = true;
                    $.ajax({
                        type: "post",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        url: "/dashboard/user/payment/twocheckout/prepaidPay",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            submitButton.disabled = false;
                            document.querySelector('.card-body button').disabled =
                                false;
                            if (data['status'] == 'error')
                                toastr.error(data['message']);
                            else {
                                const element = document.getElementById(
                                    'twocheckout-button-container');
                                element.innerHTML =
                                    '<h3>{{ __('Thank you for your payment!') }}</h3>';
                                setTimeout(function() {
                                    location.href = '/dashboard';
                                }, 1000);
                            }
                        },
                        error: function(data) {
                            submitButton.disabled = false;
                            var err = data.responseJSON.errors;
                            toastr.error(err);
                        }
                    });
                }).catch((error) => {
                    submitButton.disabled = false;
                    console.error(error);
                    toastr.error(error);
                });
            });
        });
    </script>
@endpush
