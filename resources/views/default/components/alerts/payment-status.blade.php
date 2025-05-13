@props([
    'paymentStatus' => null,
])

@if($paymentStatus)
    <x-alert {{ $attributes->class(['mt-1 w-full py-2.5'])->merge(['variant' => $paymentStatus === 'paid' ? 'success' : 'danger']) }}>
        <p>{{ \App\Helpers\Classes\Helper::marketplacePaymentMessage($paymentStatus) }}</p>
    </x-alert>
@endif