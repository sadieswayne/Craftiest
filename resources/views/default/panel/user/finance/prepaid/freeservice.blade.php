@extends('panel.layout.app')
@section('title', __('Free Token Pack'))
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

        .hidden {
            display: none;
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
                        action="{{ route('dashboard.user.payment.prepaid.checkout', ['gateway' => 'freeservice']) }}"
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
                            type="hidden"
                            name="gateway"
                            value="freeservice"
                        >
                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <span class="text-heading ps-1 leading-none">{{ __('Order ID') }}: </span>
                                <small class='inline-flex font-normal'>{{ $order_id }}</small>
                            </div>
                            <div class="col-md-12 col-xl-12 mt-3">
                                <x-button
                                    class="w-full"
                                    data-bs-toggle="{{ $app_is_not_demo && !$existingPrepaidOrder ? 'modal' : '' }}"
                                    data-bs-target="{{ $app_is_not_demo && !$existingPrepaidOrder ? '#confirmationModal' : '' }}"
                                    variant="secondary"
                                    onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                                    type="button"
                                >
                                    <div
                                        class="spinner hidden"
                                        id="spinner"
                                    ></div>
                                    <span id="button-text">
                                        @if ($existingPrepaidOrder)
                                            {{ __('This pack alredy purchased') }}
                                        @else
                                            {{ __('Buy for free now') }}
                                        @endif
                                    </span>
                                </x-button>

                            </div>
                        </div>
                    </form>

                    <p></p>
                    <p>{{ __('By purchasing you confirm our') }} <a href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                </div>
                <div class="col-sm-4 col-lg-4">
                    @php
                        $newDiscountedPrice = $plan->price;
                        $taxRate = 0;
                        $taxValue = 0;
                    @endphp
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
                    {{ __('Upon confirmation, your application will be promptly submitted.') }}
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
        function submitForm() {
            document.getElementById('bank-form').submit();
        }
    </script>
@endpush
