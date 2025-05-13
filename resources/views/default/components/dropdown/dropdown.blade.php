@php
    $base_class = 'lqd-dropdown flex relative group/dropdown [--dropdown-offset:0px]';
    $trigger_base_class = 'lqd-dropdown-trigger hover:translate-y-0
	before:absolute before:-inset-3 before:pointer-events-none
	focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
	group-[&.lqd-is-active]/dropdown:before:pointer-events-auto';
    $dropdown_base_class = 'lqd-dropdown-dropdown absolute top-full opacity-0 invisible z-50 translate-y-1 pointer-events-none transition-all mt-[--dropdown-offset]
		before:absolute before:bottom-full before:-top-[--dropdown-offset] before:inset-x-0
		group-[&.lqd-is-active]/dropdown:opacity-100 group-[&.lqd-is-active]/dropdown:visible group-[&.lqd-is-active]/dropdown:translate-y-0 group-[&.lqd-is-active]/dropdown:pointer-events-auto
		[&.dropdown-anchor-bottom]:top-auto [&.dropdown-anchor-bottom]:bottom-full [&.dropdown-anchor-bottom]:mt-0 [&.dropdown-anchor-bottom]:mb-[--dropdown-offset] [&.dropdown-anchor-bottom]:before:bottom-full [&.dropdown-anchor-bottom]:before:-top-[--dropdown-offset]';
    $dropdown_content_base_class =
        'lqd-dropdown-dropdown-content min-w-44 border border-dropdown-border rounded-dropdown bg-dropdown-background text-dropdown-foreground shadow-lg shadow-black/5';

    if ($anchor === 'start') {
        $dropdown_base_class .= ' start-0';
    } else {
        $dropdown_base_class .= ' end-0';
    }
@endphp

<div
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    @style([
        '--dropdown-offset: ' . $offsetY . '' => !empty($offsetY),
    ])
    x-data="dropdown({ triggerType: '{{ $triggerType }}' })"
    x-bind="parent"
    x-ref="parent"
>
    <x-button
        {{ $attributes->twMergeFor('trigger', $trigger_base_class, $trigger->attributes->get('class')) }}
        variant="{{ $trigger->attributes->get('variant') ? $trigger->attributes->get('variant') : 'link' }}"
        x-bind="trigger"
    >
        {{ $trigger }}
    </x-button>

    <div
        {{ $attributes->twMergeFor('dropdown-dropdown', $dropdown_base_class) }}
        x-bind="dropdown"
        x-init="$refs.parent.addEventListener('mouseenter', function() {
            const parentRect = $refs.parent.getBoundingClientRect();
            const dropdownRect = $el.getBoundingClientRect();
            $el.classList.toggle('dropdown-anchor-bottom', parentRect.bottom + dropdownRect.height > window.innerHeight && parentRect.top - dropdownRect.height > 0);
        })"
    >
        <div {{ $attributes->twMergeFor('dropdown', $dropdown_content_base_class, $dropdown->attributes->get('class')) }}>
            {{ $dropdown }}
        </div>
    </div>
</div>
