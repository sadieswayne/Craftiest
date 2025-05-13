@php
    $base_class = 'lqd-favorite-icon flex items-center justify-center w-4 h-4 group';
@endphp

<span
    data-is-active="{{ $isFavorited ? 'true' : 'false' }}"
    {{ $attributes }}
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
>
    <x-dynamic-component
        class="hidden h-full w-full group-[&[data-is-active=true]]:block"
        :component="$activeIcon"
    />
    <x-dynamic-component
        class="h-full w-full group-[&[data-is-active=true]]:hidden"
        :component="$idleIcon"
    />
</span>
