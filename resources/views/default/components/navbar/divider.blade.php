@php
    $base_class = 'lqd-navbar-divider my-3 border-navbar-divider';
@endphp

<hr {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
