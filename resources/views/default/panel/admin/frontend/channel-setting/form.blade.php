@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Edit Channel Section'))
@section('titlebar_actions', '')

@section('settings')
	<form
		class="flex flex-col gap-5"
		id="item_form"
		action="{{ route('dashboard.admin.frontend.channel-setting.update', $item->id) }}"
		enctype="multipart/form-data"
		method="post"
	>
		@csrf
		@method('PUT')
		<x-forms.input
			id="image"
			type="file"
			name="image"
			accept="image/*"
			label="{{ __('Image') }}"
			size="lg"
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