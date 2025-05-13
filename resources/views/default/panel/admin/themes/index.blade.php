@php
    $filters = ['All', 'Frontend', 'Dashboard'];
@endphp

@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Themes and skins'))
@section('titlebar_actions', '')

@section('settings')
    <div x-data="{ 'activeFilter': 'All' }">
        <h2 class="mb-4">
            @lang('Available Themes')
        </h2>
        <p class="mb-8">
            @lang('Customize the visual appearence of MagicAI with a single click and complement the design principles of your brand identity. ')
        </p>
        <x-alerts.payment-status :payment-status="$paymentStatus" />
        <div class="flex flex-col gap-16">
            <ul class="flex w-full justify-between gap-3 rounded-full bg-foreground/10 p-1 text-xs font-medium">
                @foreach ($filters as $filter)
                    <li>
                        <button
                            @class([
                                'px-6 py-3 leading-tight rounded-full transition-all hover:bg-background/80 [&.lqd-is-active]:bg-background [&.lqd-is-active]:shadow-[0_2px_12px_hsl(0_0%_0%/10%)]',
                                'lqd-is-active' => $loop->first,
                            ])
                            @click="activeFilter = '{{ $filter }}'"
                            :class="{ 'lqd-is-active': activeFilter == '{{ $filter }}' }"
                        >
                            @lang($filter)
                        </button>
                    </li>
                @endforeach
            </ul>
            {{-- data --}}
            @foreach ($items ?? [] as $theme)
                <x-card
                    class="group mt-4"
                    data-cat="{{ $theme['theme_type'] == 'All' ? 'Frontend, Dashboard' : $theme['theme_type'] }}"
                    size="none"
                    variant="shadow"
                    ::class="{ 'hidden': !$el.getAttribute('data-cat')?.includes(activeFilter) && activeFilter !== 'All' }"
                >
                    <figure class="mb-30 relative overflow-hidden">
                        @if ($theme['version'] != $theme['db_version'] && $theme['slug'] != 'default' && $theme['installed'])
                            <p
                                class="absolute end-5 top-5 m-0 rounded bg-purple-50 px-2 py-1 text-4xs font-semibold uppercase leading-tight tracking-widest text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                <a href="{{ route('dashboard.admin.themes.activate', ['slug' => $theme['slug']]) }}">Update Available</a>
                            </p>
                        @endif

                        <img
                            class="h-auto w-full"
                            src="{{ $theme['icon'] }}"
                            alt="{{ $theme['name'] }}"
                            width="490"
                            height="320"
                        >
                        <a
                            class="absolute inset-0 flex scale-110 items-center justify-center bg-foreground/40 text-background opacity-0 backdrop-blur-sm transition-all group-hover:scale-100 group-hover:opacity-100"
                            href="https://{{ $theme['slug'] == 'default' ? 'magicai.liquid-themes.com' : $theme['slug'] . '.projecthub.ai' }}"
                            target="_blank"
                        >
                            <x-tabler-zoom-in class="size-10" />
                            <span>
                                @lang('Live Preview')
                            </span>
                        </a>
                    </figure>
                    <div class="p-8">
                        <p class="mb-3 flex items-center gap-1.5">
                            @if ($theme['price'] > 0)
                                <span @class([
                                    'size-2 inline-block rounded-full',
                                    'bg-green-600' => false, // Free themes
                                    'bg-primary' => true, // Premium themes
                                ])></span>
                                @lang('Premium Theme')
                            @else
                                <span @class([
                                    'size-2 inline-block rounded-full',
                                    'bg-green-600' => true, // Free themes
                                    'bg-primary' => false, // Premium themes
                                ])></span>
                                @lang('Free Theme')
                            @endif
                        </p>
                        <h3 class="mb-3">
                            @lang($theme['name'])
                        </h3>
                        <p class="mb-5">
                            @lang($theme['description'])
                        </p>

                        @php
                            if ($theme['slug'] == 'default') {
                                $is_active = setting('front_theme') == 'default' && setting('dash_theme') == 'default';
                            } else {
                                $is_active = setting('front_theme') == $theme['slug'] || setting('dash_theme') == $theme['slug'];
                            }

                            $link = !$theme['licensed']
                                ? route('dashboard.admin.themes.buyTheme', ['slug' => $theme['slug']])
                                : route('dashboard.admin.themes.activate', ['slug' => $theme['slug']]);

                            if ($is_active) {
                                if ($theme['version'] != $theme['db_version'] && $theme['slug'] != 'default') {
                                    $is_active = false;

                                    $text = trans('Update');
                                } else {
                                    $text = trans('Activated');
                                }
                            } else {
                                if ($theme['licensed']) {
                                    $text = trans('Activate');
                                } else {
                                    $text = trans('Buy now');
                                }
                            }

                        @endphp

                        <x-button
                            class="w-full"
                            data-theme="{{ $theme['slug'] }}"
                            :disabled="$is_active"
                            variant="{{ $theme['price'] == 0 ? 'success' : 'primary' }}"
                            size="lg"
                            href="{{ $link }}"
                        >
                            {{ $text }}
                        </x-button>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/themes.js') }}"></script>
@endpush
