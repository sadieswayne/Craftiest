@php
    $base_class .= ' flex gap-4 p-4 text-xs last:border-none';
	$isImage = $entry->generator->type === 'image';
@endphp

<a
    data-type="{{ trim($entry->generator->type) }}"
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    href="{{  (route('dashboard.user.openai.documents.single', $entry->slug)) }}"
>
    <x-lqd-icon
        class="lqd-posts-item-icon lqd-docs-item-icon"
        size="lg"
        style="background: {{ $entry->generator->color }}"
    >
        <span class="flex size-5">
            @if ($entry->generator->image !== 'none')
                {!! html_entity_decode($entry->generator->image) !!}
            @endif
        </span>
    </x-lqd-icon>
    <span class="block w-0 max-w-full grow overflow-hidden">
        <span class="lqd-posts-item-title lqd-docs-item-title block text-sm font-medium">
            {{ $isImage ? str()->limit($entry->input, 25) : __($entry->generator->title) }}
        </span>
        <span class="lqd-posts-item-desc lqd-docs-item-desc block w-full overflow-hidden overflow-ellipsis whitespace-nowrap italic opacity-45">
            {{ str()->words(__($entry->generator->description), 30) }}
        </span>
    </span>
    <span class="flex flex-col whitespace-nowrap">
        {{ __('in Workbook') }}
        <span class="lqd-posts-item-date lqd-docs-item-date italic opacity-45">
            {{ $entry->created_at->format('M d, Y') }}
        </span>
    </span>
</a>
