<div
	class="mb-3"
	x-data='{
		initialModel: @json($currentModel) || "",
		selectedModel: @json($currentModel) || ""
	}'
>
	<x-card
		class="w-full"
		size="sm"
	>
		<label class="form-label">{{ __($listLabel) }}</label>
		<select
			class="form-select"
			id="{{ $listId }}"
			name="{{ $listId }}"
			@change="selectedModel = $event.target.value"
			@if($bedrockOptions)
				onchange="toggleBedrockModel(this.value)"
			@endif
		>
			@foreach ($drivers as $driver)
				@if ($driver->enum()->value === \App\Enums\BedrockEngine::BEDROCK->value || $driver->enum()->isEmbedding())
					@continue
				@endif
				<option
					value="{{ $driver->enum()->slug() }}"
					{{ $currentModel === $driver->enum()->slug() ? 'selected' : null }}
				>
					{{ $driver->enum()->label() }}
				</option>
			@endforeach

			@php
				if ($fineModelOptions) {
					App\Http\Controllers\AIFineTuneController::getFineModelOption( $setting->openai_default_model );
				}
			@endphp

			@if($bedrockOptions)
				<option
					value="{{\App\Enums\BedrockEngine::BEDROCK->value}}"
					{{ $currentModel  === \App\Enums\BedrockEngine::BEDROCK->value ? 'selected' : null }}
				>
					{{ \App\Enums\BedrockEngine::BEDROCK->label() }}
				</option>
			@endif
		</select>

		<x-alert
			class="mt-2 py-5"
			variant="danger"
			x-show="selectedModel !== initialModel"
		>
			@lang('The default AI Model is being updated. Only the default model\'s credits for each AI engine will be visible and usable by users. User credits will not be automatically transferred to the new default model. Therefore, if users lack credits for this model, they will be unable to use the tools. Additionally, if no credits are allocated for the new default model in the plans, newly registered users will also be unable to use the tools due to insufficient credits.')
			<br>
			<br>
			<div
				@click="if (confirm('{{ __('Are you sure? This is permanent.') }}')) { moveUserCredits($data.initialModel, $data.selectedModel); }"
				class="cursor-pointer flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/15 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[17%] dark:bg-heading-foreground/[20%]">
				<div>
					<h3 class="mb-0 text-2xs">{{__("Transfer Users Credits")}}</h3>
					<p class="m-0 text-2xs opacity-80">{{__("Click to move users credits from the old model to the new default model.")}}</p>
				</div>
				<div class="ms-auto">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="44"
						height="44"
						viewBox="0 0 44 44"
						fill="none"
					>
						<g clip-path="url(#clip0_6440_1039)">
							<path
								d="M5.5 22C5.5 24.1668 5.92678 26.3124 6.75599 28.3143C7.58519 30.3161 8.80057 32.1351 10.3327 33.6673C11.8649 35.1994 13.6839 36.4148 15.6857 37.244C17.6876 38.0732 19.8332 38.5 22 38.5C24.1668 38.5 26.3124 38.0732 28.3143 37.244C30.3161 36.4148 32.1351 35.1994 33.6673 33.6673C35.1994 32.1351 36.4148 30.3161 37.244 28.3143C38.0732 26.3124 38.5 24.1668 38.5 22C38.5 19.8332 38.0732 17.6876 37.244 15.6857C36.4148 13.6839 35.1994 11.8649 33.6673 10.3327C32.1351 8.80057 30.3161 7.58519 28.3143 6.75599C26.3124 5.92679 24.1668 5.5 22 5.5C19.8332 5.5 17.6876 5.92679 15.6857 6.75599C13.6839 7.58519 11.8649 8.80057 10.3327 10.3327C8.80057 11.8649 7.58519 13.6839 6.75599 15.6857C5.92678 17.6876 5.5 19.8332 5.5 22Z"
								stroke="url(#paint0_linear_6440_1039)"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
							<path
								d="M16.5 18.3333C16.5 19.792 17.0795 21.191 18.1109 22.2224C19.1424 23.2539 20.5413 23.8333 22 23.8333C23.4587 23.8333 24.8576 23.2539 25.8891 22.2224C26.9205 21.191 27.5 19.792 27.5 18.3333C27.5 16.8746 26.9205 15.4757 25.8891 14.4442C24.8576 13.4128 23.4587 12.8333 22 12.8333C20.5413 12.8333 19.1424 13.4128 18.1109 14.4442C17.0795 15.4757 16.5 16.8746 16.5 18.3333Z"
								stroke="url(#paint1_linear_6440_1039)"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
							<path
								d="M11.3081 34.5565C11.7619 33.0462 12.6904 31.7225 13.9559 30.7816C15.2214 29.8407 16.7565 29.3328 18.3334 29.3333H25.6668C27.2457 29.3328 28.7827 29.8419 30.0491 30.7849C31.3156 31.728 32.2438 33.0546 32.6958 34.5675"
								stroke="url(#paint2_linear_6440_1039)"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
						</g>
						<defs>
							<linearGradient
								id="paint0_linear_6440_1039"
								x1="5.5"
								y1="12.232"
								x2="33.187"
								y2="36.652"
								gradientUnits="userSpaceOnUse"
							>
								<stop stop-color="#82E2F4" />
								<stop
									offset="0.502"
									stop-color="#8A8AED"
								/>
								<stop
									offset="1"
									stop-color="#6977DE"
								/>
							</linearGradient>
							<linearGradient
								id="paint1_linear_6440_1039"
								x1="16.5"
								y1="15.0773"
								x2="25.729"
								y2="23.2173"
								gradientUnits="userSpaceOnUse"
							>
								<stop stop-color="#82E2F4" />
								<stop
									offset="0.502"
									stop-color="#8A8AED"
								/>
								<stop
									offset="1"
									stop-color="#6977DE"
								/>
							</linearGradient>
							<linearGradient
								id="paint2_linear_6440_1039"
								x1="11.3081"
								y1="30.4011"
								x2="13.5887"
								y2="38.6205"
								gradientUnits="userSpaceOnUse"
							>
								<stop stop-color="#82E2F4" />
								<stop
									offset="0.502"
									stop-color="#8A8AED"
								/>
								<stop
									offset="1"
									stop-color="#6977DE"
								/>
							</linearGradient>
							<clipPath id="clip0_6440_1039">
								<rect
									width="44"
									height="44"
									fill="white"
								/>
							</clipPath>
						</defs>
					</svg>
				</div>
			</div>

			<div
				@click="if (confirm('{{ __('Are you sure? This is permanent.') }}')) { updatePlansCredits($data.initialModel, $data.selectedModel); }"
				class="cursor-pointer flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/15 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[17%] dark:bg-heading-foreground/[20%]">
				<div>
					<h3 class="mb-0 text-2xs">{{__("Update Plans Credits")}}</h3>
					<p class="m-0 text-2xs opacity-80">{{__("Click to transfers allocated credits from the old model to the new default model in plans for the new registered users.")}}</p>
				</div>
				<div class="ms-auto">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="40"
						height="40"
						viewBox="0 0 40 40"
						fill="none"
					>
						<g clip-path="url(#clip0_6446_1542)">
							<path
								d="M28.3334 13.3334V8.33335C28.3334 7.89133 28.1578 7.4674 27.8453 7.15484C27.5327 6.84228 27.1088 6.66669 26.6667 6.66669H10.0001C9.11603 6.66669 8.26818 7.01788 7.64306 7.643C7.01794 8.26812 6.66675 9.11597 6.66675 10M6.66675 10C6.66675 10.8841 7.01794 11.7319 7.64306 12.357C8.26818 12.9822 9.11603 13.3334 10.0001 13.3334H30.0001C30.4421 13.3334 30.866 13.5089 31.1786 13.8215C31.4912 14.1341 31.6667 14.558 31.6667 15V20M6.66675 10V30C6.66675 30.8841 7.01794 31.7319 7.64306 32.357C8.26818 32.9822 9.11603 33.3334 10.0001 33.3334H30.0001C30.4421 33.3334 30.866 33.1578 31.1786 32.8452C31.4912 32.5326 31.6667 32.1087 31.6667 31.6667V26.6667"
								stroke="url(#paint0_linear_6446_1542)"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
							<path
								d="M33.3333 20V26.6667H26.6666C25.7825 26.6667 24.9347 26.3155 24.3096 25.6904C23.6844 25.0652 23.3333 24.2174 23.3333 23.3333C23.3333 22.4493 23.6844 21.6014 24.3096 20.9763C24.9347 20.3512 25.7825 20 26.6666 20H33.3333Z"
								stroke="url(#paint1_linear_6446_1542)"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
						</g>
						<defs>
							<linearGradient
								id="paint0_linear_6446_1542"
								x1="6.66675"
								y1="12.1067"
								x2="28.8153"
								y2="30.4208"
								gradientUnits="userSpaceOnUse"
							>
								<stop stop-color="#82E2F4" />
								<stop
									offset="0.502"
									stop-color="#8A8AED"
								/>
								<stop
									offset="1"
									stop-color="#6977DE"
								/>
							</linearGradient>
							<linearGradient
								id="paint1_linear_6446_1542"
								x1="23.3333"
								y1="21.36"
								x2="28.7569"
								y2="28.5355"
								gradientUnits="userSpaceOnUse"
							>
								<stop stop-color="#82E2F4" />
								<stop
									offset="0.502"
									stop-color="#8A8AED"
								/>
								<stop
									offset="1"
									stop-color="#6977DE"
								/>
							</linearGradient>
							<clipPath id="clip0_6446_1542">
								<rect
									width="40"
									height="40"
									fill="white"
								/>
							</clipPath>
						</defs>
					</svg>
				</div>
			</div>
		</x-alert>
	</x-card>
