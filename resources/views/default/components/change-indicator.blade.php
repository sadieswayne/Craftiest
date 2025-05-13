<span @class([
    'lqd-change-indicator inline-flex items-center leading-none text-3xs px-1.5 py-0.5 leading-snug rounded-md',
    'text-foreground bg-foreground/10' => $value == 0,
    'text-red-700 bg-red-700/10 dark:bg-red-600/10 dark:text-red-600' =>
        $value < 0,
    'text-green-600 bg-green-500/10' => $value > 0,
])>
    <x-dynamic-component
        class="size-3"
        :component="$value == 0 ? 'tabler-minus' : ($value < 0 ? 'tabler-chevron-down' : 'tabler-chevron-up')"
    />
    {{ $value }}%
</span>
