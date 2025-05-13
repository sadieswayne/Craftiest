@extends('panel.layout.app')
@section('title', __('Subscription Payment'))

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
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-8 col-lg-8">
                    <form id="checkoutForm"  action="{{ route('dashboard.user.payment.subscription.checkout' , ['gateway' => 'paddle']) }}" method="post">
                        @csrf
                        <div class="section">
                            <x-button class="btn btn-info w-full" type="button" onclick="pay()">
                                <span id="button-text">{{ __('Pay') }} {!! displayCurr(currency()->symbol, $plan->price) !!} {{ __('with') }} </span>
                            </x-button>
                        </div>
                        <input type="hidden" name="orderID" value="{{ $orderId }}">
                        <input type="hidden" name="planID" value="{{ $plan->id }}">
                        <input type="hidden" name="gateway" value="paddle">
                        <input type="hidden" name="checkoutData" id="checkoutData" value="">
                        <div class="row">

                        </div>
                    </form>

                    <p></p>
                    <p>{{ __('By purchasing you confirm our') }} <a href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                </div>
                <div class="col-sm-4 col-lg-4">
                    @include('panel.user.finance.partials.plan_card')
                </div>

                {{--                <div class="col-sm-4 col-lg-4">--}}
                {{--                    <div--}}
                {{--                        class="card card-md w-full bg-[#f3f5f8] text-center border-0 text-heading group-[.theme-dark]/body:!bg-[rgba(255,255,255,0.02)]">--}}
                {{--                        @if ($plan->is_featured == 1)--}}
                {{--                            <div class="ribbon ribbon-top ribbon-bookmark bg-green">--}}
                {{--                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-filled" width="24"--}}
                {{--                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"--}}
                {{--                                    fill="none" stroke-linecap="round" stroke-linejoin="round">--}}
                {{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />--}}
                {{--                                    <path--}}
                {{--                                        d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />--}}
                {{--                                </svg>--}}
                {{--                            </div>--}}
                {{--                        @endif--}}
                {{--                        <div class="card-body flex flex-col !p-[45px_50px_50px] text-center">--}}
                {{--                            <div  class="text-center rounded-[8px] font-medium text-[15px] leading-none text-[#2D3136]"> {{ __($plan->name) }}</div>--}}
                {{--                            <br>--}}
                {{--                            <div class="text-heading flex items-end justify-center mt-0 mb-[15px] w-full text-[50px] leading-none">--}}
                {{--                                {!! displayCurrPlan(currency()->symbol, $plan->price) !!}--}}
                {{--                                <small class="inline-flex mb-[0.3em] font-normal text-[0.35em]">/ {{__(formatCamelCase($plan->frequency))}}</small>--}}
                {{--                            </div>--}}
                {{--                            <hr>--}}
                {{--                <x-plan-details-card--}}
                {{--                    :plan="$plan"--}}
                {{--                    :period="$plan->frequency"--}}
                {{--                />     --}}
                {{--                            <div class="text-center mt-auto">--}}
                {{--                                <a class="btn rounded-md p-[1.15em_2.1em] w-full text-[15px] group-[.theme-dark]/body:!bg-[rgba(255,255,255,1)] group-[.theme-dark]/body:!text-[rgba(0,0,0,0.9)]"--}}
                {{--                                    href="{{  (route('dashboard.user.payment.subscription')) }}">{{ __('Change Plan') }}</a>--}}
                {{--                            </div>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
    <script type="text/javascript">

        function pay() {
            Paddle.Environment.set('{{ $gateway->mode == 'sandbox' ? 'sandbox' : 'production' }}');

            Paddle.Initialize({
                token: '{{ $token }}',
                pwCustomer: {
                    id: '{{ $customerId }}'
                },
                eventCallback: function(data) {
                    console.log(data);
                    if (data.name == "checkout.completed")
                    {
                        let checkoutData = JSON.stringify(data);

                        document.getElementById('checkoutData').value = checkoutData;

                        document.querySelector('#checkoutForm').submit();
                    }
                }
            });

            var itemsList = [
                {
                    priceId: '{{ $gateProduct }}',
                    quantity: 1
                }
            ];

            Paddle.Checkout.open({
                settings: {
                    displayMode: "overlay",
                    // theme: "light",
                    locale: "{{ app()->getLocale() }}"
                },
                items: itemsList,
                customer: {
                    email: '{{ auth()->user()->email }}'
                },
            });
        }


        pay();
    </script>
@endpush
