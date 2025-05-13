<x-box
    style="3"
    title="{!! __($item->title) !!}"
    desc="{!! __($item->description) !!}"
>
    <x-slot name="image">
        <img
            class="-mx-8 max-w-[calc(100%+4rem)]"
            src="{{ custom_theme_url($item->image, true) }}"
            alt="{!! __($item->title) !!}"
            width="696"
            height="426"
        >
    </x-slot>
</x-box>