</div>

<script>
	@if($bedrockOptions)
		function toggleBedrockModel(value) {
			const bedrockSelect = document.getElementById('anthropic_bedrock') || document.getElementById('stable_bedrock');
			if (value === "aws_bedrock") {
				bedrockSelect.style.display = 'block';
			} else {
				bedrockSelect.style.display = 'none';
			}
		}

		document.addEventListener("DOMContentLoaded", function () {
			const defaultModelSelect = document.getElementById('anthropic_default_model') || document.getElementById('stablediffusion_default_model');
			toggleBedrockModel(defaultModelSelect.value);
		});
	@endif
	function moveUserCredits(oldModel, newModel) {
		if (!oldModel || !newModel) {
			alert('Invalid model data provided.');
			return;
		}
		const url = '/dashboard/admin/transfer/users-credits';
		const data = {
			oldModel: oldModel,
			newModel: newModel
		};

		$.ajax({
			url: url,
			type: 'POST',
			contentType: 'application/json', // Send data as JSON
			data: JSON.stringify(data), // Convert data to JSON string
			success: function(response) {
				if (response.message) {
					alert(response.message); // Show Laravel's message
				} else {
					alert('Unexpected response received.');
				}
			},
			error: function(xhr) {
				// Attempt to parse and show error message
				let errorMessage = 'Failed to move user credits.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					errorMessage = xhr.responseJSON.message;
				}
				alert(errorMessage);
			}
		});
	}

	function updatePlansCredits(oldModel, newModel) {
		if (!oldModel || !newModel) {
			alert('Invalid model data provided.');
			return;
		}
		const url = '/dashboard/admin/transfer/plans-credits';
		const data = {
			oldModel: oldModel,
			newModel: newModel
		};

		$.ajax({
			url: url,
			type: 'POST',
			contentType: 'application/json', // Send data as JSON
			data: JSON.stringify(data), // Convert data to JSON string
			success: function(response) {
				if (response.message) {
					alert(response.message); // Show Laravel's message
				} else {
					alert('Unexpected response received.');
				}
			},
			error: function(xhr) {
				// Attempt to parse and show error message
				let errorMessage = 'Failed to update plans credits.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					errorMessage = xhr.responseJSON.message;
				}
				alert(errorMessage);
			}
		});
	}
</script>
