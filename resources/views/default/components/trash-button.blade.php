@php
    $base_class = 'lqd-fav-btn z-10 size-9';
@endphp

<x-button
    id="fav-btn-{{ $id }}"
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    size="none"
    variant="ghost-shadow"
    @click.prevent="$ajax('{{ $deleteUrl }}', {
		method: 'post',
		body: { _token: '{{ csrf_token() }}', id: {{ $id }} },
		events: true,
	})"
    @ajax:missing="$event.preventDefault()"
    @ajax:after="document.getElementById('lqd-prompt-list').removeChild($el.parentElement)"
    href="#"
    title="{{ __('Delete') }}"
>
    <x-tabler-trash />
</x-button>
