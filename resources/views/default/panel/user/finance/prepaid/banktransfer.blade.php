@extends('panel.layout.app')
@section('title', __('Token Packs'))
@section('titlebar_actions', '')

@section('additional_css')
    <style>
        #bank-form {
            width: 100%;
            align-self: center;
            box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.055), 0px 2px 5px 0px rgba(50, 50, 93, 0.068), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.021);
            border-radius: 7px;
            padding: 40px;
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
                        id="bank-form"
                        action="{{ route('dashboard.user.payment.prepaid.checkout', ['gateway' => 'banktransfer']) }}"
                        method="post"
                    >
                        @csrf
                        <input
                            type="hidden"
                            name="planID"
                            value="{{ $plan->id }}"
                        >
                        <input
                            type="hidden"
                            name="orderID"
                            value="{{ $order_id }}"
                        >
                        <input
                            id="coupon"
                            type="hidden"
                            name="couponID"
                        >
                        <input
                            type="hidden"
                            name="gateway"
                            value="banktransfer"
                        >
                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <span class="text-heading ps-1 leading-none">{{ __('Order ID') }}: </span>
                                <small class='inline-flex font-normal'>{{ $order_id }}</small>
                            </div>
                            <div class="col-md-12 col-xl-12 mt-2">
                                <div class="form-control h-auto whitespace-pre-wrap border p-3">{!! $gateway->bank_account_other ??
                                    'To facilitate the processing of your transaction, kindly remit your payment directly to our designated bank account. Please ensure to include your Order ID Number as the payment reference to expedite the allocation of funds to your account. Note that services will not be credited until the payment has successfully been received in our bank account. We appreciate your cooperation and thank you for choosing our services.' !!}
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-12 mt-2">
                                <div class="form-control h-auto whitespace-pre-wrap border p-3">{!! $gateway->bank_account_details ?? "Bank Name:\nAccount Name:\nIBAN:\nBIC/Swift:\nRouting Number:\n" !!}
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-12 mt-3">
                                <x-button
                                    class="w-full"
                                    data-bs-toggle="{{ $app_is_demo ? '' : 'modal' }}"
                                    data-bs-target="{{ $app_is_demo ? '' : '#confirmationModal' }}"
                                    variant="secondary"
                                    type="button"
                                    onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                                >
                                    <div
                                        class="spinner hidden"
                                        id="spinner"
                                    ></div>
                                    <span
                                        class="inline-flex items-center gap-2"
                                        id="button-text"
                                    >
                                        <span>
                                            {{ __('Pay') }}
                                            {!! displayCurr(currency()->symbol, $plan->price, $taxValue, $newDiscountedPrice) !!}
                                            {{ __('with') }}
                                        </span>
                                        <img
                                            class="h-auto w-24"
                                            src="{{ custom_theme_url('/assets/img/payments/banktransfer.png') }}"
                                            height="40"
                                            alt="{{ __('Bank transfer') }}"
                                        >
                                    </span>
                                </x-button>

                            </div>
                        </div>
                    </form>

                    <p></p>
                    <p>{{ __('By purchasing you confirm our') }} <a href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                </div>
                <div class="col-sm-4 col-lg-4">
                    @include('panel.user.finance.partials.plan_card')
                </div>
            </div>
        </div>
    </div>
    <!-- Confirmation Modal -->
    <div
        class="modal fade"
        id="confirmationModal"
        tabindex="-1"
        aria-labelledby="confirmationModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5
                        class="modal-title"
                        id="confirmationModalLabel"
                    >{{ __('Confirmation') }}</h5>
                    <button
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <span class="text-heading leading-none">{{ __('Order ID') }}: </span>
                        <small class='inline-flex font-normal'>{{ $order_id }}</small>
                    </div>
                    {{ __('Upon confirmation, your application will be promptly submitted. Following successful payment verification, your plan will be activated. For seamless transactions, please utilize the order ID number as a reference when making payments in the upcoming months.') }}
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        type="button"
                    >{{ __('Cancel') }}</button>
                    <button
                        class="btn btn-primary"
                        type="submit"
                        onclick="submitForm()"
                    >{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            var currentURL = window.location.href;
            if (currentURL.includes('coupon=')) {
                var couponValue = getParameterByName('coupon', currentURL);
                document.getElementById('coupon').value = couponValue;
            }
        });

        function submitForm() {
            document.getElementById('bank-form').submit();
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
