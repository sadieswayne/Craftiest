@props([
    'position' => 'te',
    'border-w' => '1px',
    'roundness' => '0px',
    'border-color' => 'hsl(var(--border))',
    'background-color' => 'hsl(var(--background))',
])

<span
    data-pos="{{ $position }}"
    {{ $attributes->withoutTwMergeClasses()->twMerge('lqd-cutout-2 pointer-events-none absolute z-0 inline-block bg-background transition-colors') }}
>
    <span class="lqd-cutout-2-border-ts"></span>
    <span class="lqd-cutout-2-border-bs"></span>
    <span class="lqd-cutout-2-border-be transition-[background]"></span>
</span>
