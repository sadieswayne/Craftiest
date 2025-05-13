@php
    $base_class = 'lqd-form-step flex items-center gap-3 rounded-xl bg-[rgba(157,107,221,0.1)] px-4 py-3 text-sm font-semibold';
    $step_base_class = 'lqd-form-step-num inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-[#9D6BDD] text-sm text-white';
@endphp

<h3
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class) }}
    {{ $attributes }}
>
    <span {{ $attributes->twMergeFor('step', $step_base_class) }}>
        {{ $step }}
    </span>
    {{ $label }}
    {{ $slot }}
</h3>
