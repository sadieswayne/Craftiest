@php
	$creativity_levels = [
		'0.25' => 'Economic',
		'0.5' => 'Average',
		'0.75' => 'Good',
		'1' => 'Premium',
	];

	$voice_tones = ['Professional', 'Funny', 'Casual', 'Excited', 'Witty', 'Sarcastic', 'Feminine', 'Masculine', 'Bold', 'Dramatic', 'Grumpy', 'Secretive', 'other'];

	$youtube_actions = [
		'blog' => __('Prepare a Blog Post'),
		'short' => __('Explain the Main Idea'),
		'list' => __('Create a List'),
		'tldr' => __('Create TLDR'),
		'prons_cons' => __('Prepare Pros and Cons'),
	];
@endphp

<div
	class="lqd-generator-options px-5 pb-8"
	id="lqd-generator-options"
>
	<x-card class="mb-5 text-2xs">
		<x-credit-list :is-js="false"/>
	</x-card>

	<form
		class="lqd-generator-form flex flex-wrap justify-between space-y-5"
		id="openai_generator_form"
	>
		<div
			class="flex w-full flex-col gap-5"
			x-data="{ 'brandEnabled': false }"
		>
			<x-forms.input
				id="brand"
				type="checkbox"
				name="brand"
				label="{{ __('Include Your Brand') }}"
				@change="brandEnabled = $el.checked"
				switcher
			/>

			<div
				class="hidden w-full flex-col gap-5"
				:class="{ 'hidden': !brandEnabled, 'flex': brandEnabled }"
			>
				<x-forms.input
					id="company"
					size="lg"
					type="select"
					label="{{ __('Select Company') }}"
					name="company"
				>
					<x-slot:label-extra>
						<a
							class="size-6 inline-flex items-center justify-center rounded-lg bg-green-500/20 text-green-700 transition-all hover:scale-110 hover:bg-green-500 hover:text-green-100"
							href="{{  (route('dashboard.user.brand.create')) }}"
						>
							<x-tabler-plus class="size-4"/>
						</a>
					</x-slot:label-extra>
					<option value="">
						{{ __('Select Company') }}
					</option>
					@foreach (auth()->user()->getCompanies() as $company)
						<option
							data-tone_of_voice="{{ $company->tone_of_voice }}"
							value="{{ $company->id }}"
						>
							{{ $company->name }}
						</option>
					@endforeach
				</x-forms.input>

				<x-forms.input
					id="product"
					name="product"
					type="select"
					size="lg"
					label="{{ __('Select Product/Service') }}"
				>
					<option value="">{{ __('Select Product') }}</option>
				</x-forms.input>
			</div>


		</div>

		<div
			class="flex w-full flex-col gap-5"
			x-data="{ 'bulkEnabled': false }"
		>
			<x-forms.input
				id="bulk"
				type="checkbox"
				name="bulk"
				label="{{ __('Generate Bulk Posts') }}"
				@change="bulkEnabled = $el.checked"
				switcher
			/>

			<div
				class="hidden w-full flex-col gap-5"
				:class="{ 'hidden': !bulkEnabled, 'flex': bulkEnabled }"
			>
				<x-forms.input
					class:container="w-full"
					id="number_of_results"
					label="{{ __('Number of Results') }}"
					type="number"
					name="number_of_results"
					min="1"
					value="1"
					size="lg"
					placeholder="{{ __('Number of results') }}"
					required
				/>
			</div>
		</div>
		@foreach (json_decode($openai->questions, false, 512, JSON_THROW_ON_ERROR) ?? [] as $question)
			<div class="w-full">
				@php
					$placeholder = !empty($question?->description) ? __($question?->description) : __($question?->question);
				@endphp
				@if ($question?->type == 'text')
					<x-forms.input
						id="{{ $question?->name }}"
						label="{{ __($question?->question) }}"
						type="{{ $question?->type }}"
						name="{{ $question?->name }}"
						size="lg"
						maxlength="{{ $setting->openai_max_input_length }}"
						placeholder="{{ __($placeholder) }}"
						required
					/>
				@elseif($question?->type == 'textarea')
					<x-forms.input
						id="{{ $question?->name }}"
						label="{{ __($question?->question) }}"
						type="textarea"
						name="{{ $question?->name }}"
						size="lg"
						rows="12"
						placeholder="{{ __($placeholder) }}"
						maxlength="{{ $setting->openai_max_input_length }}"
						required
					/>
				@elseif($question?->type == 'select')
					<x-forms.input
						id="{{ $question?->name }}"
						label="{{ __($question?->question) }}"
						type="select"
						name="{{ $question?->name }}"
						size="lg"
						required
					>
						@foreach ($question?->selectListValues ?? [] as $input)
							<option value="{{ $input }}">
								{{ $input }}
							</option>
						@endforeach
					</x-forms.input>
				@elseif($question?->type == 'rss_feed')
					<x-forms.input
						id="{{ $question?->name }}"
						label="{{ __($question?->question) }}"
						type="{{ $question?->type }}"
						name="{{ $question?->name }}"
						size="lg"
						maxlength="{{ $setting->openai_max_input_length }}"
						placeholder="{{ __($placeholder) }}"
						required
					>
						<x-slot:action>
							<button
								class="fetch-rss flex h-full items-center gap-2 rounded-e-input px-3 text-2xs font-medium transition-colors hover:bg-secondary hover:text-secondary-foreground"
								type="button"
							>
								<x-tabler-refresh class="size-4"/>
								{{ __('Fetch RSS') }}
							</button>
						</x-slot:action>
					</x-forms.input>
				@elseif($question?->type == 'url')
					<x-forms.input
						id="{{ $question?->name }}"
						label="{{ __($question?->question) }}"
						type="{{ $question?->type }}"
						name="{{ $question?->name }}"
						maxlength="{{ $setting->openai_max_input_length }}"
						placeholder="{{ __($placeholder) }}"
						size="lg"
						required
					/>
				@endif
			</div>
		@endforeach

		@if ($openai->type == 'youtube')
			<x-forms.input
				id="youtube_action"
				label="{{ __('Action') }}"
				type="select"
				name="youtube_action"
				size="lg"
				required
			>
				@foreach ($youtube_actions as $value => $label)
					<option value="{{ $value }}">{{ $label }}</option>
				@endforeach
			</x-forms.input>

			<x-forms.input
				class:container="w-full md:w-[48%]"
				id="language"
				label="{{ __('Language') }}"
				name="language"
				type="select"
				size="lg"
				required
			>
				@include('panel.user.openai.components.countries')
			</x-forms.input>
		@endif

		@if ($openai->type == 'text' || $openai->type == 'rss')
			<x-forms.input
				class:container="w-full md:w-[48%]"
				id="language"
				label="{{ __('Language') }}"
				type="select"
				name="language"
				size="lg"
				required
			>
				@include('panel.user.openai.components.countries')
			</x-forms.input>
			@if (setting('hide_output_length_option') != 1)
				<x-forms.input
					class:container="w-full md:w-[48%]"
					id="maximum_length"
					label="{{ __('Maximum Length') }}"
					type="number"
					name="maximum_length"
					max="{{ $setting->openai_max_output_length }}"
					value="{{ $setting->openai_max_output_length }}"
					placeholder="{{ __('Maximum character length of text') }}"
					required
					size="lg"
					min="1"
					step="1"
				/>
			@endif

			@if (setting('hide_creativity_option') != 1)
				<x-forms.input
					class:container="w-full md:w-[48%]"
					id="creativity"
					size="lg"
					type="select"
					label="{{ __('Creativity') }}"
					containerClass="w-full md:w-[48%]"
					name="creativity"
					required
				>
					@foreach ($creativity_levels as $creativity => $label)
						<option
							value="{{ $creativity }}"
							@selected($setting->openai_default_creativity == $creativity)
						>
							{{ __($label) }}
						</option>
					@endforeach
				</x-forms.input>
			@endif
			@if (setting('hide_tone_of_voice_option') != 1)
				<x-forms.input
					class:container="w-full md:w-[48%]"
					id="tone_of_voice"
					size="lg"
					type="select"
					label="{{ __('Tone of Voice') }}"
					containerClass="w-full md:w-[48%]"
					name="tone_of_voice"
					required
				>
					@foreach ($voice_tones as $tone)
						<option
							value="{{ $tone }}"
							@selected($setting->openai_default_tone_of_voice == $tone)
						>
							{{ __($tone) }}
						</option>
					@endforeach
				</x-forms.input>
				<x-forms.input
					class:container="hidden w-full md:w-[48%]"
					id="tone_of_voice_custom"
					name="tone_of_voice_custom"
					type="text"
					label="{{ __('Enter custom tone') }}"
					switcher
				/>
			@endif
		@endif

		@if ($models->count() && setting('select_model_option', '0') == '1')
			<x-forms.input
				id="chatbot_front_model"
				type="select"
				size="lg"
				name="chatbot_front_model"
				label="{{ __('Select model') }}"
			>
				<option value="">
					{{ __('Select model') }}
				</option>
				@foreach($models as $model)
					<option value="{{ $model->key }}">
						{{ $model->selected_title }}
					</option>
				@endforeach
			</x-forms.input>
		@endif
		@if(setting('open_router_status') == 1)
			@includeFirst(['open-router::open-router-select','vendor.empty'])
		@endif
		<input
			id="openai_type"
			hidden
			value="{{ $openai->type }}"
		>
		<input
			id="openai_slug"
			hidden
			value="{{ $openai->slug }}"
		>
		<input
			id="openai_id"
			hidden
			value="{{ $openai->id }}"
		>
		<input
			id="openai_questions"
			hidden
			value="{{ $openai->questions }}"
		>

		<x-button
			class="w-full"
			id="openai_generator_button"
			size="lg"
			type="button"
		>
			<span class="hidden group-[.lqd-form-submitting]:inline-flex">{{ __('Please wait...') }}</span>
			<span class="group-[.lqd-form-submitting]:hidden">{{ __('Generate') }}</span>
		</x-button>
		<script>
			$('#company').on('change', function () {
				var brand_id = $(this).val();
				if (brand_id == '') {
					$('#product').empty();
					$('#product').append('<option value="">Select Product</option>');
					$('#tone_of_voice option').prop('selected', false);
				} else {
					$.ajax({
						url: '/dashboard/user/brand/get-products/' + brand_id,
						type: 'get',
						success: function (response) {
							$('#product').empty();
							if (response.length == 0) {
								$('#product').append('<option value="">Select Product</option>');
							} else {
								$.each(response, function (index, value) {
									$('#product').append('<option value="' + value.id + '">' + value
											.name +
										'</option>');
								});
							}
							$('#tone_of_voice').val($('#company :selected').attr('data-tone_of_voice'));
						}
					});
				}

			});
			document.getElementById('tone_of_voice')?.addEventListener('change', function () {
				var customInput = document.getElementById('tone_of_voice_custom');
				if (this.value === 'other') {
					customInput.parentNode.classList.remove('hidden');
				} else {
					customInput.parentNode.classList.add('hidden');
				}
			});
		</script>

	</form>
</div>
