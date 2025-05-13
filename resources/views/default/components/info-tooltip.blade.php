@php
    $base_class =
        'lqd-tooltip-container group relative inline-flex cursor-default before:absolute before:-start-1.5 before:-top-1.5 before:h-7 before:w-7 [&:hover>.lqd-tooltip-content]:visible [&:hover>.lqd-tooltip-content]:translate-y-0 [&:hover>.lqd-tooltip-content]:opacity-100';
    $content_class =
        'lqd-tooltip-content min-w-64 invisible absolute start-1/2 z-50 mb-3 -translate-x-1/2 rounded-xl bg-background/80 px-4 py-3 text-center text-xs leading-normal text-foreground opacity-0 shadow-lg shadow-black/5 backdrop-blur-sm backdrop-saturate-150 transition-all before:absolute before:inset-x-0 before:h-3';

    if ($anchor === 'bottom') {
        $content_class .= ' bottom-full translate-y-1 before:-top-3';
    } elseif ($anchor === 'top') {
        $content_class .= ' top-full -translate-y-1 before:-bottom-3';
    }
@endphp

<span {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}>
    <span {{ $attributes->twMergeFor('icon', 'lqd-tooltip-icon opacity-40') }}>
        <x-tabler-info-circle-filled class="size-4" />
    </span>
    <span {{ $attributes->twMergeFor('content', $content_class) }}>
        {{ $text }}

        @if ($drivers->isNotEmpty())
            <div>
                <h5 class="font-semibold">{{ __('Credits Details') }}</h5>
                <hr class="my-3 border-heading-foreground/10" />

                @foreach ($drivers as $driver)
                    @if (!$driver->hasCreditBalance())
                        @continue
                    @endif
                    <div class="flex justify-between gap-x-1 border-b py-1.5 text-2xs last:border-b-0">
                        <span class="text-start">{{ $driver->enum()->value }}</span>
                        <span class="text-end font-medium">{{ $driver->isUnlimitedCredit() ? __('Unlimited') : $driver->creditBalance() }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </span>
</span>
