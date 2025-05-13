<x-header
    @class([
        'px-5 lg:px-10' => isset($layout_wide),
    ])
    layout-wide="{{ isset($layout_wide) ? $layout_wide : '' }}"
>
</x-header>
