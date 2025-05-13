@php
    $base_class =
        'lqd-btn group inline-flex items-center justify-center gap-1.5 text-xs font-medium rounded-full transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-black/5 disabled:bg-foreground/10 disabled:text-foreground/35 disabled:pointer-events-none';

    $variations = [
        'variant' => [
            'none' => 'lqd-btn-variant-none',
            'primary' =>
                'lqd-btn-primary bg-primary text-primary-foreground hover:bg-primary/90 hover:shadow-primary/10 focus-visible:bg-primary/90 focus-visible:shadow-primary/10',
            'secondary' => 'lqd-btn-secondary bg-secondary text-secondary-foreground',
            'outline' => 'lqd-btn-outline outline outline-input-border -outline-offset-1 focus-visible:outline-2 focus-visible:outline-offset-0 focus-visible:outline-secondary',
            'danger' => 'lqd-btn-danger bg-red-500 text-white',
            'success' => 'lqd-btn-success bg-emerald-500 text-white hover:bg-emerald-400 focus-visible:bg-emerald-400',
            'ghost' => 'lqd-btn-ghost bg-transparent text-foreground hover:bg-foreground/10 focus-visible:bg-foreground/10',
            'ghost-shadow' =>
                'lqd-btn-ghost-shadow bg-background text-foreground shadow-xs hover:bg-primary hover:text-primary-foreground dark:bg-foreground/[3%] dark:hover:bg-foreground dark:hover:text-background focus-visible:bg-primary focus-visible:text-primary-foreground dark:bg-foreground/[3%] dark:focus-visible:bg-foreground dark:focus-visible:text-background',
            'link' =>
                'lqd-btn-link bg-transparent text-foreground hover:underline p-0 hover:translate-y-0 hover:shadow-none focus-visible:underline p-0 focus-visible:translate-y-0 focus-visible:shadow-none focus-visible:outline-0',
        ],
        'hoverVariant' => [
            'none' => 'lqd-btn-hover-none',
            'danger' =>
                'lqd-btn-hover-danger hover:text-white hover:bg-rose-500 hover:border-rose-500 hover:outline-rose-500 focus-visible:text-white focus-visible:bg-rose-500 focus-visible:border-rose-500 focus-visible:outline-rose-500',
            'success' =>
                'lqd-btn-hover-success hover:text-white hover:bg-green-500 hover:border-green-500 hover:outline-green-500 focus-visible:text-white focus-visible:bg-green-500 focus-visible:border-green-500 focus-visible:outline-green-500',
            'primary' =>
                'lqd-btn-hover-primary hover:text-primary-foreground hover:bg-primary hover:border-primary hover:outline-primary focus-visible:text-primary-foreground focus-visible:bg-primary focus-visible:border-primary focus-visible:outline-primary',
            'secondary' =>
                'lqd-btn-hover-secondary hover:text-secondary-foreground hover:bg-secondary hover:border-secondary hover:outline-secondary focus-visible:text-secondary-foreground focus-visible:bg-secondary focus-visible:border-secondary focus-visible:outline-secondary',
        ],
        'size' => [
            'none' => 'lqd-btn-size-none',
            'sm' => 'lqd-btn-sm py-1 px-2.5',
            'md' => 'lqd-btn-md py-2 px-4',
            'lg' => 'lqd-btn-lg px-5 py-3',
            'xl' => 'lqd-btn-xl px-5 py-4',
        ],
    ];

    if ($variant === 'link') {
        $size = 'none';
    }

    $variant = isset($variations['variant'][$variant])
        ? $variations['variant'][$variant]
        : $variations['variant'][Theme::getSetting('defaultVariations.button.variant', 'primary')];
    $hoverVariant = isset($variations['hoverVariant'][$hoverVariant])
        ? $variations['hoverVariant'][$hoverVariant]
        : $variations['hoverVariant'][Theme::getSetting('defaultVariations.button.hoverVariant', 'none')];
    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size'][Theme::getSetting('defaultVariations.button.size', 'md')];

    if ($tag === 'button' && empty($type)) {
        $type = 'button';
    }

    if ($type === 'button' && (empty($tag) || $tag === 'a')) {
        $tag = 'button';
    }

    if ($type === 'submit') {
        $tag = 'button';
    }
@endphp

<{{ $tag }}
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $variant, $size, $hoverVariant, $attributes->get('class')) }}
    @if ($tag === 'a') href="{{ $href }}"
@else
    type="{{ $type }}" @endif
    {{ $attributes }}
>
    {{ $slot }}
    </{{ $tag }}>
