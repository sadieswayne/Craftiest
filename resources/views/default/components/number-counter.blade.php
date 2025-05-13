@php
    $base_class = 'lqd-number-counter inline-flex h-[1lh] leading-none';
    $value_class = 'lqd-number-counter-value inline-flex h-full';
@endphp

@props([
    'value' => 0,
    'options' => [],
    'dynamicValueListener' => false,
])

<span {{ $attributes->withoutTwMergeClasses()->twMerge($base_class) }}>
    <span
        {{ $attributes->twMergeFor('value', $value_class) }}
        x-data="numberCounter({ value: '{{ $value }}', options: {{ json_encode($options) }} })"
        @if ($dynamicValueListener) @dynamic-value-{{ $dynamicValueListener }}.window="updateValue($event.detail)" @endif
    >
        {{ preg_replace('/\d/', '0', $value) }}
    </span>
</span>
