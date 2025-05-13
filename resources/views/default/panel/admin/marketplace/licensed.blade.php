@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', 'Marketplace')
@section('titlebar_actions')
    <div class="flex flex-wrap gap-2">
        <x-button
            variant="ghost-shadow"
            href="{{ route('dashboard.admin.marketplace.liextension') }}"
        >
            {{ __('Manage Addons') }}
        </x-button>
        <x-button href="{{ route('dashboard.admin.marketplace.index') }}">
            <x-tabler-plus class="size-4" />
            {{ __('Browse Add-ons') }}
        </x-button>
    </div>
    <x-button
            class="relative ms-2"
            variant="ghost-shadow"
            href="{{ route('dashboard.admin.marketplace.cart') }}">
        <x-tabler-shopping-cart class="size-4"/>
        {{ __('Cart') }}
        <small id="itemCount" class="bg-red-500 text-white ps-2 pe-2 absolute top-[-10px] right-[3px] rounded-[50%] border border-red-500">{{ count(is_array($cart) ? $cart : []) }}</small>
    </x-button>
@endsection
@section('content')
    <div class="py-10">
        <div class="flex flex-col gap-9">
            @include('panel.admin.market.components.marketplace-filter')

            <div class="lqd-extension-grid flex flex-col gap-4">
                @foreach ($items as $item)
                    <x-card
                        class="lqd-extension relative flex flex-col rounded-[20px] transition-all hover:-translate-y-1 hover:shadow-lg"
                        class:body="flex flex-wrap justify-between items-center gap-4"
                        data-price="{{ $item['price'] }}"
                        data-installed="{{ $item['installed'] }}"
                        data-name="{{ $item['name'] }}"
                    >
                        <div class="flex grow items-center gap-7 lg:basis-2/3">
                            <img
                                class="shrink-0"
                                src="{{ $item['icon'] }}"
                                width="53"
                                height="53"
                                alt="{{ $item['name'] }}"
                            >
                            <div class="grow">
                                <div class="mb-4 flex flex-wrap gap-4">
                                    <h3 class="m-0 text-xl font-semibold">
                                        {{ $item['name'] }}
                                    </h3>
                                    <p class="flex items-center gap-2 text-2xs font-medium">
                                        <span @class([
                                            'size-2 inline-block rounded-full',
                                            'bg-green-500' => $item['installed'],
                                            'bg-foreground/10' => !$item['installed'],
                                        ])></span>
                                        {{ $item['installed'] ? __('Installed') . ($item['version'] != $item['db_version'] ? '  -  ' . trans('Update Available') : '') : __('Not Installed') }}

                                    </p>
                                </div>
                                <p class="text-base leading-normal">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        </div>

                        <div class="relative z-2">

                            @if ($item['version'] != $item['db_version'])
                                <x-button
                                    data-name="{{ $item['slug'] }}"
                                    @class([
                                        'size-14 btn_install group me-2',
                                        'hidden' => !$item['installed'],
                                    ])
                                    variant="outline"
                                    hover-variant="warning"
                                    size="none"
                                >
                                    <x-tabler-reload class="size-6 group-[&.lqd-is-busy]:hidden" />
                                    <x-tabler-refresh class="size-6 hidden animate-spin group-[&.lqd-is-busy]:block" />
                                    <span class="sr-only">
                                        {{ __('Upgrade') }}
                                    </span>
                                </x-button>
                            @endif

                            <x-button
                                data-name="{{ $item['slug'] }}"
                                @class([
                                    'size-14 btn_installed group',
                                    'hidden' => $item['installed'] == 0,
                                ])
                                variant="outline"
                                hover-variant="danger"
                                size="none"
                            >
                                <x-tabler-trash class="size-6 group-[&.lqd-is-busy]:hidden" />
                                <x-tabler-refresh class="size-6 hidden animate-spin group-[&.lqd-is-busy]:block" />
                                <span class="sr-only">
                                    {{ __('Uninstall') }}
                                </span>
                            </x-button>


                            <x-button
                                data-folder="{{ $item['extension_folder'] }}"
                                data-name="{{ $item['slug'] }}"
                                @class([
                                    'size-14 btn_install group',
                                    'hidden' => $item['installed'] == 1,
                                ])
                                variant="outline"
                                hover-variant="success"
                                size="none"
                            >
                                <x-tabler-plus class="size-6 group-[&.lqd-is-busy]:hidden" />
                                <x-tabler-refresh class="size-6 hidden animate-spin group-[&.lqd-is-busy]:block" />
                                <span class="sr-only">
                                    {{ __('Install') }}
                                </span>
                            </x-button>
                        </div>
                        <a
                            class="absolute inset-0 z-1"
                            href="{{ route('dashboard.admin.marketplace.extension', ['slug' => $item['slug']]) }}"
                        >
                            <span class="sr-only">
                                {{ __('View details') }}
                            </span>
                        </a>
                    </x-card>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
@endpush
