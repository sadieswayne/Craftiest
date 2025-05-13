@php
    $base_class = 'lqd-legend flex items-center gap-2.5';
    $box_base_class = 'lqd-legend-box shrink-0 grow-0 rounded-full bg-primary';
    $label_base_class = 'lqd-legend-label';

    $variations = [
        'size' => [
            'sm' => 'lqd-legend-sm size-2',
            'sm' => 'lqd-legend-sm size-2',
            'md' => 'lqd-legend-md size-2.5',
        ],
    ];

    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size']['md'];
@endphp

<div {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
    <span {{ $attributes->twMergeFor('box', $size, $box_base_class) }}></span>
    <span {{ $attributes->twMergeFor('label', $label_base_class) }}>
        {{ $label }}
    </span>
    {{ $slot }}
</div>
