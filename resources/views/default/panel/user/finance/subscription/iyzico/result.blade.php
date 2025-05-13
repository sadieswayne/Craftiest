@extends('panel.layout.app')
@section('title', __('Payment'))
@section('titlebar_actions', '')
@section('additional_css')
    <style>
        .checkmark {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #228F75;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #228F75;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
            position: relative;
            top: 5px;
            right: 5px;
            margin: 0 auto;
        }

        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #228F75;
            fill: transparent;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes scale {

            0%,
            100% {
                transform: none;
            }

            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
        }

        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 4px #228F75;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="success-animation mb-3">
                <svg
                    class="checkmark"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 52 52"
                >
                    <circle
                        class="checkmark__circle"
                        cx="26"
                        cy="26"
                        r="25"
                        fill="none"
                    />
                    <path
                        class="checkmark__check"
                        fill="none"
                        d="M14.1 27.2l7.1 7.2 16.7-16.8"
                    />
                </svg>
            </div>
            <div class="text-center">
                <h1 class="text-heading text-center">{{ __('Payment Succesful') }}</h1>
                <p class="text-heading my-3 text-center">
                    {{ __('Thanks for your purchase! Now, you can explore our AI tools and start generating content in seconds.') }}
                </p>

            </div>
            <div class="justify-content-center mt-2 flex flex-col text-center">
                <div class="justify-content-center flex min-h-[400px] w-full flex-col text-center">
                    @if ($checkoutresult->getStatus() == 'success')
                        @if ($checkoutresult->getSubscriptionStatus() == 'ACTIVE' || $checkoutresult->getSubscriptionStatus() == 'active')
                            <div>
                                <h2 class="text-success">{{ __('Subscription is ACTIVE') }}</h2>
                                <h2 class="text-success">{{ __('Reference ID') }}:</h2>
                                <h2 class="text-success">{{ $checkoutresult->getReferenceCode() }}</h2>
                                <h6 class="text-success">{{ __('Please save reference ID') }}</h6>
                            </div>
                        @else
                            <div>
                                <h2 class="text-danger">{{ __('Subscription status') }} :
                                    {{ $checkoutresult->getSubscriptionStatus() }}</h2>
                            </div>
                        @endif
                    @else
                        <div>
                            <h2 class="text-danger">{{ __('Error Code') }}: {{ $checkoutresult->getErrorCode() }}</h2>
                            <h2 class="text-danger">{{ $checkoutresult->getErrorMessage() }}</h2>
                        </div>
                    @endif
                </div>
                <p>
                    <a
                        class="btn btn-primary"
                        href="{{  (route('dashboard.index')) }}"
                    >
                        <span>{{ __('Generate New Content') }}</span>
                        <svg
                            class="ms-1"
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
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
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                    </a>
                </p>
            </div>
        </div>
    </div>

@endsection
