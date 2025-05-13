<x-dropdown.dropdown
    {{ $attributes->twMerge('header-language-dropdown') }}
    anchor="end"
    offsetY="26px"
>
    <x-slot:trigger
        {{ $attributes->twMergeFor('trigger', 'size-6 max-lg:size-10 max-lg:border max-lg:dark:bg-white/[3%]') }}
        size="none"
    >
        <x-tabler-world stroke-width="1.5" />
    </x-slot:trigger>

    <x-slot:dropdown
        class="overflow-hidden"
    >
        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            @if (in_array($localeCode, explode(',', $settings_two->languages)))
                <a
                    class="flex items-center gap-2 border-b px-3 py-2 text-heading-foreground transition-colors last:border-b-0 hover:bg-foreground/5 hover:no-underline"
                    rel="alternate"
                    hreflang="{{ $localeCode }}"
                    href="?app_locale={{ $localeCode }}"
                >
                    <span class="text-xl">
                        {{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}
                    </span>
                    {{ $properties['native'] }}
                </a>
            @endif
        @endforeach
    </x-slot:dropdown>
</x-dropdown.dropdown>
