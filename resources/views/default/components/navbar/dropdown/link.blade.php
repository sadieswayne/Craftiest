@php
    $base_class =
        'lqd-navbar-dropdown-link py-2 px-5 hover:bg-transparent hover:underline [&.active]:bg-transparent [&.active]:underline dark:[&.active]:before:hidden group-[&.navbar-shrinked]/body:group-hover/nav-item:bg-transparent group-[&.navbar-shrinked]/body:group-hover/nav-item:text-inherit';
@endphp

<x-navbar.link
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    :$href
    :$slug
    :$label
    :$icon
    :$iconHtml
    :$activeCondition
    :$new
    :$letterIcon
    :$dropdownTrigger
    :$badge
>

</x-navbar.link>
