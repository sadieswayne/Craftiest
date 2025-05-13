@php
    $base_class = 'lqd-navbar-dropdown ps-7';

    if (empty($open)) {
        $base_class .= ' hidden';
    }
@endphp

<ul
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    :class="{ 'hidden': !dropdownOpen && '{{ $open }}' == '' }"
>
    {{ $slot }}
</ul>
