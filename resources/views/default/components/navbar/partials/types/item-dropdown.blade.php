@php
    $href =
        \App\Helpers\Classes\Helper::hasRoute($item['route']) && $item['route_slug']
            ? route($item['route'], $item['route_slug'])
            : route(\App\Helpers\Classes\Helper::hasRoute($item['route']) ? $item['route'] : 'default');

    $is_active = $href === url()->current();

    if (!$is_active) {
        foreach ($item['children'] as $child) {
            if (!Route::has($child['route'])) {
                continue;
            }

            $child_href = $child['route_slug'] ? route($child['route'], $child['route_slug']) : route($child['route']);
            $child_is_active = $child_href === url()->current();

            if ($child_is_active) {
                $is_active = true;
                break;
            }
        }
    }
@endphp

<x-navbar.item
    id="{{ data_get($item, 'parent_key') ? data_get($item, 'parent_key') . '-' : '' }}{{ data_get($item, 'key') }}"
    has-dropdown
>
    <x-navbar.link
        class="{{ data_get($item, 'class') }}"
        label="{{ __($item['label']) }}"
        href="{{ $item['route'] }}"
        slug="{{ $item['route_slug'] }}"
        icon="{{ $item['icon'] }}"
        active-condition="{{ $is_active }}"
        onclick="{{ data_get($item, 'onclick') ?? '' }}"
        badge="{{ data_get($item, 'badge') ?? '' }}"
        dropdown-trigger
    />
    <x-navbar.dropdown.dropdown open="{{ $is_active }}">
        @foreach ($item['children'] as $child)
            @php
                $key = data_get($child, 'key');
            @endphp

            @if (\App\Helpers\Classes\PlanHelper::planMenuCheck($userPlan, $key))
                @if (data_get($child, 'show_condition', true) && data_get($item, 'is_active'))
                    @php
                        $child_href =
                            $child['route_slug'] && \App\Helpers\Classes\Helper::hasRoute($child['route'])
                                ? route($child['route'], $child['route_slug'])
                                : route(\App\Helpers\Classes\Helper::hasRoute($child['route']) ? $child['route'] : 'default');
                        $child_is_active = $child_href === url()->current();
                    @endphp

                    <x-navbar.dropdown.item>
                        <x-navbar.dropdown.link
                            icon="{{ $child['icon'] ?? '' }}"
                            label="{{ __($child['label']) }}"
                            href="{{ $child['route'] }}"
                            badge="{{ data_get($child, 'badge') ?? '' }}"
                            slug="{{ $child['route_slug'] }}"
                            active-condition="{{ $child_is_active }}"
                        ></x-navbar.dropdown.link>
                    </x-navbar.dropdown.item>
                @endif
            @endif
        @endforeach
    </x-navbar.dropdown.dropdown>
</x-navbar.item>
