@extends('panel.layout.app')
@section('title', __(' Token Packs '))

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
                    <form id="checkoutForm"  action="{{ route('dashboard.user.payment.prepaid.checkout' , ['gateway' => 'paddle']) }}" method="post">
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
                }
            });
        }
        pay();
    </script>
@endpush
