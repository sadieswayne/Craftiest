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

                        <div class="justify-content-center flex flex-col text-center">

                            <div class="flex min-h-[400px] w-full flex-col">
                                @if ($checkoutform->getStatus() == 'success')
                                    {!! $checkoutform->getCheckoutFormContent() !!}
                                    <div class="responsive" id="iyzipay-checkout-form"></div>
                                @else
                                    <div>
                                        <h2 class="text-danger">{{ __('Error Code') }}: {{ $checkoutform->getErrorCode() }}
                                        </h2>
                                        <h2 class="text-danger">{{ $checkoutform->getErrorMessage() }}</h2>
                                        <h4>{{ __('If error persist, please contact with us.') }}</h4>
                                    </div>
                                @endif

                            </div>

                            <p></p>
                            <p>{{ __('Note that, we do not collect or store any personal data. All information above are sent to iyzico directly.') }}
                            </p>

                            <p></p>
                            <p class="mt-3">{{ __('By purchasing you confirm our') }} <a
                                    href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>

                        </div>

                    </div>
                    <div class="col-sm-4 col-lg-4">
                        @include('panel.user.finance.partials.plan_card')
                    </div>

                </div>
            @endif
        </div>
    </div>
@endsection
