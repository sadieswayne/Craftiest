@props([
    'items' => [],
])

@php
    $nav_btn_base_class =
        'relative inline-grid size-10 place-items-center rounded-full bg-primary-foreground/5 text-primary-foreground transition-all hover:scale-110 hover:bg-primary-foreground hover:text-primary';
    $nav_dot_base_class =
        ' hover:scale-110inline-flex size-2.5 rounded-full bg-primary-foreground/5 transition-all hover:scale-110 hover:bg-primary-foreground/70 [&.active]:w-[18px] [&.active]:bg-primary-foreground';
    $unique_id = uniqid('curtain');
@endphp

<div
    {{ $attributes->withoutTwMergeClasses()->twMerge('lqd-curtain flex flex-col justify-between gap-5 lg:flex-row') }}
    style="--items-count: {{ count($items) }}"
    x-data="curtain('{{ $unique_id }}')"
>
    @foreach ($items as $item)
        @php
            $firstSlide = $item['sliders'][0] ?? null;
            $firstSlideBgColor = isset($firstSlide['bg_color']) && filled($firstSlide['bg_color']) ? $firstSlide['bg_color'] : 'hsl(var(--primary))';
        @endphp
        <div
            {{ $attributes->twMergeFor('item', ['lqd-curtain-item relative overflow-hidden flex flex-col rounded-xl lg:flex-row', $loop->first ? 'lqd-curtain-item-active' : 'lqd-curtain-item-inactive']) }}
            @if (count($item['sliders']) > 1) x-data="slideshow('{{ $unique_id }}',{{ count($item['sliders']) }})" @endif
            @if ($firstSlideBgColor) style="background: {{ $firstSlideBgColor }}" @endif
        >
            <h3
                {{ $attributes->twMergeFor('title', ['lqd-curtain-item-title relative z-1 m-0 flex overflow-hidden px-8 py-8 text-[22px] text-white lg:order-3 lg:px-11 lg:py-9 xl:px-14', !isset($item['title']) || !filled($item['title']) ?? 'lqd-curtain-item-title-empty']) }}>
                <span class="lqd-curtain-item-title-inner flex grow items-center lg:rotate-180 lg:[writing-mode:vertical-rl]">
                    @if (isset($item['title_icon']) && filled($item['title_icon']))
                        <span class="lqd-curtain-item-title-icon inline-flex lg:rotate-90">
                            {!! $item['title_icon'] !!}
                        </span>
                    @endif
                    @if (isset($item['title']) && filled($item['title']))
                        {!! $item['title'] !!}
                    @endif
                </span>
            </h3>

            @if (isset($item['sliders']) && filled($item['sliders']))
                <div {{ $attributes->twMergeFor('content', 'lqd-curtain-item-content flex items-end') }}>
                    <div {{ $attributes->twMergeFor('content-inner', 'lqd-curtain-item-content-inner grow p-6 lg:p-11') }}>
                        <div class="lqd-curtain-item-content-width-outer">
                            <div class="lqd-curtain-item-content-width-inner">
                                <div class="lqd-curtain-item-content-wrap grid">
                                    @foreach ($item['sliders'] as $slide)
                                        @php
                                            $bg_color = isset($slide['bg_color']) && filled($slide['bg_color']) ? $slide['bg_color'] : 'hsl(var(--primary))';
                                            $bg_image = isset($slide['bg_image']) && filled($slide['bg_image']) ? $slide['bg_image'] : '';
                                            $bg_video = isset($slide['bg_video']) && filled($slide['bg_video']) ? $slide['bg_video'] : '';
                                            $title_color = isset($slide['title_color']) && filled($slide['title_color']) ? $slide['title_color'] : 'hsl(var(--primary-foreground))';
                                            $description_color =
                                                isset($slide['description_color']) && filled($slide['description_color'])
                                                    ? $slide['description_color']
                                                    : 'hsl(var(--primary-foreground) / 60%)';
                                        @endphp

                                        <div
                                            {{ $attributes->twMergeFor('bg', 'lqd-curtain-item-bg transition-all duration-500 ease-in-out pointer-events-none absolute inset-0 z-0 rounded-[inherit]') }}
                                            style="background: {{ $bg_color }}"
                                            @if (!$loop->first) x-cloak @endif
                                            @if (count($item['sliders']) > 1) x-show="$data.activeSlide === {{ $loop->index }}"
                                            x-transition:enter-start="opacity-0 scale-110 blur-3xl"
                                            x-transition:enter-end="opacity-100 scale-100 blur-0"
                                            x-transition:leave-start="opacity-100 scale-100 blur-0"
                                            x-transition:leave-end="opacity-0 scale-110 blur-3xl" @endif
                                        >
                                            @if (isset($slide['bg_video']) && filled($slide['bg_video']))
                                                <video
                                                    class="scale-115 absolute left-0 top-0 h-full w-full object-cover object-center"
                                                    data-src="{{ $slide['bg_video'] }}"
                                                    :class="{
                                                        'scale-115': $data.activeCurtain !== {{ $loop->parent->index }},
                                                        'scale-100': $data.activeCurtain === {{ $loop->parent->index }}
                                                    }"
                                                    playsinline
                                                    loop
                                                    muted
                                                    autoplay
                                                    x-intersect.margin.350px="if ({{ $loop->index }} === 0 && !$el.src) {
                                                        $el.src = $el.getAttribute('data-src');
                                                        $el.load();
													}
													if ( {{ $loop->parent->index }} === 0 ) {
														$el.play();
													} else {
														$el.pause();
													}"
                                                    x-intersect:leave="$el.src && $el.readyState >= 2 && $el.pause()"
                                                    x-intersect:enter="if ($el.src && {{ $loop->index }} === $data.activeSlide && {{ $loop->parent->index }} === $data.activeCurtain && $el.readyState >= 2) { $el.play(); }"
                                                    @slide-changed-{{ $unique_id }}.window="if ({{ $loop->index }} === $data.activeSlide && {{ $loop->parent->index }} === $data.activeCurtain) { if(!$el.src) {$el.src = $el.getAttribute('data-src'); $el.load()} $el.play(); } else { $el.pause(); }"
                                                    @curtain-changed-{{ $unique_id }}.window="if (($data.activeSlide == null || {{ $loop->index }} === $data.activeSlide) && {{ $loop->parent->index }} === $data.activeCurtain) { if(!$el.src) {$el.src = $el.getAttribute('data-src'); $el.load()} $el.play(); } else { $el.pause(); }"
                                                ></video>
                                            @endif
                                            @if (isset($slide['bg_image']) && filled($slide['bg_image']))
                                                <img
                                                    class="scale-115 absolute left-0 top-0 h-full w-full object-cover object-center"
                                                    data-src="{{ $slide['bg_image'] }}"
                                                    :class="{
                                                        'scale-115': $data.activeCurtain !== {{ $loop->parent->index }},
                                                        'scale-100': $data.activeCurtain === {{ $loop->parent->index }}
                                                    }"
                                                    x-intersect.margin.350px="if ({{ $loop->index }} === 0 && !$el.src) {
														$el.src = $el.getAttribute('data-src');
													}"
                                                    @slide-changed-{{ $unique_id }}.window="if ({{ $loop->index }} === $data.activeSlide && {{ $loop->parent->index }} === $data.activeCurtain) { if(!$el.src) {$el.src = $el.getAttribute('data-src');} }"
                                                    @curtain-changed-{{ $unique_id }}.window="if (($data.activeSlide == null || {{ $loop->index }} === $data.activeSlide) && {{ $loop->parent->index }} === $data.activeCurtain) { if(!$el.src) {$el.src = $el.getAttribute('data-src');} }"
                                                >
                                            @endif

                                            <div
                                                class="absolute inset-0 backdrop-blur-md transition-all"
                                                :class="{
                                                    'backdrop-blur-0': $data.activeCurtain === {{ $loop->parent->index }},
                                                    'backdrop-blur-md': $data.activeCurtain !== {{ $loop->parent->index }}
                                                }"
                                                style="background: linear-gradient(to top, {{ $bg_color }}, transparent 60%)"
                                            ></div>
                                        </div>

                                        @if (isset($slide['title']) && filled($slide['title']))
                                            <h4
                                                class="lqd-curtain-item-content-title relative z-1 col-span-full col-start-1 row-start-1 row-end-1 mb-3 text-[24px] transition-all duration-300 ease-in-out"
                                                style="color: {{ $title_color }}"
                                                @if (!$loop->first) x-cloak @endif
                                                @if (count($item['sliders']) > 1) x-show="$data.activeSlide === {{ $loop->index }}""
                                                x-transition:enter="delay-100"
                                                x-transition:enter-start="opacity-0 -translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 translate-y-2" @endif
                                            >
                                                {!! $slide['title'] !!}
                                            </h4>
                                        @endif
                                        @if (isset($slide['description']) && filled($slide['description']))
                                            <p
                                                class="lqd-curtain-item-content-description relative z-1 col-span-full col-start-1 row-start-2 row-end-2 transition-all duration-300 ease-in-out"
                                                style="color: {{ $description_color }}"
                                                @if (!$loop->first) x-cloak @endif
                                                @if (count($item['sliders']) > 1) x-show="$data.activeSlide === {{ $loop->index }}""
                                                x-transition:enter="delay-200"
                                                x-transition:enter-start="opacity-0 -translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 translate-y-2" @endif
                                            >
                                                {!! $slide['description'] !!}
                                            </p>
                                        @endif
                                    @endforeach

                                    @if (count($item['sliders']) > 1)
                                        <div class="lqd-curtain-item-nav relative z-2 row-start-3 mt-5 flex items-center gap-3">
                                            <button
                                                type="button"
                                                {{ $attributes->twMergeFor('slideshow-btn', $nav_btn_base_class, 'lqd-curtain-item-nav-prev') }}
                                                @click.prevent="$data.setActiveSlide('<')"
                                            >
                                                <x-tabler-chevron-left class="size-4" />
                                            </button>
                                            <div class="lqd-curtain-item-dots contents">
                                                @foreach ($item['sliders'] as $slide)
                                                    <button
                                                        type="button"
                                                        {{ $attributes->twMergeFor('slideshow-dot', $nav_dot_base_class, 'lqd-curtain-item-dot', $loop->first ? 'active' : '') }}
                                                        @click.prevent="$data.setActiveSlide({{ $loop->index }})"
                                                        :class="{ 'active': $data.activeSlide === {{ $loop->index }} }"
                                                    ></button>
                                                @endforeach
                                            </div>
                                            <button
                                                type="button"
                                                {{ $attributes->twMergeFor('slideshow-btn', $nav_btn_base_class, 'lqd-curtain-item-nav-next') }}
                                                @click.prevent="$data.setActiveSlide('>')"
                                            >
                                                <x-tabler-chevron-right class="size-4" />
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
