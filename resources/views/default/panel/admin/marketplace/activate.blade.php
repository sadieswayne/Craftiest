@extends('panel.layout.app')
@section('title', 'Marketplace Payment')
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        <div class="mx-auto w-full text-center lg:w-6/12 lg:px-9">
            <h2 class="mb-4">
                {{ $item['licensed'] ? __('Payment Success') : __('Payment Failed') }}
            </h2>

            <div class="mx-auto mb-4 rounded-lg border text-heading-foreground">
                <div class="grid grid-cols-2 items-center gap-2 border-b p-4">
                    <p class="mb-0 text-start">
                        {{ __('Add-on') }}
                    </p>
                    <p class="mb-0 text-end">
                        {{ $item['name'] }}
                    </p>
                </div>
                <div class="grid grid-cols-2 items-center gap-2 p-4">
                    <p class="mb-0 text-start">
                        {{ __('Total') }}:
                    </p>
                    <p class="mb-0 text-end text-xl font-bold">
                        {{ $item['price'] . '$'}}
                    </p>
                </div>
            </div>
            @if ($item['licensed'])
                <x-alert variant="info">
                    @lang('Your payment is success, you can install your extension.')
                </x-alert>

                <x-button
                    class="mt-4 w-full"
                    size="lg"
                    href="{{ route('dashboard.admin.marketplace.liextension') }}"
                >
                    {{ __('Install Now') }}
                </x-button>
            @else
                <x-alert variant="danger">
                    @lang('Your payment is unsuccessful')
                </x-alert>

                @if ($app_is_demo)
                    <x-button
                        class="w-full"
                        size="lg"
                        href="#"
                        onclick="return toastr.info('This feature is disabled in Demo version.')"
                        disabled
                    >
                        {{ __('Buy Now') }}
                    </x-button>
                @else
                    <x-button
                        class="mt-4 w-full"
                        :disabled="false"
                        size="lg"
                        href="{{ route('dashboard.admin.marketplace.buyextesion', ['slug' => $item['slug']]) }}"
                    >
                        {{ __('Buy Now') }}
                    </x-button>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('https://js.stripe.com/v3/') }}"></script>

    <script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
@endpush
