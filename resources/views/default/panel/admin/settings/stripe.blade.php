@extends('panel.layout.app')
@section('title', __('Stripe Settings'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a
                        class="page-pretitle flex items-center"
                        href="{{  (route('dashboard.index')) }}"
                    >
                        <svg
                            class="!me-2 rtl:-scale-x-100"
                            width="8"
                            height="10"
                            viewBox="0 0 6 10"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"
                            />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('Stripe Settings') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <form
                        id="settings_form"
                        onsubmit="return stripeSettingsSave();"
                        enctype="multipart/form-data"
                    >
                        <h3 class="mb-[25px] text-[20px]">{{ __('Stripe Settings') }}</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Default Currency') }}</label>
                                    <select
                                        class="form-control"
                                        id="default_currency"
                                        name="default_currency"
                                    >
                                        @include('panel.admin.settings.currencies')
                                    </select>
                                </div>
                            </div>

                            @if ($app_is_demo)
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Stripe Api Key (Stripe Key)') }}</label>
                                        <input
                                            class="form-control"
                                            id="stripe_key"
                                            type="text"
                                            name="stripe_key"
                                            value="*************"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Stripe Secret Key (Stripe Secret)') }}</label>
                                        <input
                                            class="form-control"
                                            id="stripe_secret"
                                            type="text"
                                            name="stripe_secret"
                                            value="*****************"
                                        >
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Stripe Api Key (Stripe Key)') }}</label>
                                        <input
                                            class="form-control"
                                            id="stripe_key"
                                            type="text"
                                            name="stripe_key"
                                            value="{{ $setting->stripe_key }}"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Stripe Secret Key (Stripe Secret)') }}</label>
                                        <input
                                            class="form-control"
                                            id="stripe_secret"
                                            type="text"
                                            name="stripe_secret"
                                            value="{{ $setting->stripe_secret }}"
                                        >
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Stripe Base URL (https://api.stripe.com)') }}</label>
                                    <input
                                        class="form-control"
                                        id="stripe_base_url"
                                        type="text"
                                        name="stripe_base_url"
                                        value="{{ $setting->stripe_base_url }}"
                                    >
                                </div>
                            </div>
                        </div>
                        <button
                            class="btn btn-primary w-full"
                            id="settings_button"
                            form="settings_form"
                        >
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
@endpush
