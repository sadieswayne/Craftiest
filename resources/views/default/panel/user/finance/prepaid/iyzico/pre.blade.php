@extends('panel.layout.app')
@section('title', __('Token Packs'))
@section('titlebar_actions', '')

@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-8 col-lg-8">
                    <div class="justify-content-center flex flex-col text-center">
                        @include('panel.user.finance.coupon.index')
                        <p class="text-start">
                            {{ __('Please enter your billing details') }}
                        </p>
                        <form class=""
                            action="{{ route('dashboard.user.payment.prepaid.checkout', ['gateway' => 'iyzico']) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="planID" value="{{ $plan->id }}">
                            <input type="hidden" name="ip" value="{{ $_SERVER['REMOTE_ADDR'] }}">
                            <input id="coupon" type="hidden" name="couponID">
                            <input type="hidden" name="gateway" value="iyzico">
                            <div class="flex min-h-[400px] w-full flex-col gap-3 sm:flex-row">
                                <div class="w-full sm:w-1/2">
                                    <div class="flex max-w-sm flex-col gap-3">
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Name') }}</label>
                                            <input class="form-control" id="name" type="text" name="name"
                                                required value="{{ old('name') ?? auth()->user()->name }}">
                                        </div>
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Surname') }}</label>
                                            <input class="form-control" id="surname" type="text" name="surname"
                                                required value="{{ old('surname') ?? auth()->user()->surname }}">
                                        </div>
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Identity Number') }}</label>
                                            <input class="form-control" id="identityNumber" type="number" min="100000"
                                                name="identityNumber" value="{{ old('identityNumber') }}" required>
                                        </div>
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Email') }}</label>
                                            <input class="form-control" id="email" type="email" name="email"
                                                value="{{ old('email') ?? auth()->user()->email }}" required>
                                        </div>
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Mobile') }}</label>
                                            <input class="form-control" id="gsmNumber" type="tel" name="gsmNumber"
                                                value="{{ old('gsmNumber') ?? auth()->user()->phone }}" required>
                                        </div>
                                        <script>
                                            const gsmNumberInput = document.getElementById("gsmNumber");

                                            gsmNumberInput.addEventListener("input", function() {
                                                const inputValue = gsmNumberInput.value;

                                                // Check if the input starts with "+90"
                                                if (!inputValue.startsWith("+90")) {
                                                    // If not, clear the input field
                                                    gsmNumberInput.value = "+90";
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="w-full sm:w-1/2">
                                    <div class="flex max-w-sm flex-col gap-3">
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Country') }}</label>
                                            <input class="form-control" id="country" type="text" name="country"
                                                value="{{ old('country') ?? auth()->user()->country }}" required>
                                        </div>
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('City') }}</label>
                                            <input class="form-control" id="city" type="text" name="city"
                                                value="{{ old('city') }}" required>
                                        </div>
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Address') }}</label>
                                            <input class="form-control" id="registrationAddress" type="text"
                                                value="{{ old('registrationAddress') ?? auth()->user()->address }}"
                                                name="registrationAddress" required>
                                        </div>
                                        <div class="flex flex-col justify-start text-start">
                                            <label class="form-label">{{ __('Zip Code') }}</label>
                                            <input class="form-control" id="zipCode" type="text" name="zipCode"
                                                value="{{ old('zipCode') }}" required>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <p>
                                <button class="btn btn-primary mt-4 w-full"
                                    type="submit">{{ __('Continue to Payment') }}</button>
                            </p>
                        </form>
                        <br>
                        <p>{{ __('Note that, we do not collect or store any personal data. All information above are sent to iyzico directly.') }}
                        </p>
                        <p class="mt-3">{{ __('By purchasing you confirm our') }} <a
                                href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                    </div>
                </div>
                <div class="col-sm-4 col-lg-4">
                    @include('panel.user.finance.partials.plan_card')
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
