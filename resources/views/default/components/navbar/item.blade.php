@php
    $base_class = 'lqd-navbar-item relative group/nav-item';
@endphp

<li
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    x-data="navbarItem()"
    x-bind="item"
>
    {{ $slot }}
</li>
