@php
    $is_modal = $triggerType === 'modal' && !empty($modal);
@endphp

@if ($is_modal)
    <x-modal type="page">
        <x-slot:trigger
            custom
        >
            @include('components.navbar.partials.link-markup')
        </x-slot:trigger>
        <x-slot:modal>
            {{ $modal }}
        </x-slot:modal>
    </x-modal>
@else
    @include('components.navbar.partials.link-markup')
@endif
