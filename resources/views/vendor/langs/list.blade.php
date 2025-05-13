@extends(config('elseyyid-location.layout'))
@section(config('elseyyid-location.content_section'))
	<div class="col-md-12">

		@if ($lang != 'edit')
			@php $lang_region = LaravelLocalization::getSupportedLocales()[str_replace('_','-',$lang)]['regional']; @endphp
			@php $lang_native = LaravelLocalization::getSupportedLocales()[str_replace('_','-',$lang)]['native']; @endphp
			<h2 class="mb-6 text-center">{{ __('Editing Language') }}: <span
					class="mr-2 align-sub text-3xl">{{ country2flag(substr($lang_region, strrpos($lang_region, '_') + 1)) }}</span>{{ ucfirst($lang_native) }}
				<span
					class="ml-2 text-xs opacity-20"
				>{{ $lang }}</h2>
		@else
			<div
				class="alert alert-danger mb-6"
				role="alert"
			>
				<strong>{{ __('Take a backup before process!') }}</strong><br>
				{{ __('Your changes will override en.json file!') }}
			</div>
			<h2 class="mb-6 text-center">{{ __('Editing Main Strings') }}</h2>
		@endif
		<input
			class="form-control mb-6 rounded-xl"
			id="search_string"
			type="text"
			onkeyup="searchStrings()"
			placeholder="{{ __('Filter strings...') }}"
		>
		<form
			action="{{ route('dashboard.admin.translations.auto', [$lang]) }}"
			method="POST"
		>
			@csrf
			<button
				class="btn btn-secondary mb-2 rounded-xl"
				type="submit"
			>{{ __('Auto Translate (Each click will translate the next 100 key)') }}</button>
		</form>
		<div class="card">
			<div class="card-table table-responsive">

				<table
					class="table"
					id="strings"
				>
					<thead>
					<tr>
						<th>{{ __('String') }}</th>
						<th>{{ __('Translation') }}</th>
					</tr>
					</thead>
					<tbody>
					@foreach ($list as $key => $value)
						<tr>
							<td
								class="hidden"
								width="10px"
							><input
									type="checkbox"
									name="ids_to_edit[]"
									value="{{ $value->id }}"
								/></td>
							@foreach ($value->toArray() as $key => $element)
								@if ($key !== 'code')
									@if ($key === 'en')
										<td class="w-[45%]">
											<div data-name="{{ $key }}">{{ $element }}</div>
										</td>
									@else
										<td class="w-[50%]">
											<input
												class="form-control w-full px-2 py-2 placeholder:text-gray-300 dark:border-solid dark:border-[--tblr-border-color] dark:placeholder:text-white/50"
												data-pk="{{ $value->code }}"
												data-name="{{ $key }}"
												type="text"
												value="{{ $element }}"
												placeholder="{{ __('enter string') }}"
											>
										</td>
									@endif
								@endif
							@endforeach
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
		<div class="fixed bottom-6 left-[--navbar-width] right-0">
			<div class="container">
				<div class="row">
					<div class="col-md-8 mx-auto !px-8">
						<div
							class="btn btn-primary w-full"
							id="save_all"
							data-lang="{{ $lang }}"
						>Save
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section(config('elseyyid-location.scripts_section'))
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script>
		$(document).ready(function () {
			"use strict";
			$(document).ready(function () {
				$('#save_all').click(function () {
					let demo = @json($app_is_demo ? true : false, JSON_THROW_ON_ERROR);
					if (demo) {
						toastr.info("{{ __('This feature is disabled in Demo version') }}");
						return false;
					}

					document.getElementById("save_all").disabled = true;
					document.getElementById("save_all").innerHTML = magicai_localize.please_wait;

					var inputData = {};
					$('table input[type="text"]').each(function () {
						let key = $(this).data('pk');
						let value = $(this).val();
						if (key && value) {
							inputData[key] = value;
						}
					});

					const entries = Object.entries(inputData);
					const chunkSize = 50;
					const chunks = [];
					for (let i = 0; i < entries.length; i += chunkSize) {
						chunks.push(entries.slice(i, i + chunkSize));
					}

					(async function sendChunks() {
						console.log('Starting chunk processing...');
						try {
							for (let index = 0; index < chunks.length; index++) {
								const chunk = Object.fromEntries(chunks[index]);
								await $.ajax({
									type: "post",
									headers: {
										Accept: "application/json",
										"X-CSRF-TOKEN": "{{ csrf_token() }}",
									},
									url: "/translations/lang/update-all",
									data: {
										_token: "{{ csrf_token() }}",
										lang: $('#save_all').data('lang'),
										json: JSON.stringify(chunk),
									},
								});
							}

							// Show a single success message after all chunks are processed
							toastr.success("{{ __('All translations saved successfully!') }}");
						} catch (error) {
							console.error('Error processing chunk:', error);
							toastr.error("{{ __('An error occurred while saving translations.') }}");
						} finally {
							document.getElementById("save_all").disabled = false;
							document.getElementById("save_all").innerHTML = "Save";
						}
					})();
				});
			});
		});
	</script>
	<script>
		function searchStrings() {
			var input, filter, table, tr, td, i, j, txtValue;
			input = document.getElementById("search_string");
			filter = input.value.toUpperCase();
			table = document.getElementById("strings");
			tr = table.getElementsByTagName("tr");

			for (i = 0; i < tr.length; i++) {
				var foundMatch = false;
				td = tr[i].getElementsByTagName("td");

				if (td.length > 0) {
					for (j = 0; j < td.length; j++) {
						var divElement = td[j].querySelector("div[data-name='en']");
						var inputElement = td[j].querySelector("input");
						if (divElement && divElement.textContent.toUpperCase().indexOf(filter) > -1) {
							foundMatch = true;
							break;
						} else if (inputElement && inputElement.value.toUpperCase().indexOf(filter) > -1) {
							foundMatch = true;
							break;
						}
					}
				}

				if (foundMatch) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = tr[i].parentNode.tagName === 'THEAD' ? 'table-row' : 'none';
				}
			}
		}
	</script>
@endsection
