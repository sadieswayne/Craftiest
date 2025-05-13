@php
    $base_class = 'lqd-card text-card-foreground w-full transition-all group/card';
    $head_base_class = 'lqd-card-head border-b border-card-border px-6 py-3.5 relative transition-border';
    $body_base_class = 'lqd-card-body relative only:grow';
    $foot_base_class = 'lqd-card-foot border-t border-card-border relative transition-border';

    $variations = [
        'variant' => [
            'none' => '',
            'solid' => 'lqd-card-solid bg-card-background',
            'outline' => 'lqd-card-outline border border-card-border',
            'shadow' => 'lqd-card-shadow bg-card-background shadow-card',
            'outline-shadow' => 'lqd-card-outline-shadow border border-card-border shadow-card bg-card-background',
        ],
        'size' => [
            'none' => 'lqd-card-size-none',
            'xs' => 'lqd-card-xs px-5 py-3',
            'sm' => 'lqd-card-sm p-4',
            'md' => 'lqd-card-md py-5 px-7',
            'lg' => 'lqd-card-lg py-8 px-10',
        ],
        'roundness' => [
            'none' => 'lqd-card-roundness-none',
            'default' => 'lqd-card-roundness-default rounded-xl',
            '2xl' => 'lqd-card-roundness-2xl rounded-2xl',
            '3xl' => 'lqd-card-roundness-3xl rounded-3xl',
            '4xl' => 'lqd-card-roundness-4xl rounded-4xl',
            '5xl' => 'lqd-card-roundness-5xl rounded-5xl',
            '6xl' => 'lqd-card-roundness-6xl rounded-6xl',
        ],
    ];

    $variant = isset($variations['variant'][$variant]) ? $variations['variant'][$variant] : $variations['variant'][Theme::getSetting('defaultVariations.card.variant', 'outline')];
    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size'][Theme::getSetting('defaultVariations.card.size', 'md')];
    $roundness = isset($variations['roundness'][$roundness])
        ? $variations['roundness'][$roundness]
        : $variations['roundness'][Theme::getSetting('defaultVariations.card.roundness', 'default')];
@endphp

<div {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $variant, $roundness, $attributes->get('class')) }}>
    @if (!empty($head))
        <div {{ $attributes->twMergeFor('head', $head_base_class, $head->attributes->get('class')) }}>
            {{ $head }}
        </div>
    @endif
    <div {{ $attributes->twMergeFor('body', $body_base_class, $size) }}>
        {{ $slot }}
    </div>
    @if (!empty($foot))
        <div {{ $attributes->twMergeFor('foot', $foot_base_class, $foot->attributes->get('class')) }}>
            {{ $foot }}
        </div>
    @endif
</div>
