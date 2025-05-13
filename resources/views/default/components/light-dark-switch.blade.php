<x-button
    {{ $attributes->twMerge('lqd-light-dark-switch size-6 max-lg:size-10 flex items-center justify-center hover:bg-transparent max-lg:rounded-full max-lg:border max-lg:dark:bg-white/[3%]') }}
    size="none"
    href="#"
    title="{{ __('Toggle dark/light') }}"
    variant="link"
    x-data="{}"
    @click.prevent="$store.darkMode.toggle()"
>
    <x-tabler-sun
        class="hidden size-5 dark:block"
        stroke-width="1.5"
    />
    <x-tabler-moon
        class="size-5 dark:hidden"
        stroke-width="1.5"
    />
</x-button>
