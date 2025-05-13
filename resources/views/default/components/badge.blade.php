@php
    $base_class = 'lqd-badge inline-flex py-0.5 px-2 rounded-full font-medium transition-all hover:-translate-y-0.5 hover:shadow-lg';

    $variations = [
        'variant' => [
            'default' => 'lqd-badge-default bg-foreground/10 text-heading-foreground',
            'primary' => 'lqd-badge-primary bg-primary text-primary-foreground hover:bg-primary hover:text-white hover:shadow-primary/20',
            'secondary' => 'lqd-badge-secondary bg-secondary text-secondary-foreground hover:bg-secondary hover:text-secondary-foreground hover:shadow-secondary/20',
            'info' => 'lqd-badge-success bg-teal-100 text-black hover:bg-teal-300 hover:shadow-teal-200/10',
            'success' => 'lqd-badge-success bg-emerald-500/10 text-emerald-500 hover:bg-emerald-400 hover:text-white hover:shadow-emerald-400/20',
            'danger' => 'lqd-badge-danger bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white hover:shadow-rose-500/20',
            'warning' => 'lqd-badge-warning bg-orange-500/10 text-orange-500 hover:bg-orange-500 hover:text-white hover:shadow-orange-500/20',
        ],
    ];

    $variant = isset($variations['variant'][$variant]) ? $variations['variant'][$variant] : $variations['variant']['default'];
@endphp

<span {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $variant, $attributes->get('class')) }}>
    {{ $slot }}
</span>
