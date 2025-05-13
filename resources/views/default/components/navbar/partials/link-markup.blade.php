@php
    $base_class = 'lqd-navbar-link flex items-center gap-2 ps-navbar-link-ps pe-navbar-link-pe pt-navbar-link-pt pb-navbar-link-pb rounded-xl relative transition-colors group/link
		hover:bg-navbar-background-hover/5 hover:text-navbar-foreground-hover
		[&.active]:bg-navbar-background-active/5 [&.active]:text-navbar-foreground-active
		dark:[&.active]:bg-transparent
		dark:before:w-1.5 dark:before:h-full dark:before:absolute dark:before:top-0 dark:before:-start-2 dark:before:bg-primary dark:before:rounded-e-lg dark:before:opacity-0
		dark:[&.active]:before:opacity-100';
    $label_base_class = 'lqd-nav-link-label flex grow gap-2 items-center transition-[opacity,transform,visbility] [&_.lqd-nav-item-badge]:ms-auto';
    $letter_icon_base_class = 'lqd-nav-link-letter-icon inline-flex size-6 shrink-0 items-center justify-center rounded-md bg-primary text-4xs text-primary-foreground';

    $target = '_self';

    // setting href
    if (!empty($href) && $href !== '#') {
        if (is_string($href) && Route::has($href)) {
            $href = !empty($slug) ? route($href, $slug) : route($href);
        } else {
            $target = '_blank';
        }

    }

    // if (empty(trim($activeCondition)) && !empty($href)) {
    //     $activeCondition = $href === url()->current();
    // }
    if ($activeCondition) {
        $base_class .= ' active';
    }
@endphp

<a
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    href="{{ $href }}"
    target="{{ $target }}"
    @if ($dropdownTrigger) @click.prevent="toggleDropdownOpen()" @endif
    @if ($app_is_not_demo && ($activeCondition && !empty(trim($activeCondition)))) x-init="$el.parentElement.offsetTop > window.innerHeight && $el.closest('.lqd-navbar-inner').scrollTo({ top: (($el.parentElement.offsetHeight + $el.parentElement.offsetTop) / 2) })" @endif
    @if ($triggerType === 'modal') @click.prevent="toggleModal()" @endif
>
    @if ($letterIcon && !empty($label))
        <span
            {{ $attributes->twMergeFor('letter-icon', $letter_icon_base_class) }}
            @if (!empty($letterIconStyles)) style="{{ $letterIconStyles }}" @endif
        >
            {{ mb_substr($label, 0, 1) }}
        </span>
    @endif
    @if (!empty($icon) || !empty($iconHtml))
        <span
            class="lqd-nav-link-icon bg-navbar-icon-background text-navbar-icon-foreground group-hover/link:bg-navbar-icon-background-hover group-hover/link:text-navbar-icon-foreground-hover group-[&.active]/link:bg-navbar-icon-background-active group-[&.active]/link:text-navbar-icon-foreground-active"
        >
            @if (!empty($iconHtml))
                {!! $iconHtml !!}
            @else
                <x-dynamic-component
                    class="size-navbar-icon"
                    stroke-width="1.5"
                    :component="$icon"
                />
            @endif
        </span>
    @endif

    @if (!empty($label))
        <span {{ $attributes->twMergeFor('label', $label_base_class, $attributes->get('class:label')) }}>
            {{ $label }}
        </span>
    @endif

    @if ($dropdownTrigger)
        <span class="lqd-nav-link-expander  shrink-0 group-[&.navbar-shrinked]/body:hidden">
            <x-tabler-plus
                class="w-3"
                stroke-width="2.5"
            />
        </span>
    @endif

   @if ($new && $app_is_demo)
        <x-badge
            class=" rounded-md text-4xs group-[&.navbar-shrinked]/body:hidden"
            variant="secondary"
        >
            @if (!empty($badge))
                {{ mb_strtoupper($badge) }}
			@else
				{{ __('NEW') }}
            @endif
        </x-badge>
    @endif
</a>
