@php
    $base_class = 'lqd-posts-folder lqd-docs-folder relative z-20 px-5 py-2.5 bg-folder-background text-folder-foreground transition-all';

    if (!$folderSingleView) {
        $base_class .= ' hover:scale-[1.022] hover:shadow-md';
    } else {
        $base_class .= ' grow w-full';
    }
@endphp

<x-card
    class:body="flex items-center justify-between gap-5"
    id="{{ 'folder-' . $folder->id }}"
    size="none"
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
>
    <svg
        width="29"
        height="28"
        viewBox="0 0 29 28"
        fill="#c1c1c3"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M15.6547 6.62605L13.0714 1.45939C12.9641 1.24484 12.7992 1.06441 12.5951 0.93832C12.391 0.812229 12.1559 0.745456 11.916 0.745483H1.58268C1.24011 0.745483 0.91157 0.881569 0.669336 1.1238C0.427101 1.36604 0.291016 1.69458 0.291016 2.03715V7.20382C0.291016 7.54639 0.427101 7.87493 0.669336 8.11716C0.91157 8.3594 1.24011 8.49548 1.58268 8.49548H14.4993C14.7195 8.49551 14.9361 8.43924 15.1284 8.33203C15.3208 8.22481 15.4825 8.07021 15.5982 7.8829C15.714 7.69559 15.78 7.48179 15.7899 7.26182C15.7998 7.04184 15.7532 6.82299 15.6547 6.62605Z"
        />
        <path
            d="M27.416 6.75H1.29102C0.738731 6.75 0.291016 7.19772 0.291016 7.75V26.125C0.291016 26.4676 0.427101 26.7961 0.669336 27.0383C0.91157 27.2806 1.24011 27.4167 1.58268 27.4167H27.416C27.7586 27.4167 28.0871 27.2806 28.3294 27.0383C28.5716 26.7961 28.7077 26.4676 28.7077 26.125V8.04167C28.7077 7.6991 28.5716 7.37056 28.3294 7.12832C28.0871 6.88609 27.7586 6.75 27.416 6.75Z"
            fill-opacity="0.45"
        />
    </svg>
    <div class="text-2xs">
        <p
            class="m-0 font-medium"
            id="folder-name-{{ $folder->id }}"
        >
            {{ $folder->name }}
        </p>
        <small class="opacity-70">
            {{ $folder->updated_at->diffForHumans() }}
        </small>
    </div>

    @if (!$folderSingleView)
        <a
            class="absolute inset-0"
            href="{{ route('dashboard.user.openai.documents.all', $folder->id) }}"
        ></a>
    @endif

    <x-dropdown.dropdown
        class="ms-auto"
        anchor="end"
        offsetY="5px"
        triggerType="click"
    >
        <x-slot:trigger
            class="before:-star[5%]-0 z-10 size-9 p-0 text-inherit before:absolute before:-top-[5%] before:h-[120%] before:w-[120%] hover:bg-background hover:text-foreground"
            variant="ghost"
            size="xs"
        >
            <x-tabler-dots-vertical class="size-5" />
        </x-slot:trigger>

        <x-slot:dropdown
            class="overflow-hidden whitespace-nowrap py-1 text-2xs font-medium"
        >
            @foreach (['rename', 'delete'] as $action)
                <x-modal
                    title="{{ __(ucfirst($action) . ' folder') }}"
                    disable-modal="{{ $app_is_demo }}"
                    disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
                >
                    <x-slot:trigger
                        class="w-full justify-start rounded-none px-3 py-2 text-2xs hover:translate-y-0 hover:bg-foreground/5 hover:shadow-none focus-visible:bg-foreground/5"
                        variant="ghost"
                    >
                        @if ($action === 'rename')
                            <x-tabler-pencil-minus class="size-5" />
                        @else
                            <x-tabler-circle-minus class="size-5 text-red-600" />
                        @endif
                        {{ __(ucfirst($action)) }}
                    </x-slot:trigger>

                    <x-slot:modal>
                        @includeIf('panel.user.openai.components.modals.' . $action . '-folder', [
                            'folder_id' => $folder->id,
                            'folder_name' => $folder->name,
                        ])
                    </x-slot:modal>
                </x-modal>
            @endforeach
        </x-slot:dropdown>
    </x-dropdown.dropdown>
</x-card>
