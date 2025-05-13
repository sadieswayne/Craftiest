@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Edit Content Box Section'))
@section('titlebar_actions', '')

@section('settings')
	<form
		class="flex flex-col gap-5"
		id="item_form"
		action="{{ route('dashboard.admin.frontend.content-box.update', $item->id) }}"
		enctype="multipart/form-data"
		method="post"
	>
		@csrf
		@method('PUT')
		<x-forms.input
			id="emoji"
			label="{{ __('Emoji') }}"
			name="emoji"
			size="lg"
			required
			value="{{ $item->emoji }}"
		/>

		<x-forms.input
			id="title"
			label="{{ __('Title') }}"
			name="title"
			size="lg"
			required
			value="{{ $item->title }}"
		/>

		<x-forms.input
			id="description"
			name="description"
			type="textarea"
			label="{{ __('Description') }}"
			rows="10"
			required
		>{{ $item->description }}</x-forms.input>


		<x-forms.input
			type="color"
			id="background"
			label="{{ __('Background') }}"
			name="background"
			size="lg"
			required
			value="{{ $item->background }}"
		/>

		<x-forms.input
			type="color"
			id="foreground"
			label="{{ __('Foreground') }}"
			name="foreground"
			size="lg"
			required
			value="{{ $item->foreground }}"
		/>
		<x-button
			id="item_button"
			size="lg"
			type="submit"
		>
			{{ __('Save') }}
		</x-button>
	</form>
@endsection

@push('script')
@endpush