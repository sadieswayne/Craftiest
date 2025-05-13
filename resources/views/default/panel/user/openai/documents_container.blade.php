@php
    //Replicating table styles from table component
    $base_class = 'rounded-xl transition-colors';

    $variations = [
        'variant' => [
            'solid' => 'rounded-xl bg-table-background pt-1 group-[&[data-view-mode=grid]]:bg-transparent',
            'outline' => 'rounded-xl border border-table-border pt-1 group-[&[data-view-mode=grid]]:border-0',
            'shadow' => ' rounded-xl shadow-table bg-table-background pt-1 group-[&[data-view-mode=grid]]:shadow-none group-[&[data-view-mode=grid]]:bg-transparent',
            'outline-shadow' => 'rounded-xl border border-table-border pt-1 shadow-table bg-table-background',
            'plain' => '',
        ],
    ];

    $variant =
        isset($variant) && isset($variations['variant'][$variant])
            ? $variations['variant'][$variant]
            : $variations['variant'][Theme::getSetting('defaultVariations.table.variant', 'outline')];

    $class = @twMerge($base_class, $variant);
@endphp

<div
    class="lqd-posts-container lqd-docs-container group transition-all [&[aria-busy=true]]:animate-pulse max-lg:[&[data-view-mode=list]]:max-w-full max-lg:[&[data-view-mode=list]]:overflow-x-auto"
    id="lqd-docs-container"
    data-view-mode="list"
    x-bind:data-view-mode="$store.docsViewMode.docsViewMode"
    x-init
    x-merge.transition
>
    {{-- Setting the view mode attribute before contents load to avoid page flashes --}}
    <script>
        document.querySelector('.lqd-docs-container')?.setAttribute('data-view-mode', localStorage.getItem('docsViewMode')?.replace(/\"/g, '') || 'list');
    </script>

    <div class="{{ $class }}">
        <div
            class="lqd-posts-head lqd-docs-head grid gap-x-4 border-b px-4 py-3 text-4xs font-medium uppercase leading-tight tracking-wider text-foreground/50 [grid-template-columns:3fr_repeat(2,minmax(0,1fr))_100px_1fr] group-[&[data-view-mode=grid]]:hidden">
            <span>
                {{ __('Name') }}
            </span>

            <span>
                {{ __('Type') }}
            </span>

            <span>
                {{ __('Date') }}
            </span>

            <span>
                {{ __('Cost') }}
            </span>

            <span class="text-end">
                {{ __('Actions') }}
            </span>
        </div>

        @include('panel.user.openai.documents_list')
    </div>

    {{ $items->links('pagination::ajax', [
        'action' => route('dashboard.user.openai.documents.all', ['id' => $currfolder?->id, 'listOnly' => true]),
        'currfolder' => $currfolder,
        'target_id' => 'lqd-docs-container',
    ]) }}
</div>
