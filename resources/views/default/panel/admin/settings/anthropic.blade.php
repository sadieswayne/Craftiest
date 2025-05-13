@php
    use App\Domains\Entity\Enums\EntityEnum;
@endphp

@extends('panel.layout.settings', ['layout' => 'wide'])
@section('title', __(\App\Domains\Engine\Enums\EngineEnum::ANTHROPIC->label().' Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('This API key is used for all AI-powered features, including AI Chat, Image Generation, and Content Writing'))

@section('additional_css')
    <link
            href="{{ custom_theme_url('/assets/libs/select2/select2.min.css') }}"
            rel="stylesheet"
    />
    <style>

    </style>
@endsection

@section('settings')
<form
		id="settings_form"
		onsubmit="return anthropicSettingsSave();"
		enctype="multipart/form-data"
>
	<h3 class="mb-[25px] text-[20px]">{{ __('Anthropic Settings') }}</h3>
	<div class="row">
		<!-- TODO OPENAI API KEY -->
		@if ($app_is_demo)
			<div class="col-md-12">
				<div class="mb-3">
					<label class="form-label">{{ __('Anthropic API Secret') }}</label>
					<input
							class="form-control"
							id="anthropic_api_secret"
							type="text"
							name="anthropic_api_secret"
							value="*********************"
					>
				</div>
			</div>
		@else
			<div class="col-md-12">
				<div
						class="form-control mb-3 border-none p-0 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em]">
					<label class="form-label">{{ __('Anthropic API Secret') }}</label>

					<select
							class="form-control select2"
							id="anthropic_api_secret"
							name="anthropic_api_secret"
							multiple
					>
						@foreach (explode(',', setting('anthropic_api_secret')) as $secret)
							<option
									value="{{ $secret }}"
									selected
							>{{ $secret }}</option>
						@endforeach
					</select>

					<x-alert class="mt-2">
						<p>
							{{ __('You can enter as much API KEY as you want. Click "Enter" after each api key.') }}
						</p>
					</x-alert>
					<x-alert class="mt-2">
						<p>
							{{ __('Please ensure that your Anthropic API key is fully functional and billing defined on your Anthropic account.') }}
						</p>
					</x-alert>
					<a
							class="btn btn-primary mb-2 mt-2 w-full"
							href="{{ route('dashboard.admin.settings.anthropic.test') }}"
							target="_blank"
					>
						{{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
					</a>
				</div>
			</div>
		@endif

		<div class="col-md-12">
			<div class="mb-3">
				@php
					$anthropicDrivers = \App\Domains\Entity\EntityStats::word()
						->filterByEngine(\App\Domains\Engine\Enums\EngineEnum::ANTHROPIC)
						->list();
					$current_anthropic_model = EntityEnum::fromSlug(setting('anthropic_default_model', EntityEnum::CLAUDE_2_0->slug()))->slug();
				@endphp
				<x-model-select-list-with-change-alert :bedrockOptions="true" :listLabel="'Default Anthropic Word Model'" :listId="'anthropic_default_model'" currentModel="{{ $current_anthropic_model }}" :drivers="$anthropicDrivers" />
		</div>

		<div class="col-md-12" id="anthropic_bedrock" style="display: none;">
			<div class="mb-3">
				<label class="form-label">{{ __('Default AWS Bedrock Model') }}
					<x-info-tooltip text="{{ __('To use Bedrock, you must first configure your AWS settings.') }}" />
				</label>
				<select
						class="form-select"
						id="anthropic_bedrock_model"
						name="anthropic_bedrock_model"
				>
					<option
							value="{{\App\Enums\BedrockEngine::CLAUDE_1->value}}"
							{{ setting('anthropic_bedrock_model', \App\Enums\BedrockEngine::CLAUDE_1->value) == \App\Enums\BedrockEngine::CLAUDE_1->value ? 'selected' : null }}
					>
						{{ \App\Enums\BedrockEngine::CLAUDE_1->label() }}
					</option>
					<option
							value="{{\App\Enums\BedrockEngine::CLAUDE_2->value}}"
							{{ setting('anthropic_bedrock_model', \App\Enums\BedrockEngine::CLAUDE_2->value) == \App\Enums\BedrockEngine::CLAUDE_2->value ? 'selected' : null }}
					>
						{{ \App\Enums\BedrockEngine::CLAUDE_2->label() }}
					</option>
					<option
							value="{{\App\Enums\BedrockEngine::CLAUDE_21->value}}"
							{{ setting('anthropic_bedrock_model', \App\Enums\BedrockEngine::CLAUDE_21->value) == \App\Enums\BedrockEngine::CLAUDE_21->value ? 'selected' : null }}
					>
						{{ \App\Enums\BedrockEngine::CLAUDE_21->label() }}
					</option>
					<option
							value="{{\App\Enums\BedrockEngine::CLAUDE_3_HAIKU->value}}"
							{{ setting('anthropic_bedrock_model', \App\Enums\BedrockEngine::CLAUDE_3_HAIKU->value) == \App\Enums\BedrockEngine::CLAUDE_3_HAIKU->value ? 'selected' : null }}
					>
						{{ \App\Enums\BedrockEngine::CLAUDE_3_HAIKU->label() }}
					</option>
					<option
							value="{{\App\Enums\BedrockEngine::CLAUDE_3_SONNET->value}}"
							{{ setting('anthropic_bedrock_model', \App\Enums\BedrockEngine::CLAUDE_3_SONNET->value) == \App\Enums\BedrockEngine::CLAUDE_3_SONNET->value ? 'selected' : null }}
					>
						{{ \App\Enums\BedrockEngine::CLAUDE_3_SONNET->label() }}
					</option>
				</select>
			</div>
		</div>

		{{--            <div class="col-md-6"> --}}
		{{--                <div class="mb-3"> --}}
		{{--                    <label class="form-label">{{ __('Maximum Input Length') }}</label> --}}
		{{--                    <input --}}
		{{--                        class="form-control" --}}
		{{--                        id="anthropic_max_input_length" --}}
		{{--                        type="number" --}}
		{{--                        name="anthropic_max_input_length" --}}
		{{--                        min="10" --}}
		{{--                        max="2000" --}}
		{{--                        value="{{ setting('anthropic_max_input_length', 200) }}" --}}
		{{--                        required --}}
		{{--                    > --}}
		{{--                    <x-alert class="mt-2"> --}}
		{{--                        <p> --}}
		{{--                            {{ __('In Characters') }} --}}
		{{--                        </p> --}}
		{{--                    </x-alert> --}}
		{{--                </div> --}}
		{{--            </div> --}}

		<div class="col-md-12">
			<div class="mb-3">
				<label class="form-label">{{ __('Maximum Output Length') }}</label>
				<input
						class="form-control"
						id="anthropic_max_output_length"
						type="number"
						name="anthropic_max_output_length"
						min="0"
						value="{{ setting('anthropic_max_output_length', 200) }}"
						required
				>
				<x-alert class="mt-2">
					<p>
						{{ __('In Words. OpenAI has a hard limit based on Token limits for each model. Refer to OpenAI documentation to learn more. As a recommended by OpenAI, max result length is capped at 2000 tokens') }}
					</p>
					<p>
						{{ __('The maximum output length refers to the point at which the AI-generated response will stop. It can occur when the response reaches 4096 bytes or when the generated content is considered sufficient for the given context.') }}
					</p>
				</x-alert>
			</div>
		</div>

	</div>

		<button
				class="btn btn-primary w-full"
				id="settings_button"
				form="settings_form"
				{{--            onclick="checkMaxOutputLength()" --}}
		>
			{{ __('Save') }}
		</button>
	</div>
</form>
@endsection

@push('script')
    <script>
        function checkMaxOutputLength() {
            var maxOutputLength = document.getElementById("anthropic_max_output_length").value;
            var msg = "{{ __('The maximum output length is set above 2000. Are you sure you want to continue?') }}";
            if (maxOutputLength > 2000) {
                var confirmation = confirm(msg);
                if (!confirmation) {
                    event.preventDefault();
                }
            }
        }
    </script>

    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/select2/select2.min.js') }}"></script>
@endpush
