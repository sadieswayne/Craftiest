@extends('panel.layout.app')
@section('title', 'Cart')
@section('titlebar_actions', '')
@section('titlebar_pretitle', '')
@section('content')
    <div class="py-10">
        <div class="mx-auto w-full text-center lg:w-6/12 lg:px-9">

            @if(isset($items['data']) && $items['data'] && $app_is_not_demo)
                <h2 class="mb-4">
                    {{ __('Cart Items') }}
                </h2>

                <p class="mx-auto mb-8 lg:w-10/12">
                    {{ __('To complete the order, make payment') }}.
                </p>

                <div class="mx-auto mb-4 rounded-lg border text-heading-foreground">

                    @foreach($items['data'] as $item)
                        <div class="flex justify-between items-center gap-2 border-b p-4" id="ext-{{$item['extension_id']}}">
                            <p class="mb-0 text-start">
                                {{ $item['extension']['name'] }}

                            </p>
                            <div
                                    class="flex items-center"
                                    data-toogle="cart"
                                    data-url="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['extension_id']) }}"
                                    data-delete-item="ext-{{$item['extension_id']}}"
                            >
                                <span class="me-1">{{ $item['extension']['price'] . ' USD' }}</span>
                                <a href="#">
                                    <x-tabler-trash id="{{ $item['id'].'-icon' }}" class="w-9 h-9 text-gray-500 border rounded p-1"/>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="mb-7 opacity-60">
                    {{ __('Tax included. Your payment is secured vis SSL.') }}
                </p>

                <div id="checkout">
                    <x-button
                            target="_blank"
                            class="w-full"
                            size="lg"
                            href="{{ $items['paymentJson'] }}"
                    >
                        {{ __('Pay Now') }}
                    </x-button>
                </div>
            @else
                @if($app_is_demo)
                    <p class="mb-7 opacity-60">
                        {{ __('You can not add any extensions in demo mode.') }}
                    </p>
                @else
                    <p class="mb-7 opacity-60">
                        {{ __('You did not add any extensions.') }}
                    </p>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('[data-toogle="cart"]').on('click', function () {
            var url = $(this).data('url');

            var deleteItem = $(this).data('delete-item');

            $.get(url, function (data) {


                var icon = $('#' + data.iconId);

                if (icon.hasClass('text-gray-500')) {
                    icon.removeClass('text-gray-500');
                    icon.addClass('text-green-500');
                } else {
                    icon.removeClass('text-green-500');
                    icon.addClass('text-gray-500');
                }

                $('#itemCount').html(data.itemCount);

                toastr.success(data.message);

                setTimeout(function () {
                    location.reload();
                }, 500);
            });
        });
    </script>
@endpush

