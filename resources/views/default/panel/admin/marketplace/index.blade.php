@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Marketplace'))
@section('titlebar_actions_before')
@php
    $filters = ['All', 'Installed', 'Free', 'Paid'];
@endphp
<div
    x-data="{searchbarHidden: false}"
    class="flex flex-nowrap group"
    :class="searchbarHidden ? 'searchbar-hidden' : ''"
>
    <x-dropdown.dropdown
        class:dropdown-dropdown="max-lg:end-auto max-lg:start-0 max-sm:-left-20"
        anchor="end"
        triggerType="click"
        offsetY="0px"
        >
        <x-slot:trigger
            class="size-9"
            variant="none"
            title="{{ __('Filter') }}"
        >
            <svg class="flex-shrink-0 cursor-pointer" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.58333 9.25V7.83333H8.41667V9.25H5.58333ZM2.75 5.70833V4.29167H11.25V5.70833H2.75ZM0.625 2.16667V0.75H13.375V2.16667H0.625Z"  class="fill-[#0F0F0F] dark:fill-white"/>
            </svg>
        </x-slot:trigger>
        <x-slot:dropdown
            class="min-w-32 text-xs font-medium"
        >
            <ul>
                @foreach ($filters as $filter)
                <li>
                    <x-button
                        data-filter="{{ $filter }}"
                        class="lqd-filter-btn addons_filter flex justify-center w-full items-center gap-2 rounded-md px-3 py-2 text-center transition-colors hover:bg-foreground/5 active:bg-foreground/5 {{ $loop->first ? 'active' : '' }}"
                        tag="button"
                        type="button"
                        name="filter"
                        variant="ghost"
                    >
                        {{ __($filter) }}
                    </x-button>
                </li>
                @endforeach
            </ul>
        </x-slot:dropdown>
    </x-dropdown.dropdown>
</div>
@endsection
@section('titlebar_actions')
    <div class="flex flex-wrap gap-2">
        <x-button variant="ghost-shadow" href="{{ route('dashboard.admin.marketplace.liextension') }}">
            {{ __('Manage Addons') }}
        </x-button>
        <x-button href="{{ route('dashboard.admin.marketplace.index') }}">
            <x-tabler-plus class="size-4" />
            {{ __('Browse Add-ons') }}
        </x-button>
        <x-button class="relative ms-2" variant="ghost-shadow" href="{{ route('dashboard.admin.marketplace.cart') }}">
            <x-tabler-shopping-cart class="size-4" />
            {{ __('Cart') }}
            <small id="itemCount"
                class="bg-red-500 text-white ps-2 pe-2 absolute top-[-10px] right-[3px] rounded-[50%] border border-red-500">{{ count(is_array($cart) ? $cart : []) }}</small>
        </x-button>
    </div>
@endsection

@section('content')
    <div class="py-10">
        <div class="flex flex-col gap-9">
            <!-- @include('panel.admin.market.components.marketplace-filter') -->
            {{--TODO: This banner section should be made in accordance with the design.--}}
			@if(is_array($banners) && $banners)
				<div class="rounded-2xl overflow-hidden" x-data="{
					banners: {{ json_encode($banners) }},
					currentBanner: 0,
				}">

					<a x-bind:href="banners[currentBanner].banner_link" class="bg-gradient-to-r from-purple-200 to-blue-200 flex items-center justify-between">
						<div class="p-9">
							<span class="inline-block px-3 py-1 rounded-full bg-foreground/80 text-background text-xs mb-4" x-text="banners[currentBanner].banner_title"></span>
							<h1 class="text-2xl font-semibold text-background mb-0" x-html="banners[currentBanner].banner_description"></h1>
                            <div class="flex justify-start gap-2 pt-4">
                            <template x-for="(banner, index) in banners" :key="index">
                                <span
                                    :class="currentBanner === index ? 'bg-gray-800 px-2' : 'bg-foreground/80'"
                                    class="w-2 h-2 rounded-full cursor-pointer"
                                    @click="currentBanner = index">
                                </span>
                            </template>
                        </div>
						</div>

						<div>
							<img class="h-32 object-cover" :src="banners[currentBanner].banner_image" alt="Social Media Icon">
						</div>
					</a>
				</div>
			@endif

            <x-alerts.payment-status :payment-status="$paymentStatus" />
            <div class="lqd-extension-grid grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">

                @foreach ($items as $item)
					{{-- TODO: {{ $item['is_featured'] ? 'border-red-500': '' --- If is featured true, a border gradient must be added. --}}
                    <div class="lqd-extension bg-background h-full rounded-[20px] hover:-translate-y-1 {{ $item['is_featured'] ? 'p-[3px] bg-gradient-to-r from-[#82E2F4] via-[#8A8AED] to-[#6977DE]': '' }}"
                        data-price="{{ $item['price'] }}"
                        data-installed="{{ $item['installed'] }}"
                        data-name="{{ $item['name'] }}"
                    >
                        <x-card
                                class="bg-background relative flex flex-col rounded-[17px] transition-all hover:shadow-lg h-full"
                                class:body="flex flex-col"
                        >
                            @if (trim($item['badge'], ' ') != '' || $item['price'] == 0)
                                <p class="absolute end-5 top-5 m-0 rounded bg-[#FFF1DB] px-2 py-1 text-4xs font-semibold uppercase leading-tight tracking-widest text-[#242425]">
                                    @if (trim($item['badge'], ' ') != '')
                                        {{ $item['badge'] }}
                                    @elseif ($item['price'] == 0)
                                        @lang('Free')
                                    @endif
                                </p>
                            @endif

                            @if ($item['version'] != $item['db_version'] && $item['installed'])
                                <p
                                        class="top-{{ $item['price'] == 0 ? '10' : '5' }} absolute end-5 m-0 rounded bg-purple-50 px-2 py-1 text-4xs font-semibold uppercase leading-tight tracking-widest text-[#242425] text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                    <a href="{{ route('dashboard.admin.marketplace.liextension') }}">{{ __('Update Available') }}</a>
                                </p>
                            @endif
                            <div class="size-[53px] mb-6 flex items-center rounded-xl">
                                <img
                                        src="{{ $item['icon'] }}"
                                        width="53"
                                        height="53"
                                        alt="{{ $item['name'] }}"
                                >
                                @if ($item['installed'])
                                    <p class="mb-0 ms-3 flex items-center gap-2 text-2xs font-medium">
                                        <span class="size-2 inline-block rounded-full bg-green-500"></span>
                                        {{ __('Installed') }}
                                    </p>
                                @endif
                            </div>

                            <div class="mb-7 flex flex-wrap items-center gap-2">
                                <h3 class="m-0 text-xl font-semibold">
                                    {{ $item['name'] }}
                                </h3>
                                <p class="review m-0 flex items-center gap-1 text-sm font-medium text-heading-foreground">
                                    <x-tabler-star-filled class="size-3"/>
                                    {{ number_format($item['review'], 1) }}
                                </p>
                            </div>
                            <p class="mb-7 text-base leading-normal">
                                {{ $item['description'] }}
                            </p>
                            <a
                                    class="absolute inset-0 z-1"
                                    href="{{ route('dashboard.admin.marketplace.extension', ['slug' => $item['slug']]) }}"
                            >
                                <span class="sr-only">
                                    {{ __('View details') }}
                                </span>
                            </a>
                            <div class="flex justify-between">
                                @if(! $item['only_show'])
                                    <div class="mt-auto flex flex-wrap items-center gap-2">
                                        @foreach ($item['categories'] as $tag)
                                            {{ $tag }}
                                            @if (!$loop->last)
                                                <span class="size-1 inline-block rounded-full bg-foreground/10"></span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @if((!$item['licensed']) && $item['price'] && $item['is_buy'] && !$item['only_show'])
                                    @if($app_is_not_demo)
                                            @if($item['slug'] === 'chatbot-agent')
                                                @if(\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot'))
                                                    <div
                                                        class="inset-0 z-1"
                                                        data-toogle="cart"
                                                        data-url="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                                                    >
                                                        <a href="#">
                                                            <x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}" class="w-9 h-9 text-{{ in_array($item['id'], $cartExists) ? 'green' : 'gray' }}-500 border rounded p-1"/>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div
                                                        onclick="return toastr.info('External Chatbot is required for this extension.')"
                                                        class="inset-0 z-1"
                                                    >
                                                        <a href="#">
                                                            <x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}" class="w-9 h-9 text-{{ in_array($item['id'], $cartExists) ? 'green' : 'gray' }}-500 border rounded p-1"/>
                                                        </a>
                                                    </div>
                                                @endif

                                            @else
                                                <div
                                                    class="inset-0 z-1"
                                                    data-toogle="cart"
                                                    data-url="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                                                >
                                                    <a href="#">
                                                        <x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}" class="w-9 h-9 text-{{ in_array($item['id'], $cartExists) ? 'green' : 'gray' }}-500 border rounded p-1"/>
                                                    </a>
                                                </div>
                                            @endif

                                    @endif
                                @endif
                            </div>
                        </x-card>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
@endpush
