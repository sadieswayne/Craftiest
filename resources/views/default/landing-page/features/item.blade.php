<x-box
    title="{!! __($item->title) !!}"
    desc="{!! __($item->description) !!}"
>
    <x-slot name="icon">
        {!! $item->image !!}
    </x-slot>
</x-box>
