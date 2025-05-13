@extends('panel.layout.settings', ['disable_tblr' => true, 'layout' => 'fullwidth'])
@section('title', __('Edit Curtain Section'))
@section('titlebar_actions', '')

@section('settings')
	<form
		class="flex flex-col gap-5"
		id="item_form"
		action="{{ route('dashboard.admin.frontend.curtain.update', $item->id) }}"
		enctype="multipart/form-data"
		method="post"
	>
		@csrf
		@method('PUT')

		<x-forms.input
			id="title"
			label="{{ __('Title') }}"
			name="title"
			size="lg"
			required
			value="{{ $item->title }}"
		/>

		<x-forms.input
			tooltip="{{ __('Use html tag') }}"
			id="title_icon"
			label="{{ __('Title icon') }}"
			name="title_icon"
			size="lg"
			value="{{ $item->title_icon }}"
		/>


		<hr>
		@php
			$empty = [
				'title'             => '',
				'description'       => '',
				'bg_image'          => '',
				'bg_video'          => '',
				'bg_color'          => '',
				'title_color'       => '',
				'description_color' => '',
			];

			$sliders = $item['sliders'] ?? [$empty, $empty, $empty];
		@endphp

		@foreach($sliders as $key => $slider)
			<x-card>
				<div class="gap-2">
{{--					<x-forms.input--}}
{{--						id="sliders[{{ $key }}][title]"--}}
{{--						label="{{ __('Title') }}"--}}
{{--						name="sliders[{{ $key }}][title]"--}}
{{--						size="lg"--}}
{{--						value="{{ $slider['title'] ?? '' }}"--}}
{{--					/>--}}
					<x-forms.input
						class="mt-2"
						type="textarea"
						id="sliders[{{ $key }}][description]"
						label="{{ __('Description') }}"
						name="sliders[{{ $key }}][description]"
						size="lg"
						value="{{ $slider['description'] ?? '' }}"
					/>
				</div>

				<div class="flex mt-3 gap-2 grid grid-cols-1 lg:grid-cols-2">
					<x-forms.input
						type="file"
						id="sliders[{{ $key }}][bg_image]"
						label="{{ __('Background Image') }}"
						name="sliders[{{ $key }}][bg_image]"
						size="lg"
						value="{{ $slider['bg_image'] ?? '' }}"
					>
						@if(isset($slider['bg_image']) && $slider['bg_image'])
							<x-slot name="labelExtra">
								<a href="{{ $slider['bg_image'] }}" class="text-red-700">Image Download</a>
							</x-slot>
						@endif
					</x-forms.input>
					<x-forms.input
						type="file"
						id="sliders[{{ $key }}][bg_video]"
						label="{{ __('Background Video') }}"
						name="sliders[{{ $key }}][bg_video]"
						size="lg"
						value="{{ $slider['bg_video'] ?? '' }}"
					>
						@if(isset($slider['bg_video']) && $slider['bg_video'])
							<x-slot name="labelExtra">
								<a href="{{ $slider['bg_video'] }}" class="text-red-700">Video Download</a>
							</x-slot>
						@endif
					</x-forms.input>
				</div>

				<div class="flex mt-3 gap-2 grid grid-cols-1 lg:grid-cols-3">
					<x-forms.input
						type="color"
						id="sliders[{{ $key }}][bg_color]"
						label="{{ __('Background Color') }}"
						name="sliders[{{ $key }}][bg_color]"
						size="lg"
						value="{{ $slider['bg_color'] ?? '' }}"
					/>
					<x-forms.input
						type="color"

						id="sliders[{{ $key }}][title_color]"
						label="{{ __('Title Color') }}"
						name="sliders[{{ $key }}][title_color]"
						size="lg"
						value="{{ $slider['title_color'] ?? '' }}"
					/>
					<x-forms.input
						type="color"
						id="sliders[{{ $key }}][description_color]"
						label="{{ __('Description Color') }}"
						name="sliders[{{ $key }}][description_color]"
						size="lg"
						value="{{ $slider['description_color'] ?? '' }}"
					/>
				</div>
			</x-card>
		@endforeach

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
