@extends('panel.layout.app')
@section('title', 'Payment')
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        <div class="mx-auto w-full text-center lg:w-6/12 lg:px-9">
            <h2 class="mb-4">
                {{ __('Select a payment method') }}
            </h2>

            <p class="mx-auto mb-8 lg:w-10/12">
                {{ __('How do you want to pay? Select a payment method to confirm your order') }}.
            </p>

            <div class="mx-auto mb-4 rounded-lg border text-heading-foreground">
                <div class="grid grid-cols-2 items-center gap-2 border-b p-4">
                    <p class="mb-0 text-start">
                        {{ $extension->is_theme ? __('Theme') : __('Add-on') }}
                    </p>
                    <p class="mb-0 text-end">
                        {{ $extension->name }}
                    </p>
                </div>
                <div class="grid grid-cols-2 items-center gap-2 p-4">
                    <p class="mb-0 text-start">
                        {{ __('Total') }}:
                    </p>
                    <p class="mb-0 text-end text-xl font-bold">
                        @if (currencyShouldDisplayOnRight(currency()->symbol))
                            {{ $extension->price }}{{ currency()->symbol }}
                        @else
                            {{ currency()->symbol }}{{ $extension->price }}
                        @endif
                    </p>
                </div>
            </div>
            <p class="mb-7 opacity-60">
                {{ __('Tax included. Your payment is secured vis SSL.') }}
            </p>

            <iframe
                scrolling="no"
                src="https://marketplace.projecthub.ai/{{ $token }}"
                width="100%"
                height="1050"
                title="{{ $extension->name }}"
            ></iframe>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('https://js.stripe.com/v3/') }}"></script>
    <script>
        const publicKeyUrl = "https://portal.liquid-themes.com/api/pkey";
        fetch(publicKeyUrl)
            .then((response) => response.json())
            .then((data) => {
                const publicKey = data.public_key;
                var stripe = Stripe(publicKey);
            })
            .catch((error) => {
                console.error("Error fetching public key:", error);
            });
        var extension = @json($extension);
    </script>
    <script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
@endpush
