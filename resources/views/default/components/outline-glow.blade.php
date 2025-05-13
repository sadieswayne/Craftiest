@props([
    'effect' => 1,
])

@php
    $base_class = 'lqd-outline-glow absolute inline-block rounded-[inherit] pointer-events-none overflow-hidden lqd-outline-glow-effect-' . $effect;
    $inner_base_class = 'lqd-outline-glow-inner absolute start-1/2 top-1/2 inline-block aspect-square min-h-[150%] min-w-[150%] rounded-[inherit]';
@endphp

<span {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
    <span {{ $attributes->twMergeFor('inner', $inner_base_class) }}></span>
</span>
