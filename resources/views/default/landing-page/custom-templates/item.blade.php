<x-box
    wrapperClass="templates-all templates-{{ \Illuminate\Support\Str::slug($item->filters) }}"
    style="2"
    title="{{ __($item->title) }}"
    desc="{{ __($item->description) }}"
>
    <x-slot name="image">
        <span
            class="size-11 mb-4 inline-flex items-center justify-center rounded-lg bg-gradient-to-bl from-[#f0f0f2] to-[#d7d7d9] [&_path]:fill-inherit [&_svg]:h-5 [&_svg]:w-6 [&_svg]:fill-[#7c7c7e]"
        >
            {!! stripslashes($item->image) !!}
        </span>
    </x-slot>
</x-box>
