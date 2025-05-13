@php
    $base_class = "lqd-posts-item lqd-docs-item lqd-docs-item-$style relative w-full items-center border-b transition-all last:border-b-0 hover:bg-foreground/5 group-[&[data-view-mode=grid]]:min-h-48 group-[&[data-view-mode=grid]]:bg-card-background group-[&[data-view-mode=grid]]:gap-0 group-[&[data-view-mode=grid]]:pb-1";
@endphp

@if ($style === 'min')
    @include('default.components.documents.item-min', [
        'entry' => $entry,
        'attributes' => $attributes,
        'base_class' => $base_class,
    ])
@else
    @include('default.components.documents.item-extended', [
        'entry' => $entry,
        'attributes' => $attributes,
        'base_class' => $base_class,
    ])
@endif
