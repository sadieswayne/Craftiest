<div
    class="lqd-generator-actions-top-bar relative flex h-[--editor-tb-h] justify-between border-b bg-background"
    x-data="{ mobileOptionsShow: false }"
>
    <div class="flex w-full justify-between gap-4 lg:hidden">
        <a
            class="text-heading flex grow basis-1/3 items-center gap-2 px-4 text-xs font-medium leading-tight lg:hidden"
            href="#"
            x-data="{}"
            @click.prevent="mobileOptionsShow = !mobileOptionsShow"
        >
            <x-tabler-dots-circle-horizontal
                class="size-5"
                stroke-width="1.5"
                ::class="{ hidden: mobileOptionsShow }"
            />
            <x-tabler-x
                class="size-5"
                stroke-width="1.5"
                ::class="{ hidden: !mobileOptionsShow }"
            />
            {{ __('Options') }}
        </a>

        <a
            class="flex shrink-0 basis-1/3 items-center justify-center text-center"
            href="{{  (route('dashboard.index')) }}"
        >
            @if (isset($setting->logo_dashboard))
                <img
                    class="h-auto max-h-8 shrink-0 dark:hidden"
                    src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                    @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto max-h-8 shrink-0 dark:block"
                    src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                    @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @else
                <img
                    class="h-auto max-h-8 shrink-0 dark:hidden"
                    src="{{ custom_theme_url($setting->logo_path, true) }}"
                    @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto max-h-8 shrink-0 dark:block"
                    src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                    @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @endif
        </a>

        {{-- There is a separate save button for mobile at bottom --}}
        <a
            class="web_hook_save text-heading flex basis-1/3 items-center justify-end gap-1 px-4 py-4 text-xs font-medium leading-tight"
            href="#"
        >
            {{ __('Save') }}
            <x-tabler-world-share class="size-5" />
        </a>
    </div>

    <div
        class="flex w-full justify-between max-lg:invisible max-lg:absolute max-lg:start-0 max-lg:top-full max-lg:flex-col max-lg:bg-background max-lg:shadow-lg max-lg:shadow-black/10 max-lg:[&.active]:visible"
        x-data="{}"
        :class="{ 'active': mobileOptionsShow }"
    >
        <div class="lqd-generator-actions-top-bar-col-start flex w-[--sidebar-w] max-lg:w-full max-lg:flex-col">
            <a
                class="text-heading flex items-center gap-1 px-5 py-4 text-[12px] font-medium leading-tight"
                href="{{ route('dashboard.user.index') }}"
            >
                <x-tabler-arrow-left class="size-4 rtl:rotate-180" />
                {{ __('Back to Dashboard') }}
            </a>

            <hr class="m-0 h-full w-px border-none bg-foreground/5">

            <form
                class="relative grow"
                action="#"
            >
                <input
                    class="peer w-full border-none bg-transparent px-5 py-4 text-[12px] font-medium text-inherit focus:outline-none max-lg:border max-lg:border-t"
                    id="document_title"
                    type="text"
                    placeholder="@lang('Untitled Document')"
                    value="@lang('Untitled Document')"
                />
                <span
                    class="pointer-events-none absolute end-3 top-1/2 inline-flex size-7 -translate-y-1/2 items-center justify-center rounded-full border shadow-sm transition-opacity peer-focus:opacity-0"
                >
                    <x-tabler-pencil class="size-4" />
                </span>
            </form>

            <hr class="m-0 h-full w-px border-none bg-foreground/5">
        </div>

        <div
            class="lqd-generator-actions-top-bar-col-middle flex grow transition-opacity duration-[0.4s] group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:pointer-events-none group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:opacity-10 max-lg:-order-1 max-lg:hidden">
            <div class="flex w-full items-center justify-center text-center">
                <a
                    class="shrink-0"
                    href="{{  (route('dashboard.index')) }}"
                >
                    @if (isset($setting->logo_dashboard))
                        <img
                            class="h-auto max-h-8 w-full dark:hidden"
                            src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                            @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                        <img
                            class="hidden h-auto max-h-8 w-full dark:block"
                            src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                            @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                    @else
                        <img
                            class="h-auto max-h-8 w-full dark:hidden"
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                        <img
                            class="hidden h-auto max-h-8 w-full dark:block"
                            src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                            @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                    @endif
                </a>
            </div>
        </div>

        <div
            class="lqd-generator-actions-top-bar-col-end flex min-w-[--sidebar-w] justify-end transition-opacity duration-[0.4s] group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:pointer-events-none group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:opacity-10 max-lg:flex-col max-lg:!opacity-100">
            <hr class="m-0 h-full w-px border-none bg-foreground/5">

            <div class="flex items-center gap-2 px-7 max-lg:justify-center max-lg:py-2">
                <div class="flex gap-1 rtl:flex-row-reverse">
                    <button
                        class="inline-flex size-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                        id="workbook_undo"
                        title="{{ __('Undo') }}"
                    >
                        <x-tabler-arrow-back-up class="size-5" />
                        <span class="sr-only">{{ __('Undo') }}</span>
                    </button>
                    <button
                        class="inline-flex size-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                        id="workbook_redo"
                        title="{{ __('Redo') }}"
                    >
                        <x-tabler-arrow-forward-up class="size-5" />
                        <span class="sr-only">{{ __('Redo') }}</span>
                    </button>
                </div>

                <button
                    class="inline-flex size-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                    id="workbook_copy"
                    title="{{ __('Copy to clipboard') }}"
                >
                    <x-tabler-copy class="size-5" />
                    <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                </button>

                <button
                    class="inline-flex size-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                    id="workbook_print"
                    title="{{ __('Print') }}"
                    onclick="return tinymce?.activeEditor?.execCommand('mcePrint');"
                >
                    <x-tabler-printer class="size-5" />
                    <span class="sr-only">{{ __('Print') }}</span>
                </button>

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
                            data-doc-name="{{ Config::get('app.name') }}"
                        >
                            <x-tabler-brand-office class="size-5" />
                            MS Word
                        </button>
                        <button
                            class="workbook_download flex w-full items-center gap-1 rounded-md p-2 font-medium hover:bg-foreground/5"
                            data-doc-type="pdf"
                            data-doc-name="{{ Config::get('app.name') }}"
                        >
                            <x-tabler-file-type-pdf class="size-5" />
                            PDF
                        </button>
                        <button
                            class="workbook_download flex w-full items-center gap-1 rounded-md p-2 font-medium hover:bg-foreground/5"
                            data-doc-type="html"
                            data-doc-name="{{ Config::get('app.name') }}"
                        >
                            <x-tabler-brand-html5 class="size-5" />
                            HTML
                        </button>
                    </x-slot:dropdown>
                </x-dropdown.dropdown>

                <button
                    class="inline-flex size-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                    title="{{ __('Add New Document') }}"
                    x-init
                    @click.prevent="toggleSideNavCollapse('expand')"
                >
                    <x-tabler-square-rounded-plus class="size-5" />
                    <span class="sr-only">{{ __('Add New Document') }}</span>
                </button>

            </div>

            <hr class="m-0 h-full w-px border-none bg-foreground/5">

            {{-- There is a separate save button for mobile on top --}}
            <a
                class="web_hook_save text-heading flex items-center gap-1 px-11 py-4 text-xs font-medium leading-tight max-lg:hidden"
                href="#"
            >
                {{ __('Save') }}
                <x-tabler-world-share class="size-5" />
            </a>
        </div>
    </div>
</div>
