<div class="{{ @twMerge('lqd-generator-action-btns flex w-full flex-wrap items-center gap-2 text-2xs', $class ?? '') }}">
    @if ($type !== 'code' && $type !== 'image')
        <button
            class="inline-flex size-7 items-center justify-center rounded-sm transition-colors hover:bg-foreground/5"
            id="workbook_undo"
            title="{{ __('Undo') }}"
        >
            <x-tabler-arrow-back-up class="size-5" />
        </button>
        <button
            class="inline-flex size-7 items-center justify-center rounded-sm transition-colors hover:bg-foreground/5"
            id="workbook_redo"
            title="{{ __('Redo') }}"
        >
            <x-tabler-arrow-forward-up class="size-5" />
        </button>
    @endif
    @if ($type !== 'image')
        <button
            class="inline-flex size-7 items-center justify-center rounded-sm transition-colors hover:bg-foreground/5"
            id="workbook_copy"
            title="{{ __('Copy to clipboard') }}"
        >
            <x-tabler-copy class="size-5" />
        </button>
    @endif
    @if ($type !== 'code')
        @if ($type === 'image')
            <a
                class="inline-flex size-7 items-center justify-center rounded-sm transition-colors hover:bg-foreground/5"
                href="{{ $output }}"
                download
                title="{{ __('Download') }}"
            >
                <x-tabler-download class="size-5" />
            </a>
        @elseif ($type === 'voiceover' || $type === \App\Domains\Entity\Enums\EntityEnum::ISOLATOR->value)
            <a
                class="inline-flex size-7 items-center justify-center rounded-sm transition-colors hover:bg-foreground/5"
                href="/uploads/{{ $output }}"
                download
                target="_blank"
                title="{{ __('Download') }}"
            >
                <x-tabler-download class="size-5" />
            </a>
        @else
            <x-dropdown.dropdown offsetY="1rem">
                <x-slot:trigger
                    class="px-2 py-1"
                    variant="link"
                    size="xs"
                    title="{{ __('Download') }}"
                >
                    <x-tabler-download class="size-5" />
                </x-slot:trigger>
                <x-slot:dropdown
                    class="overflow-hidden"
                >
                    <button
                        class="workbook_download flex w-full items-center gap-1 rounded-md p-2 font-medium hover:bg-foreground/5"
                        data-doc-type="doc"
                        data-doc-name="{{ $title }}"
                    >
                        <x-tabler-brand-office
                            class="size-6"
                            stroke-width="1.5"
                        />
                        MS Word
                    </button>
                    <button
                        class="workbook_download flex w-full items-center gap-1 rounded-md p-2 text-2xs font-medium hover:bg-foreground/5"
                        data-doc-type="html"
                        data-doc-name="{{ $title }}"
                    >
                        <x-tabler-brand-html5
                            class="size-6"
                            stroke-width="1.5"
                        />
                        HTML
                    </button>
                </x-slot:dropdown>
            </x-dropdown.dropdown>
        @endif
    @endif
    @if (isset($is_generated_doc))
        <a
            class="inline-flex size-7 items-center justify-center rounded-sm text-2xs text-red-600 transition-colors hover:bg-foreground/5"
            id="workbook_delete"
            href="{{ isset($slug) && isset($title) ? route('dashboard.user.openai.documents.delete', $slug) : '#' }}"
            @if (isset($slug) && isset($title)) onclick="return confirm('Are you sure?')" @endif
            @if (!isset($slug)) @click.prevent="tinyMCE?.activeEditor?.setContent('')" @endif
            title="{{ __('Delete') }}"
        >
            <x-tabler-circle-minus class="size-5" />
        </a>
    @endif

    @if (isset($workbook))
        <div class="flex items-center gap-4 lg:ms-4 lg:hidden">
            <x-dropdown.dropdown
                class="doc-share-dropdown"
                class:dropdown-dropdown="max-lg:end-auto max-lg:start-0"
                anchor="end"
                offsetY="20px"
            >
                <x-slot:trigger>
                    {{ __('Share') }}
                    <span
                        class="inline-grid size-6 shrink-0 place-items-center rounded-md bg-foreground/10 transition-all group-hover/dropdown:scale-105 group-hover/dropdown:bg-heading-foreground group-hover/dropdown:text-heading-background"
                    >
                        <x-tabler-share class="size-4" />
                    </span>
                </x-slot:trigger>
                <x-slot:dropdown
                    class="py-1 text-2xs"
                >
                    <x-button
                        class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                        variant="link"
                        target="_blank"
                        href="http://twitter.com/share?text={{ $workbook->output }}"
                    >
                        <x-tabler-brand-x />
                        @lang('X')
                    </x-button>
                    <x-button
                        class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                        variant="link"
                        target="_blank"
                        href="https://wa.me/?text={{ htmlspecialchars($workbook->output) }}"
                    >
                        <x-tabler-brand-whatsapp />
                        @lang('Whatsapp')
                    </x-button>
                    <x-button
                        class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                        variant="link"
                        target="_blank"
                        href="https://t.me/share/url?url={{ request()->host() }}&text={{ htmlspecialchars($workbook->output) }}"
                    >
                        <x-tabler-brand-telegram />
                        @lang('Telegram')
                    </x-button>
                </x-slot:dropdown>
            </x-dropdown.dropdown>

            @if (!empty($integrations) && $checkIntegration)
                <x-dropdown.dropdown
                    class="doc-integrate-publish-dropdown"
                    anchor="end"
                    offsetY="20px"
                >
                    <x-slot:trigger
                        variant="success"
                    >
                        {{ __('Publish') }}
                    </x-slot:trigger>
                    <x-slot:dropdown
                        class="min-w-48 text-xs"
                    >
                        <p class="border-b px-3 py-3 text-foreground/70">
                            @lang('Integrations')
                        </p>
                        <div class="pb-2">
                            @foreach ($integrations as $integration)
                                <x-button
                                    class="w-full justify-start rounded-none px-3 py-2 text-start hover:bg-heading-foreground/5"
                                    variant="link"
                                    href="{{ route('dashboard.user.integration.share.workbook', [$integration->id, $workbook->id]) }}"
                                >
                                    {{ $integration?->integration?->app }}
                                </x-button>
                            @endforeach

                        </div>
                    </x-slot:dropdown>
                </x-dropdown.dropdown>
            @endif
        </div>
    @endif
</div>
