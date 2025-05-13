<x-tabs-trigger
    target="#{{ \Illuminate\Support\Str::slug($item->menu_title) }}"
    label="{!! __($item->menu_title) !!}"
    active="{{ $loop->first ? 'true' : '' }}"
/>
