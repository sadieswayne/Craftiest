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
            </span>
        </x-button>
    </div>
@endsection
@section('content')
    <div class="py-10">
        <div class="flex flex-col gap-9">
            @include('panel.admin.market.components.marketplace-filter')

            <div class="lqd-extension-grid flex flex-col gap-4">
                @foreach ($extensions as $extension)
                    <x-card
                        class="lqd-extension relative flex flex-col rounded-[20px] transition-all hover:-translate-y-1 hover:shadow-lg"
                        class:body="flex flex-wrap justify-between items-center gap-4"
                        data-price="{{ $extension->price }}"
                        data-installed="{{ $extension->installed }}"
                        data-name="{{ $extension->name }}"
                    >
                        <div class="flex grow items-center gap-7 lg:basis-2/3">
                            <img
                                class="shrink-0"
                                src="{{ $extension->image_url }}"
                                width="53"
                                height="53"
                                alt="{{ $extension->name }}"
                            >
                            <div class="grow">
                                <div class="mb-4 flex flex-wrap gap-4">
                                    <h3 class="m-0 text-xl font-semibold">
                                        {{ $extension->name }}
                                    </h3>
                                    <p class="flex items-center gap-2 text-2xs font-medium">
                                        <span @class([
                                            'size-2 inline-block rounded-full',
                                            'bg-green-500' => $extension->installed,
                                            'bg-foreground/10' => !$extension->installed,
                                        ])></span>
                                        {{ $extension->installed ? __('Installed') : __('Not Installed') }}
                                    </p>
                                </div>
                                <p class="text-base leading-normal">
                                    {{ $extension->description }}
                                </p>
                            </div>
                        </div>

                        <div class="relative z-2">
                            <x-button
                                data-name="{{ $extension->slug }}"
                                @class([
                                    'size-14 btn_installed group',
                                    'hidden' => $extension->installed == 0,
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
                                data-name="{{ $extension->slug }}"
                                @class([
                                    'size-14 btn_install group',
                                    'hidden' => $extension->installed == 1,
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
                            href="{{ route('dashboard.admin.marketplace.extension', ['slug' => $extension->slug]) }}"
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
