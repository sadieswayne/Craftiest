@php
    $base_class = 'lqd-navbar-dropdown-item';
@endphp

<x-navbar.item {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
    {{ $slot }}
</x-navbar.item>
