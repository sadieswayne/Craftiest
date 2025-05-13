@extends('panel.layout.settings', ['layout' => 'wide', 'disable_tblr' => true])
@section('title', __($title))
@section('titlebar_actions', '')

@section('additional_css')
	@error('content')
	<style>
		.ace_editor {
			border: 1px solid #dc3545 !important;
		}
	</style>
	@enderror
	<style>
		.ace_editor {
			min-height: 400px;
		}
	</style>
@endsection
@php
	$isPaymentMail = in_array($template?->slug, ['subscription-successful', 'payment-successful'], true);
@endphp
@section('settings')
	<form
		class="flex flex-wrap justify-between gap-y-5"
		id="form-submit"
		action="{{ $action }}"
		enctype="multipart/form-data"
		method="post"
	>
		@csrf
		@method($method)

		@if($isPaymentMail)
			<div class="w-full space-y-2">
				<x-forms.input
					class:container="w-full"
					id="send_payment_confirmation"
					name="send_payment_confirmation"
					type="checkbox"
					:checked="(int) setting('send_payment_confirmation', 1) === 1"
					label="{{ __('Send successful payment/subscription confirmation email') }}"
					tooltip="{{ __('Send successful payment/subscription confirmation email to user.') }}"
					switcher
				/>
				@error('send_payment_confirmation')
				<p class="text-red-500">
					{{ $message }}
				</p>
				@enderror
			</div>
		@endif

		@if ($template->system)
			<input
				id="title"
				type="hidden"
				name="title"
				value="{{ $template != null ? $template->title : null }}"
			/>
		@else
			<div class="w-full space-y-2">
				<x-forms.input
					class:container="w-full"
					id="title"
					type="text"
					name="title"
					size="lg"
					value="{{ old('title', $template != null ? $template->title : null) }}"
					label="{{ __('Title') }}"
					tooltip="{{ __('Template title.') }}"
				/>
				@error('title')
				<p class="text-red-500">
					{{ $message }}
				</p>
				@enderror
			</div>
		@endif

		<div class="w-full space-y-2">
			<x-forms.input
				class:container="w-full"
				id="subject"
				type="text"
				name="subject"
				value="{{ old('title', $template != null ? $template->subject : null) }}"
				size="lg"
				label="{{ __('Subject') }}"
				tooltip="{{ __('Email Subject') }}"
			/>
			@error('title')
			<p class="text-red-500">
				{{ $message }}
			</p>
			@enderror
		</div>

		<div>
			<label class="lqd-input-label mb-3 flex w-full cursor-pointer items-center gap-2 text-2xs font-medium leading-none text-label">
				{{ __('Content') }}
				<x-info-tooltip text="{{ __('You can use HTML. Not: All html elements not competible for mails.') }}"/>
			</label>
			<textarea
				id="content"
				name="content"
			>{{ old('content', $template != null ? $template->content : null) }}</textarea>

			<x-alert class="mt-3">
				<p>
					{{ __('You can use this tags') }}:
					<br>
					<br>
					<strong>
						<code>{site_name}</code>,
						<code>{site_url}</code>,
						<code>{reset_url}</code>,
						<code>{affiliate_url}</code>,
						<code>{user_name}</code>,
						<code>{user_activation_url}</code>
						@if($isPaymentMail)
						, <code>{plan_name}</code>,
						<code>{plan_price}</code>
						@endif
					</strong>
				</p>
			</x-alert>

			<textarea
				id="content_ace"
				name="content_ace"
				hidden=""
			></textarea>
			@error('content')
			<p class="text-red-500">
				{{ $message }}
			</p>
			@enderror
		</div>

		<x-button
			class="w-full"
			id="email_templates_button"
			size="lg"
			type="submit"
		>
			{{ __('Save') }}
		</x-button>
	</form>
@endsection

@push('script')
	<script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"></script>
	<script>
		var email_template_content = ace.edit("content");
		email_template_content.session.setMode("ace/mode/html");

		$('#form-submit').submit(function (event) {
			$('#content_ace').val(email_template_content.getValue());
		});
	</script>
@endpush
