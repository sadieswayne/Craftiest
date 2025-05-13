@php
    $base_class = 'lqd-alert border border-input-border bg-input-background flex gap-2 font-medium [&_:first-child]:mt-0 [&_:last-child]:mb-0';
    $icon_base_class = 'lqd-alert-icon size-5 shrink-0';

    $variations = [
        'variant' => [
            'info' => 'lqd-alert-info shadow-sm text-blue-600',
            'warn' => 'lqd-alert-warn shadow-sm text-orange-600',
            'danger' => 'lqd-alert-danger shadow-sm text-red-600 dark:text-red-500',
            'success' => 'lqd-alert-danger shadow-sm text-green-600',

            'info-fill' => 'lqd-alert-info bg-blue-700/10 text-blue-800 border-none',
            'warn-fill' => 'lqd-alert-warn bg-yellow-700/10 text-yellow-900 border-none dark:bg-yellow-300/10 dark:text-yellow-500',
            'danger-fill' => 'lqd-alert-danger bg-red-700/10 text-red-900 border-none',
            'success-fill' => 'lqd-alert-success bg-green-700/10 text-green-800 border-none',
        ],
        'size' => [
            'none' => 'lqd-alert-size-none',
            'sm' => 'lqd-alert-sm p-2 rounded-md',
            'md' => 'lqd-alert-md px-5 py-1.5 rounded-lg',
            'lg' => 'lqd-alert-lg p-6 rounded-xl',
        ],
    ];

    $variant = isset($variations['variant'][$variant]) ? $variations['variant'][$variant] : $variations['variant']['info'];
    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size']['md'];
@endphp

<div
    {{--
        using this will not merge tailwind classes correctly
        {{ $attributes->merge(['class' => $base_class . ' ' . $variant . ' ' . $size]) }}
     --}}
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $variant, $size) }}
    {{ $attributes }}
>
    @if (filled($icon))
        <x-dynamic-component
            :component="$icon"
            {{ $attributes->withoutTwMergeClasses()->twMergeFor('icon', $icon_base_class) }}
        />
    @endif
    <div class="grow">
        {{ $slot }}
    </div>
</div>
