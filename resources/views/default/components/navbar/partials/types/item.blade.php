@if ($item['show_condition'])
    @php
        $href =
            $item['route_slug'] && \App\Helpers\Classes\Helper::hasRoute($item['route'])
                ? route($item['route'], $item['route_slug'])
                : (\App\Helpers\Classes\Helper::hasRoute($item['route'])
                    ? route($item['route'])
                    : '');
        $is_active = $href === url()->current() || activeRoute(...$item['active_condition'] ?: []);
    @endphp

    <x-navbar.item id="{{ data_get($item, 'parent_key') ? data_get($item, 'parent_key') . '-' : '' }}{{ data_get($item, 'key') }}">
        <x-navbar.link
            class:letter-icon="{{ $item['letter_icon_bg'] }}"
            class="{{ data_get($item, 'class') }}"
            data-name="{{ data_get($item, 'data-name') }}"
            letter-icon-styles="{{ $item['letter_icon_bg'] }}"
            label="{{ __($item['label']) }}"
            href="{{ $item['route'] }}"
            slug="{{ $item['route_slug'] }}"
            icon="{{ $item['icon'] }}"
            active-condition="{{ $is_active }}"
            letter-icon="{{ (int) $item['letter_icon'] }}"
            onclick="{{ data_get($item, 'onclick') ?? '' }}"
            badge="{{ data_get($item, 'badge') ?? '' }}"
        />
    </x-navbar.item>
@endif
