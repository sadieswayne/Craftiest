@php
    $base_class = 'lqd-fav-btn z-10 size-9';
@endphp

<x-button
    id="fav-btn-{{ $id }}"
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    size="none"
    variant="ghost-shadow"
    x-data="{ isFavorite: {{ $isFavorite ? 'true' : 'false' }} }"
    @click.prevent="$ajax('{{ $updateUrl }}', {
		method: 'post',
		body: { _token: '{{ csrf_token() }}', id: {{ $id }} },
		events: true,
	})"
    @ajax:missing="$event.preventDefault()"
    @ajax:after="isFavorite = !isFavorite; $dispatch('favorite-toggled', { id: {{ $id }}, isFavorite: isFavorite })"
    href="#"
    title="{{ __('Favorite') }}"
>
    <x-favorite-icon
        is-favorited="{{ $isFavorite }}"
        x-bind:data-is-active="isFavorite"
    />
</x-button>
