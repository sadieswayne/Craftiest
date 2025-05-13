<script>
	function generateSpeech() {
		"use strict";

		document.getElementById("generate_speech_button").disabled = true;
		document.getElementById("generate_speech_button").innerHTML = magicai_localize.please_wait;

		var formData = new FormData();
		var speechData = [];
		formData.append('workbook_title', $('#workbook_title').val());

		let aContent = "";

		$('.speeches .speech').each(function() {
			var data = {
				voice: $(this).find('textarea').attr('data-voice'),
				lang: $(this).find('textarea').attr('data-lang'),
				language: $(this).find('textarea').attr('data-language'),
				pace: $(this).find('textarea').attr('data-pace'),
				platform: $(this).find('textarea').attr('data-platform'),
				break: $(this).find('textarea').attr('data-break'),
				voice_openai: $(this).find('textarea').attr('data-voiceopenai'),
				model_openai: $(this).find('textarea').attr('data-modelopenai'),
				content: $(this).find('textarea').val() + ' ' + sayAs,
				name: $(this).find('textarea').attr('name')
			};
			speechData.push(data);
			aContent += data.content + "";
		});
		var jsonData = JSON.stringify(speechData);
		formData.append('speeches', jsonData);

		$.ajax({
			type: "post",
			headers: {
				'X-CSRF-TOKEN': "{{ csrf_token() }}",
			},
			url: "/dashboard/user/openai/generate-speech",
			data: formData,
			contentType: false,
			processData: false,
			success: function(data) {
				toastr.success("{{ __('Generated') }}");
				document.getElementById("generate_speech_button").disabled = false;
				document.getElementById("generate_speech_button").innerHTML = "{{ __('Generate') }}";
				$("#generator_sidebar_table").html(data.html2);

				var audioElements = document.querySelectorAll('.data-audio');
				audioElements.forEach(generateWaveForm);
			},
			error: function(data) {
				var err = data.responseJSON.errors;
				$.each(err, function(index, value) {
					toastr.error(value);
				});
				document.getElementById("generate_speech_button").disabled = false;
				// document.getElementById("generate_speech_button").innerHTML = "{{ __('Save') }}";
			}
		});
		return false;
	}

	const openaiVoiceData = [{
		value: "alloy",
		label: "Alloy (Male)"
	}, {
		value: "echo",
		label: "Echo (Male)"
	}, {
		value: "fable",
		label: "Fable (Male)"
	}, {
		value: "onyx",
		label: "Onyx (Male)"
	}, {
		value: "nova",
		label: "Nova (Female)"
	}, {
		value: "shimmer",
		label: "Shimmer (Female)"
	}];

	const allowedOpenAIList = [
		"af-ZA",
		"ar-XA",
		"mr-IN",
		"cmn-TW",
		"fil-PH",
		"gu-IN",
		"en-AU",
		"kn-IN",
		"lv-LV",
		"ml-IN",
		"cmn-CN",
		"pa-IN",
		"sv-SE",
		"te-IN",
		"bn-IN",
		"eu-ES",
		"bg-BG",
		"ca-ES",
		"cs-CZ",
		"da-DK",
		"nl-BE",
		"en-US",
		"fi-FI",
		"fr-FR",
		"gl-ES",
		"de-DE",
		"el-GR",
		"he-IL",
		"hi-IN",
		"hu-HU",
		"is-IS",
		"id-ID",
		"it-IT",
		"ja-JP",
		"kn-IN",
		"ko-KR",
		"lv-LV",
		"ms-MY",
		"ml-IN",
		"nb-NO",
		"pl-PL",
		"pt-BR",
		"pt-PT",
		"ro-RO",
		"ru-RU",
		"sr-RS",
		"sk-SK",
		"es-ES",
		"ta-IN",
		"th-TH",
		"tr-TR",
		"uk-UA",
		"nl-NL",
		"en-IN",
		"en-GB",
		"vi-VN",
		"yue-HK"
	];

	const allowedElevenLabsList = [
		"en-US",
		"en-AU",
		"en-IN",
		"en-GB",
		"hi-IN",
		"pt-PT",
		"es-ES",
		"fr-FR",
		"de-DE",
		"ja-JP",
		"ar-XA",
		"ru-RU",
		"ko-KR",
		"id-ID",
		"it-IT",
		"nl-NL",
		"tr-TR",
		"pl-PL",
		"sv-SE",
		"fil-PH",
		"ms-MY",
		"ro-RO",
		"uk-UA",
		"el-GR",
		"cs-CZ",
		"da-DK",
		"fi-FI",
		"bg-BG",
		"sk-SK",
		"ta-IN",
	];

	const allowedAzureList = [
		"af-ZA", "am-ET", "ar-XA", "ar-AE", "ar-BH", "ar-DZ", "ar-EG", "ar-IQ", "ar-JO", "ar-KW", "ar-LB",
		"ar-LY", "ar-MA", "ar-OM", "ar-QA", "ar-SA", "ar-SY", "ar-TN", "ar-YE", "az-AZ", "bg-BG",
		"bn-BD", "bn-IN", "bs-BA", "ca-ES", "cs-CZ", "cy-GB", "da-DK", "de-AT", "de-CH", "de-DE",
		"el-GR", "en-AU", "en-CA", "en-GB", "en-HK", "en-IE", "en-IN", "en-KE", "en-NG", "en-NZ",
		"en-PH", "en-SG", "en-TZ", "en-US", "en-ZA", "es-AR", "es-BO", "es-CL", "es-CO", "es-CR",
		"es-CU", "es-DO", "es-EC", "es-ES", "es-GQ", "es-GT", "es-HN", "es-MX", "es-NI", "es-PA",
		"es-PE", "es-PR", "es-PY", "es-SV", "es-US", "es-UY", "es-VE", "et-EE", "eu-ES", "fa-IR",
		"fi-FI", "fil-PH", "fr-BE", "fr-CA", "fr-CH", "fr-FR", "ga-IE", "gl-ES", "gu-IN", "he-IL",
		"hi-IN", "hr-HR", "hu-HU", "hy-AM", "id-ID", "is-IS", "it-IT", "ja-JP", "jv-ID", "ka-GE",
		"kk-KZ", "km-KH", "kn-IN", "ko-KR", "lo-LA", "lt-LT", "lv-LV", "mk-MK", "ml-IN", "mn-MN",
		"mr-IN", "ms-MY", "mt-MT", "my-MM", "nb-NO", "ne-NP", "nl-BE", "nl-NL", "pl-PL", "ps-AF",
		"pt-BR", "pt-PT", "ro-RO", "ru-RU", "si-LK", "sk-SK", "sl-SI", "so-SO", "sq-AL", "sr-LATN-RS",
		"sr-RS", "su-ID", "sv-SE", "sw-KE", "sw-TZ", "ta-IN", "ta-LK", "ta-MY", "ta-SG", "te-IN",
		"th-TH", "tr-TR", "uk-UA", "ur-IN", "ur-PK", "uz-UZ", "vi-VN", "wuu-CN", "yue-CN", "zh-CN",
		"zh-HK", "zh-TW", "zu-ZA"
	];


	const elevenLabsVoices = [
		@if ($elevenlabServiceVoice !== [])
			@foreach ($elevenlabServiceVoice as $voice)
				{
					"voice_id": "{{ $voice['voice_id'] }}",
					"name": "{{ $voice['name'] }}",
					"preview_url": "{{ $voice['preview_url'] }}",
				},
			@endforeach
		@endif

		@if ($elevenlabs)
			@foreach ($elevenlabs as $voice)
				{
					"voice_id": "{{ $voice->voice_id }}",
					"name": "{{ $voice->name }}",
				},
			@endforeach
		@endif
	];

	const voicesData = {
		"af-ZA": [{
			value: "af-ZA-Standard-A",
			label: "Ayanda ({{ __('Female') }})"
		}],
		"ar-XA": [{
			value: "ar-XA-Standard-A",
			label: "Fatima ({{ __('Female') }})"
		},
			{
				value: "ar-XA-Standard-B",
				label: "Ahmed ({{ __('Male') }})"
			},
			{
				value: "ar-XA-Standard-C",
				label: "Mohammed ({{ __('Male') }})"
			},
			{
				value: "ar-XA-Standard-D",
				label: "Aisha ({{ __('Female') }})"
			},
			{
				value: "ar-XA-Wavenet-A",
				label: "Layla ({{ __('Female') }})"
			},
			{
				value: "ar-XA-Wavenet-B",
				label: "Ali ({{ __('Male') }})"
			},
			{
				value: "ar-XA-Wavenet-C",
				label: "Omar ({{ __('Male') }})"
			},
			{
				value: "ar-XA-Wavenet-D",
				label: "Zahra ({{ __('Female') }})"
			}
		],
		"eu-ES": [{
			value: "eu-ES-Standard-A",
			label: "Ane ({{ __('Female') }})"
		}],
		"bn-IN": [{
			value: "bn-IN-Standard-A",
			label: "Ananya ({{ __('Female') }})"
		},
			{
				value: "bn-IN-Standard-B",
				label: "Aryan ({{ __('Male') }})"
			},
			{
				value: "bn-IN-Wavenet-A",
				label: "Ishita ({{ __('Female') }})"
			},
			{
				value: "bn-IN-Wavenet-B",
				label: "Arry ({{ __('Male') }})"
			}
		],
		"bg-BG": [{
			value: "bg-BG-Standard-A",
			label: "Elena ({{ __('Female') }})"
		}],
		"ca-ES": [{
			value: "ca-ES-Standard-A",
			label: "Laia ({{ __('Female') }})"
		}],
		"yue-HK": [{
			value: "yue-HK-Standard-A",
			label: "Wing ({{ __('Female') }})"
		},
			{
				value: "yue-HK-Standard-B",
				label: "Ho ({{ __('Male') }})"
			},
			{
				value: "yue-HK-Standard-C",
				label: "Siu ({{ __('Female') }})"
			},
			{
				value: "yue-HK-Standard-D",
				label: "Lau ({{ __('Male') }})"
			}
		],
		"cs-CZ": [{
			value: "cs-CZ-Standard-A",
			label: "Tereza ({{ __('Female') }})"
		},
			{
				value: "cs-CZ-Wavenet-A",
				label: "Karolína ({{ __('Female') }})"
			}
		],
		"da-DK": [
			//{value:"da-DK-Neural2-D", label: "Neural2 - FEMALE"},
			//{value:"da-DK-Neural2-F", label: "Neural2 - MALE"},
			{
				value: "da-DK-Standard-A",
				label: "Emma ({{ __('Female') }})"
			},
			{
				value: "da-DK-Standard-A",
				label: "Freja ({{ __('Female') }})"
			},
			{
				value: "da-DK-Standard-A",
				label: "Ida ({{ __('Female') }})"
			},
			{
				value: "da-DK-Standard-C",
				label: "Noah ({{ __('Male') }})"
			},
			{
				value: "da-DK-Standard-D",
				label: "Mathilde ({{ __('Female') }})"
			},
			{
				value: "da-DK-Standard-E",
				label: "Clara ({{ __('Female') }})"
			},
			{
				value: "da-DK-Wavenet-A",
				label: "Isabella ({{ __('Female') }})"
			},
			{
				value: "da-DK-Wavenet-C",
				label: "Lucas ({{ __('Male') }})"
			},
			{
				value: "da-DK-Wavenet-D",
				label: "Olivia ({{ __('Female') }})"
			},
			{
				value: "da-DK-Wavenet-E",
				label: "Emily ({{ __('Female') }})"
			}
		],
		"nl-BE": [{
			value: "nl-BE-Standard-A",
			label: "Emma ({{ __('Female') }})"
		},
			{
				value: "nl-BE-Standard-B",
				label: "Thomas ({{ __('Male') }})"
			},
			{
				value: "nl-BE-Wavenet-A",
				label: "Sophie ({{ __('Female') }})"
			},
			{
				value: "nl-BE-Wavenet-B",
				label: "Lucas ({{ __('Male') }})"
			}
		],
		"nl-NL": [{
			value: "nl-NL-Standard-A",
			label: "Emma ({{ __('Female') }})"
		},
			{
				value: "nl-NL-Standard-B",
				label: "Daan ({{ __('Male') }})"
			},
			{
				value: "nl-NL-Standard-C",
				label: "Luuk ({{ __('Male') }})"
			},
			{
				value: "nl-NL-Standard-D",
				label: "Lotte ({{ __('Female') }})"
			},
			{
				value: "nl-NL-Standard-E",
				label: "Sophie ({{ __('Female') }})"
			},
			{
				value: "nl-NL-Wavenet-A",
				label: "Mila ({{ __('Female') }})"
			},
			{
				value: "nl-NL-Wavenet-B",
				label: "Sem ({{ __('Male') }})"
			},
			{
				value: "nl-NL-Wavenet-C",
				label: "Stijn ({{ __('Male') }})"
			},
			{
				value: "nl-NL-Wavenet-D",
				label: "Fenna ({{ __('Female') }})"
			},
			{
				value: "nl-NL-Wavenet-E",
				label: "Eva ({{ __('Female') }})"
			}
		],
		"en-AU": [
			//{value:"en-AU-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"en-AU-Neural2-B", label: "Neural2 - MALE"},
			//{value:"en-AU-Neural2-C", label: "Neural2 - FEMALE"},
			//{value:"en-AU-Neural2-D", label: "Neural2 - MALE"},
			{
				value: "en-AU-News-E",
				label: "Emma ({{ __('Female') }})"
			},
			{
				value: "en-AU-News-F",
				label: "Olivia ({{ __('Female') }})"
			},
			{
				value: "en-AU-News-G",
				label: "Liam ({{ __('Male') }})"
			},
			//{value:"en-AU-Polyglot-1", label: "Noah ({{ __('Male') }})"},
			{
				value: "en-AU-Standard-A",
				label: "Charlotte ({{ __('Female') }})"
			},
			{
				value: "en-AU-Standard-B",
				label: "Oliver ({{ __('Male') }})"
			},
			{
				value: "en-AU-Standard-C",
				label: "Ava ({{ __('Female') }})"
			},
			{
				value: "en-AU-Standard-D",
				label: "Jack ({{ __('Male') }})"
			},
			{
				value: "en-AU-Wavenet-A",
				label: "Sophie ({{ __('Female') }})"
			},
			{
				value: "en-AU-Wavenet-B",
				label: "William ({{ __('Male') }})"
			},
			{
				value: "en-AU-Wavenet-C",
				label: "Amelia ({{ __('Female') }})"
			},
			{
				value: "en-AU-Wavenet-D",
				label: "Thomas ({{ __('Male') }})"
			}
		],
		"en-IN": [{
			value: "en-IN-Standard-A",
			label: "Aditi ({{ __('Female') }})"
		},
			{
				value: "en-IN-Standard-B",
				label: "Arjun ({{ __('Male') }})"
			},
			{
				value: "en-IN-Standard-C",
				label: "Rohan ({{ __('Male') }})"
			},
			{
				value: "en-IN-Standard-D",
				label: "Ananya ({{ __('Female') }})"
			},
			{
				value: "en-IN-Wavenet-A",
				label: "Alisha ({{ __('Female') }})"
			},
			{
				value: "en-IN-Wavenet-B",
				label: "Aryan ({{ __('Male') }})"
			},
			{
				value: "en-IN-Wavenet-C",
				label: "Kabir ({{ __('Male') }})"
			},
			{
				value: "en-IN-Wavenet-D",
				label: "Diya ({{ __('Female') }})"
			}
		],
		"en-GB": [
			//{value:"en-GB-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"en-GB-Neural2-B", label: "Neural2 - MALE"},
			//{value:"en-GB-Neural2-C", label: "Neural2 - FEMALE"},
			//{value:"en-GB-Neural2-D", label: "Neural2 - MALE"},
			//{value:"en-GB-Neural2-F", label: "Neural2 - FEMALE"},
			{
				value: "en-GB-News-G",
				label: "Amelia ({{ __('Female') }})"
			},
			{
				value: "en-GB-News-H",
				label: "Elise ({{ __('Female') }})"
			},
			{
				value: "en-GB-News-I",
				label: "Isabella ({{ __('Female') }})"
			},
			{
				value: "en-GB-News-J",
				label: "Jessica ({{ __('Female') }})"
			},
			{
				value: "en-GB-News-K",
				label: "Alexander ({{ __('Male') }})"
			},
			{
				value: "en-GB-News-L",
				label: "Benjamin ({{ __('Male') }})"
			},
			{
				value: "en-GB-News-M",
				label: "Charles ({{ __('Male') }})"
			},
			{
				value: "en-GB-Standard-A",
				label: "Emily ({{ __('Female') }})"
			},
			{
				value: "en-GB-Standard-B",
				label: "John ({{ __('Male') }})"
			},
			{
				value: "en-GB-Standard-C",
				label: "Mary ({{ __('Female') }})"
			},
			{
				value: "en-GB-Standard-D",
				label: "Peter ({{ __('Male') }})"
			},
			{
				value: "en-GB-Standard-F",
				label: "Sarah ({{ __('Female') }})"
			},
			{
				value: "en-GB-Wavenet-A",
				label: "Ava ({{ __('Female') }})"
			},
			{
				value: "en-GB-Wavenet-B",
				label: "David ({{ __('Male') }})"
			},
			{
				value: "en-GB-Wavenet-C",
				label: "Emily ({{ __('Female') }})"
			},
			{
				value: "en-GB-Wavenet-D",
				label: "James ({{ __('Male') }})"
			},
			{
				value: "en-GB-Wavenet-F",
				label: "Sophie ({{ __('Female') }})"
			}
		],
		"en-US": [
			//{value:"en-US-Neural2-A", label: "Neural2 - MALE"},
			//{value:"en-US-Neural2-C", label: "Neural2 - FEMALE"},
			//{value:"en-US-Neural2-D", label: "Neural2 - MALE"},
			//{value:"en-US-Neural2-E", label: "Neural2 - FEMALE"},
			//{value:"en-US-Neural2-F", label: "Neural2 - FEMALE"},
			//{value:"en-US-Neural2-G", label: "Neural2 - FEMALE"},
			//{value:"en-US-Neural2-H", label: "Neural2 - FEMALE"},
			//{value:"en-US-Neural2-I", label: "Neural2 - MALE"},
			//{value:"en-US-Neural2-J", label: "Neural2 - MALE"},
			{
				value: "en-US-News-K",
				label: "Lily ({{ __('Female') }})"
			},
			{
				value: "en-US-News-L",
				label: "Olivia ({{ __('Female') }})"
			},
			{
				value: "en-US-News-M",
				label: "Noah ({{ __('Male') }})"
			},
			{
				value: "en-US-News-N",
				label: "Oliver ({{ __('Male') }})"
			},
			//{value:"en-US-Polyglot-1", label:"John ({{ __('Male') }})"},
			{
				value: "en-US-Standard-A",
				label: "Michael ({{ __('Male') }})"
			},
			{
				value: "en-US-Standard-B",
				label: "David ({{ __('Male') }})"
			},
			{
				value: "en-US-Standard-C",
				label: "Emma ({{ __('Female') }})"
			},
			{
				value: "en-US-Standard-D",
				label: "William ({{ __('Male') }})"
			},
			{
				value: "en-US-Standard-E",
				label: "Ava ({{ __('Female') }})"
			},
			{
				value: "en-US-Standard-F",
				label: "Sophia ({{ __('Female') }})"
			},
			{
				value: "en-US-Standard-G",
				label: "Isabella ({{ __('Female') }})"
			},
			{
				value: "en-US-Standard-H",
				label: "Charlotte ({{ __('Female') }})"
			},
			{
				value: "en-US-Standard-I",
				label: "James ({{ __('Male') }})"
			},
			{
				value: "en-US-Standard-J",
				label: "Lucas ({{ __('Male') }})"
			},
			{
				value: "en-US-Studio-M",
				label: "Benjamin ({{ __('Male') }})"
			},
			{
				value: "en-US-Studio-O",
				label: "Eleanor ({{ __('Female') }})"
			},
			{
				value: "en-US-Wavenet-A",
				label: "Alexander ({{ __('Male') }})"
			},
			{
				value: "en-US-Wavenet-B",
				label: "Benjamin ({{ __('Male') }})"
			},
			{
				value: "en-US-Wavenet-C",
				label: "Emily ({{ __('Female') }})"
			},
			{
				value: "en-US-Wavenet-D",
				label: "James ({{ __('Male') }})"
			},
			{
				value: "en-US-Wavenet-E",
				label: "Ava ({{ __('Female') }})"
			},
			{
				value: "en-US-Wavenet-F",
				label: "Sophia ({{ __('Female') }})"
			},
			{
				value: "en-US-Wavenet-G",
				label: "Isabella ({{ __('Female') }})"
			},
			{
				value: "en-US-Wavenet-H",
				label: "Charlotte ({{ __('Female') }})"
			},
			{
				value: "en-US-Wavenet-I",
				label: "Alexander ({{ __('Male') }})"
			},
			{
				value: "en-US-Wavenet-J",
				label: "Lucas ({{ __('Male') }})"
			}
		],
		"fil-PH": [{
			value: "fil-PH-Standard-A",
			label: "Maria ({{ __('Female') }})"
		},
			{
				value: "fil-PH-Standard-B",
				label: "Juana ({{ __('Female') }})"
			},
			{
				value: "fil-PH-Standard-C",
				label: "Juan ({{ __('Male') }})"
			},
			{
				value: "fil-PH-Standard-D",
				label: "Pedro ({{ __('Male') }})"
			},
			{
				value: "fil-PH-Wavenet-A",
				label: "Maria ({{ __('Female') }})"
			},
			{
				value: "fil-PH-Wavenet-B",
				label: "Juana ({{ __('Female') }})"
			},
			{
				value: "fil-PH-Wavenet-C",
				label: "Juan ({{ __('Male') }})"
			},
			{
				value: "fil-PH-Wavenet-D",
				label: "Pedro ({{ __('Male') }})"
			}
			//{value:"fil-ph-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"fil-ph-Neural2-D", label: "Neural2 - MALE"}
		],
		"fi-FI": [{
			value: "fi-FI-Standard-A",
			label: "Sofia ({{ __('Female') }})"
		},
			{
				value: "fi-FI-Wavenet-A",
				label: "Sofianna ({{ __('Female') }})"
			}
		],
		"fr-CA": [
			//{value:"fr-CA-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"fr-CA-Neural2-B", label: "Neural2 - MALE"},
			//{value:"fr-CA-Neural2-C", label: "Neural2 - FEMALE"},
			//{value:"fr-CA-Neural2-D", label: "Neural2 - MALE"},
			{
				value: "fr-CA-Standard-A",
				label: "Emma ({{ __('Female') }})"
			},
			{
				value: "fr-CA-Standard-B",
				label: "Jean ({{ __('Male') }})"
			},
			{
				value: "fr-CA-Standard-C",
				label: "Gabrielle ({{ __('Female') }})"
			},
			{
				value: "fr-CA-Standard-D",
				label: "Thomas ({{ __('Male') }})"
			},
			{
				value: "fr-CA-Wavenet-A",
				label: "Amelie ({{ __('Female') }})"
			},
			{
				value: "fr-CA-Wavenet-B",
				label: "Antoine ({{ __('Male') }})"
			},
			{
				value: "fr-CA-Wavenet-C",
				label: "Gabrielle ({{ __('Female') }})"
			},
			{
				value: "fr-CA-Wavenet-D",
				label: "Thomas ({{ __('Male') }})"
			}
		],
		"fr-FR": [
			//{value:"fr-FR-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"fr-FR-Neural2-B", label: "Neural2 - MALE"},
			//{value:"fr-FR-Neural2-C", label: "Neural2 - FEMALE"},
			//{value:"fr-FR-Neural2-D", label: "Neural2 - MALE"},
			//{value:"fr-FR-Neural2-E", label: "Neural2 - FEMALE"},
			//{value:"fr-FR-Polyglot-1", label:"Jean ({{ __('Male') }})"},
			{
				value: "fr-FR-Standard-A",
				label: "Marie ({{ __('Female') }})"
			},
			{
				value: "fr-FR-Standard-B",
				label: "Pierre ({{ __('Male') }})"
			},
			{
				value: "fr-FR-Standard-C",
				label: "Sophie ({{ __('Female') }})"
			},
			{
				value: "fr-FR-Standard-D",
				label: "Paul ({{ __('Male') }})"
			},
			{
				value: "fr-FR-Standard-E",
				label: "Julie ({{ __('Female') }})"
			},
			{
				value: "fr-FR-Wavenet-A",
				label: "Elise ({{ __('Female') }})"
			},
			{
				value: "fr-FR-Wavenet-B",
				label: "Nicolas ({{ __('Male') }})"
			},
			{
				value: "fr-FR-Wavenet-C",
				label: "Clara ({{ __('Female') }})"
			},
			{
				value: "fr-FR-Wavenet-D",
				label: "Antoine ({{ __('Male') }})"
			},
			{
				value: "fr-FR-Wavenet-E",
				label: "Amelie ({{ __('Female') }})"
			}
		],
		"gl-ES": [{
			value: "gl-ES-Standard-A",
			label: "Ana ({{ __('Female') }})"
		}],
		"de-DE": [
			//{value:"de-DE-Neural2-B", label: "Neural2 - MALE"},
			//{value:"de-DE-Neural2-C", label: "Neural2 - FEMALE"},
			//{value:"de-DE-Neural2-D", label: "Neural2 - MALE"},
			//{value:"de-DE-Neural2-F", label: "Neural2 - FEMALE"},
			//{value:"de-DE-Polyglot-1", label:"Johannes ({{ __('Male') }})"},
			{
				value: "de-DE-Standard-A",
				label: "Anna ({{ __('Female') }})"
			},
			{
				value: "de-DE-Standard-B",
				label: "Max ({{ __('Male') }})"
			},
			{
				value: "de-DE-Standard-C",
				label: "Sophia ({{ __('Female') }})"
			},
			{
				value: "de-DE-Standard-D",
				label: "Paul ({{ __('Male') }})"
			},
			{
				value: "de-DE-Standard-E",
				label: "Erik ({{ __('Male') }})"
			},
			{
				value: "de-DE-Standard-F",
				label: "Lina ({{ __('Female') }})"
			},
			{
				value: "de-DE-Wavenet-A",
				label: "Eva ({{ __('Female') }})"
			},
			{
				value: "de-DE-Wavenet-B",
				label: "Felix ({{ __('Male') }})"
			},
			{
				value: "de-DE-Wavenet-C",
				label: "Emma ({{ __('Female') }})"
			},
			{
				value: "de-DE-Wavenet-D",
				label: "Lukas ({{ __('Male') }})"
			},
			{
				value: "de-DE-Wavenet-E",
				label: "Nico ({{ __('Male') }})"
			},
			{
				value: "de-DE-Wavenet-F",
				label: "Mia ({{ __('Female') }})"
			}
		],
		"el-GR": [{
			value: "el-GR-Standard-A",
			label: "Ελένη ({{ __('Female') }})"
		},
			{
				value: "el-GR-Wavenet-A",
				label: "Ελένη ({{ __('Female') }})"
			}
		],
		"gu-IN": [{
			value: "gu-IN-Standard-A",
			label: "દિવ્યા ({{ __('Female') }})"
		},
			{
				value: "gu-IN-Standard-B",
				label: "કિશોર ({{ __('Male') }})"
			},
			{
				value: "gu-IN-Wavenet-A",
				label: "દિવ્યા ({{ __('Female') }})"
			},
			{
				value: "gu-IN-Wavenet-B",
				label: "કિશોર ({{ __('Male') }})"
			}
		],
		"he-IL": [{
			value: "he-IL-Standard-A",
			label: "Tamar ({{ __('Female') }})"
		},
			{
				value: "he-IL-Standard-B",
				label: "David ({{ __('Male') }})"
			},
			{
				value: "he-IL-Standard-C",
				label: "Michal ({{ __('Female') }})"
			},
			{
				value: "he-IL-Standard-D",
				label: "Jonathan ({{ __('Male') }})"
			},
			{
				value: "he-IL-Wavenet-A",
				label: "Yael ({{ __('Female') }})"
			},
			{
				value: "he-IL-Wavenet-B",
				label: "Eli ({{ __('Male') }})"
			},
			{
				value: "he-IL-Wavenet-C",
				label: "Abigail ({{ __('Female') }})"
			},
			{
				value: "he-IL-Wavenet-D",
				label: "Alex ({{ __('Male') }})"
			}
		],
		"hi-IN": [
			//{value:"hi-IN-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"hi-IN-Neural2-B", label: "Neural2 - MALE"},
			//{value:"hi-IN-Neural2-C", label: "Neural2 - MALE"},
			//{value:"hi-IN-Neural2-D", label: "Neural2 - FEMALE"},
			{
				value: "hi-IN-Standard-A",
				label: "Aditi ({{ __('Female') }})"
			},
			{
				value: "hi-IN-Standard-B",
				label: "Abhishek ({{ __('Male') }})"
			},
			{
				value: "hi-IN-Standard-C",
				label: "Aditya ({{ __('Male') }})"
			},
			{
				value: "hi-IN-Standard-D",
				label: "Anjali ({{ __('Female') }})"
			},
			{
				value: "hi-IN-Wavenet-A",
				label: "Kiara ({{ __('Female') }})"
			},
			{
				value: "hi-IN-Wavenet-B",
				label: "Rohan ({{ __('Male') }})"
			},
			{
				value: "hi-IN-Wavenet-C",
				label: "Rishabh ({{ __('Male') }})"
			},
			{
				value: "hi-IN-Wavenet-D",
				label: "Srishti ({{ __('Female') }})"
			}
		],
		"hu-HU": [{
			value: "hu-HU-Standard-A",
			label: "Eszter ({{ __('Female') }})"
		},
			{
				value: "hu-HU-Wavenet-A",
				label: "Lilla ({{ __('Female') }})"
			}
		],
		"is-IS": [{
			value: "is-IS-Standard-A",
			label: "Guðrún ({{ __('Female') }})"
		}],
		"id-ID": [{
			value: "id-ID-Standard-A",
			label: "Amelia ({{ __('Female') }})"
		},
			{
				value: "id-ID-Standard-B",
				label: "Fajar ({{ __('Male') }})"
			},
			{
				value: "id-ID-Standard-C",
				label: "Galih ({{ __('Male') }})"
			},
			{
				value: "id-ID-Standard-D",
				label: "Kiara ({{ __('Female') }})"
			},
			{
				value: "id-ID-Wavenet-A",
				label: "Nadia ({{ __('Female') }})"
			},
			{
				value: "id-ID-Wavenet-B",
				label: "Reza ({{ __('Male') }})"
			},
			{
				value: "id-ID-Wavenet-C",
				label: "Satria ({{ __('Male') }})"
			},
			{
				value: "id-ID-Wavenet-D",
				label: "Vania ({{ __('Female') }})"
			}
		],
		"it-IT": [
			//{value:"it-IT-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"it-IT-Neural2-C", label: "Neural2 - MALE"},
			{
				value: "it-IT-Standard-A",
				label: "Chiara ({{ __('Female') }})"
			},
			{
				value: "it-IT-Standard-B",
				label: "Elisa ({{ __('Female') }})"
			},
			{
				value: "it-IT-Standard-C",
				label: "Matteo ({{ __('Male') }})"
			},
			{
				value: "it-IT-Standard-D",
				label: "Riccardo ({{ __('Male') }})"
			},
			{
				value: "it-IT-Wavenet-A",
				label: "Valentina ({{ __('Female') }})"
			},
			{
				value: "it-IT-Wavenet-B",
				label: "Vittoria ({{ __('Female') }})"
			},
			{
				value: "it-IT-Wavenet-C",
				label: "Andrea ({{ __('Male') }})"
			},
			{
				value: "it-IT-Wavenet-D",
				label: "Luca ({{ __('Male') }})"
			}
		],
		"ja-JP": [
			//{value:"ja-JP-Neural2-B", label: "Neural2 - FEMALE"},
			//{value:"ja-JP-Neural2-C", label: "Neural2 - MALE"},
			//{value:"ja-JP-Neural2-D", label: "Neural2 - MALE"},
			{
				value: "ja-JP-Standard-A",
				label: "Akane ({{ __('Female') }})"
			},
			{
				value: "ja-JP-Standard-B",
				label: "Emi ({{ __('Female') }})"
			},
			{
				value: "ja-JP-Standard-C",
				label: "Daisuke ({{ __('Male') }})"
			},
			{
				value: "ja-JP-Standard-D",
				label: "Kento ({{ __('Male') }})"
			},
			{
				value: "ja-JP-Wavenet-A",
				label: "Haruka ({{ __('Female') }})"
			},
			{
				value: "ja-JP-Wavenet-B",
				label: "Rin ({{ __('Female') }})"
			},
			{
				value: "ja-JP-Wavenet-C",
				label: "Shun ({{ __('Male') }})"
			},
			{
				value: "ja-JP-Wavenet-D",
				label: "Yuta ({{ __('Male') }})"
			}
		],
		"kn-IN": [{
			value: "kn-IN-Standard-A",
			label: "Dhanya ({{ __('Female') }})"
		},
			{
				value: "kn-IN-Standard-B",
				label: "Keerthi ({{ __('Male') }})"
			},
			{
				value: "kn-IN-Wavenet-A",
				label: "Meena ({{ __('Female') }})"
			},
			{
				value: "kn-IN-Wavenet-B",
				label: "Nandini ({{ __('Male') }})"
			}
		],
		"ko-KR": [
			//{value:"ko-KR-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"ko-KR-Neural2-B", label: "Neural2 - FEMALE"},
			//{value:"ko-KR-Neural2-C", label: "Neural2 - MALE"},
			{
				value: "ko-KR-Standard-A",
				label: "So-young ({{ __('Female') }})"
			},
			{
				value: "ko-KR-Standard-B",
				label: "Se-yeon ({{ __('Female') }})"
			},
			{
				value: "ko-KR-Standard-C",
				label: "Min-soo ({{ __('Male') }})"
			},
			{
				value: "ko-KR-Standard-D",
				label: "Seung-woo ({{ __('Male') }})"
			},
			{
				value: "ko-KR-Wavenet-A",
				label: "Ji-soo ({{ __('Female') }})"
			},
			{
				value: "ko-KR-Wavenet-B",
				label: "Yoon-a ({{ __('Female') }})"
			},
			{
				value: "ko-KR-Wavenet-C",
				label: "Tae-hyun ({{ __('Male') }})"
			},
			{
				value: "ko-KR-Wavenet-D",
				label: "Jun-ho ({{ __('Male') }})"
			}
		],
		"lv-LV": [{
			value: "lv-LV-Standard-A",
			label: "Raivis ({{ __('Male') }})"
		}],
		"lv-LT": [{
			value: "lv-LT-Standard-A",
			label: "Raivis ({{ __('Male') }})"
		}],
		"ms-MY": [{
			value: "ms-MY-Standard-A",
			label: "Amira ({{ __('Female') }})"
		},
			{
				value: "ms-MY-Standard-B",
				label: "Danial ({{ __('Male') }})"
			},
			{
				value: "ms-MY-Standard-C",
				label: "Eira ({{ __('Female') }})"
			},
			{
				value: "ms-MY-Standard-D",
				label: "Farhan ({{ __('Male') }})"
			},
			{
				value: "ms-MY-Wavenet-A",
				label: "Hana ({{ __('Female') }})"
			},
			{
				value: "ms-MY-Wavenet-B",
				label: "Irfan ({{ __('Male') }})"
			},
			{
				value: "ms-MY-Wavenet-C",
				label: "Janna ({{ __('Female') }})"
			},
			{
				value: "ms-MY-Wavenet-D",
				label: "Khairul ({{ __('Male') }})"
			}
		],
		"ml-IN": [{
			value: "ml-IN-Standard-A",
			label: "Aishwarya ({{ __('Female') }})"
		},
			{
				value: "ml-IN-Standard-B",
				label: "Dhruv ({{ __('Male') }})"
			},
			{
				value: "ml-IN-Wavenet-A",
				label: "Deepthi ({{ __('Female') }})"
			},
			{
				value: "ml-IN-Wavenet-B",
				label: "Gautam ({{ __('Male') }})"
			},
			{
				value: "ml-IN-Wavenet-C",
				label: "Isha ({{ __('Female') }})"
			},
			{
				value: "ml-IN-Wavenet-D",
				label: "Kabir ({{ __('Male') }})"
			}
		],
		"cmn-CN": [{
			value: "cmn-CN-Standard-A",
			label: "Xiaomei ({{ __('Female') }})"
		},
			{
				value: "cmn-CN-Standard-B",
				label: "Lijun ({{ __('Male') }})"
			},
			{
				value: "cmn-CN-Standard-C",
				label: "Minghao ({{ __('Male') }})"
			},
			{
				value: "cmn-CN-Standard-D",
				label: "Yingying ({{ __('Female') }})"
			},
			{
				value: "cmn-CN-Wavenet-A",
				label: "Shanshan ({{ __('Female') }})"
			},
			{
				value: "cmn-CN-Wavenet-B",
				label: "Chenchen ({{ __('Male') }})"
			},
			{
				value: "cmn-CN-Wavenet-C",
				label: "Jiahao ({{ __('Male') }})"
			},
			{
				value: "cmn-CN-Wavenet-D",
				label: "Yueyu ({{ __('Female') }})"
			}
		],
		"cmn-TW": [{
			value: "cmn-TW-Standard-A",
			label: "Jingwen ({{ __('Female') }})"
		},
			{
				value: "cmn-TW-Standard-B",
				label: "Jinghao ({{ __('Male') }})"
			},
			{
				value: "cmn-TW-Standard-C",
				label: "Tingting ({{ __('Female') }})"
			},
			{
				value: "cmn-TW-Wavenet-A",
				label: "Yunyun ({{ __('Female') }})"
			},
			{
				value: "cmn-TW-Wavenet-B",
				label: "Zhenghao ({{ __('Male') }})"
			},
			{
				value: "cmn-TW-Wavenet-C",
				label: "Yuehan ({{ __('Female') }})"
			}
		],
		"mr-IN": [{
			value: "mr-IN-Standard-A",
			label: "Anjali ({{ __('Female') }})"
		},
			{
				value: "mr-IN-Standard-B",
				label: "Aditya ({{ __('Male') }})"
			},
			{
				value: "mr-IN-Standard-C",
				label: "Dipti ({{ __('Female') }})"
			},
			{
				value: "mr-IN-Wavenet-A",
				label: "Gauri ({{ __('Female') }})"
			},
			{
				value: "mr-IN-Wavenet-B",
				label: "Harsh ({{ __('Male') }})"
			},
			{
				value: "mr-IN-Wavenet-C",
				label: "Ishita ({{ __('Female') }})"
			}
		],
		"nb-NO": [{
			value: "nb-NO-Standard-A",
			label: "Ingrid ({{ __('Female') }})"
		},
			{
				value: "nb-NO-Standard-B",
				label: "Jonas ({{ __('Male') }})"
			},
			{
				value: "nb-NO-Standard-C",
				label: "Marit ({{ __('Female') }})"
			},
			{
				value: "nb-NO-Standard-D",
				label: "Olav ({{ __('Male') }})"
			},
			{
				value: "nb-NO-Standard-E",
				label: "Silje ({{ __('Female') }})"
			},
			{
				value: "nb-NO-Wavenet-A",
				label: "Astrid ({{ __('Female') }})"
			},
			{
				value: "nb-NO-Wavenet-B",
				label: "Eirik ({{ __('Male') }})"
			},
			{
				value: "nb-NO-Wavenet-C",
				label: "Inger ({{ __('Female') }})"
			},
			{
				value: "nb-NO-Wavenet-D",
				label: "Kristian ({{ __('Male') }})"
			},
			{
				value: "nb-NO-Wavenet-E",
				label: "Trine ({{ __('Female') }})"
			}
		],
		"pl-PL": [{
			value: "pl-PL-Standard-A",
			label: "Agata ({{ __('Female') }})"
		},
			{
				value: "pl-PL-Standard-B",
				label: "Bartosz ({{ __('Male') }})"
			},
			{
				value: "pl-PL-Standard-C",
				label: "Kamil ({{ __('Male') }})"
			},
			{
				value: "pl-PL-Standard-D",
				label: "Julia ({{ __('Female') }})"
			},
			{
				value: "pl-PL-Standard-E",
				label: "Magdalena ({{ __('Female') }})"
			},
			{
				value: "pl-PL-Wavenet-A",
				label: "Natalia ({{ __('Female') }})"
			},
			{
				value: "pl-PL-Wavenet-B",
				label: "Paweł ({{ __('Male') }})"
			},
			{
				value: "pl-PL-Wavenet-C",
				label: "Tomasz ({{ __('Male') }})"
			},
			{
				value: "pl-PL-Wavenet-D",
				label: "Zofia ({{ __('Female') }})"
			},
			{
				value: "pl-PL-Wavenet-E",
				label: "Wiktoria ({{ __('Female') }})"
			}
		],
		"pt-BR": [
			//{value:"pt-BR-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"pt-BR-Neural2-B", label: "Neural2 - MALE"},
			//{value:"pt-BR-Neural2-C", label: "Neural2 - FEMALE"},
			{
				value: "pt-BR-Standard-A",
				label: "Ana ({{ __('Female') }})"
			},
			{
				value: "pt-BR-Standard-B",
				label: "Carlos ({{ __('Male') }})"
			},
			{
				value: "pt-BR-Standard-C",
				label: "Maria ({{ __('Female') }})"
			},
			{
				value: "pt-BR-Wavenet-A",
				label: "Julia ({{ __('Female') }})"
			},
			{
				value: "pt-BR-Wavenet-B",
				label: "João ({{ __('Male') }})"
			},
			{
				value: "pt-BR-Wavenet-C",
				label: "Fernanda ({{ __('Female') }})"
			}
		],
		"pt-PT": [{
			value: "pt-PT-Standard-A",
			label: "Maria ({{ __('Female') }})"
		},
			{
				value: "pt-PT-Standard-B",
				label: "José ({{ __('Male') }})"
			},
			{
				value: "pt-PT-Standard-C",
				label: "Luís ({{ __('Male') }})"
			},
			{
				value: "pt-PT-Standard-D",
				label: "Ana ({{ __('Female') }})"
			},
			{
				value: "pt-PT-Wavenet-A",
				label: "Catarina ({{ __('Female') }})"
			},
			{
				value: "pt-PT-Wavenet-B",
				label: "Miguel ({{ __('Male') }})"
			},
			{
				value: "pt-PT-Wavenet-C",
				label: "João ({{ __('Male') }})"
			},
			{
				value: "pt-PT-Wavenet-D",
				label: "Marta ({{ __('Female') }})"
			}
		],
		"pa-IN": [{
			value: "pa-IN-Standard-A",
			label: "Harpreet ({{ __('Female') }})"
		},
			{
				value: "pa-IN-Standard-B",
				label: "Gurpreet ({{ __('Male') }})"
			},
			{
				value: "pa-IN-Standard-C",
				label: "Jasmine ({{ __('Female') }})"
			},
			{
				value: "pa-IN-Standard-D",
				label: "Rahul ({{ __('Male') }})"
			},
			{
				value: "pa-IN-Wavenet-A",
				label: "Simran ({{ __('Female') }})"
			},
			{
				value: "pa-IN-Wavenet-B",
				label: "Amardeep ({{ __('Male') }})"
			},
			{
				value: "pa-IN-Wavenet-C",
				label: "Kiran ({{ __('Female') }})"
			},
			{
				value: "pa-IN-Wavenet-D",
				label: "Raj ({{ __('Male') }})"
			}
		],
		"ro-RO": [{
			value: "ro-RO-Standard-A",
			label: "Maria ({{ __('Female') }})"
		},
			{
				value: "ro-RO-Wavenet-A",
				label: "Ioana ({{ __('Female') }})"
			}
		],
		"ru-RU": [{
			value: "ru-RU-Standard-A",
			label: "Anastasia"
		},
			{
				value: "ru-RU-Standard-B",
				label: "Alexander"
			},
			{
				value: "ru-RU-Standard-C",
				label: "Elizabeth"
			},
			{
				value: "ru-RU-Standard-D",
				label: "Michael"
			},
			{
				value: "ru-RU-Standard-E",
				label: "Victoria"
			},
			{
				value: "ru-RU-Wavenet-A",
				label: "Daria"
			},
			{
				value: "ru-RU-Wavenet-B",
				label: "Dmitry"
			},
			{
				value: "ru-RU-Wavenet-C",
				label: "Kristina"
			},
			{
				value: "ru-RU-Wavenet-D",
				label: "Ivan"
			},
			{
				value: "ru-RU-Wavenet-E",
				label: "Sophia"
			}
		],
		"sr-RS": [{
			value: "sr-RS-Standard-A",
			label: "Ana"
		}],
		"sk-SK": [{
			value: "sk-SK-Standard-A",
			label: "Mária ({{ __('Female') }})"
		},
			{
				value: "sk-SK-Wavenet-A",
				label: "Zuzana ({{ __('Female') }})"
			}
		],
		"es-ES": [
			//{value:"es-ES-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"es-ES-Neural2-B", label: "Neural2 - MALE"},
			//{value:"es-ES-Neural2-C", label: "Neural2 - FEMALE"},
			//{value:"es-ES-Neural2-D", label: "Neural2 - FEMALE"},
			//{value:"es-ES-Neural2-E", label: "Neural2 - FEMALE"},
			//{value:"es-ES-Neural2-F", label: "Neural2 - MALE"},
			//{value: "es-ES-Polyglot-1", label: "Juan ({{ __('Male') }})"},
			{
				value: "es-ES-Standard-A",
				label: "María ({{ __('Female') }})"
			},
			{
				value: "es-ES-Standard-B",
				label: "José ({{ __('Male') }})"
			},
			{
				value: "es-ES-Standard-C",
				label: "Ana ({{ __('Female') }})"
			},
			{
				value: "es-ES-Standard-D",
				label: "Isabel ({{ __('Female') }})"
			},
			{
				value: "es-ES-Wavenet-B",
				label: "Pedro ({{ __('Male') }})"
			},
			{
				value: "es-ES-Wavenet-C",
				label: "Laura ({{ __('Female') }})"
			},
			{
				value: "es-ES-Wavenet-D",
				label: "Julia ({{ __('Female') }})"
			}

		],
		"es-US": [
			//{value:"es-US-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"es-US-Neural2-B", label: "Neural2 - MALE"},
			//{value:"es-US-Neural2-C", label: "Neural2 - MALE"},
			{
				value: "es-US-News-D",
				label: "Diego ({{ __('Male') }})"
			},
			{
				value: "es-US-News-E",
				label: "Eduardo ({{ __('Male') }})"
			},
			{
				value: "es-US-News-F",
				label: "Fátima ({{ __('Female') }})"
			},
			{
				value: "es-US-News-G",
				label: "Gabriela ({{ __('Female') }})"
			},
			//{value:"es-US-Polyglot-1", label: "Juan ({{ __('Male') }})"},
			{
				value: "es-US-Standard-A",
				label: "Ana ({{ __('Female') }})"
			},
			{
				value: "es-US-Standard-B",
				label: "José ({{ __('Male') }})"
			},
			{
				value: "es-US-Standard-C",
				label: "Carlos ({{ __('Male') }})"
			},
			{
				value: "es-US-Studio-B",
				label: "Miguel ({{ __('Male') }})"
			},
			{
				value: "es-US-Wavenet-A",
				label: "Laura ({{ __('Female') }})"
			},
			{
				value: "es-US-Wavenet-B",
				label: "Pedro ({{ __('Male') }})"
			},
			{
				value: "es-US-Wavenet-C",
				label: "Pablo ({{ __('Male') }})"
			}
		],
		"sv-SE": [{
			value: "sv-SE-Standard-A",
			label: "Ebba ({{ __('Female') }})"
		},
			{
				value: "sv-SE-Standard-B",
				label: "Saga ({{ __('Female') }})"
			},
			{
				value: "sv-SE-Standard-C",
				label: "Linnea ({{ __('Female') }})"
			},
			{
				value: "sv-SE-Standard-D",
				label: "Erik ({{ __('Male') }})"
			},
			{
				value: "sv-SE-Standard-E",
				label: "Anton ({{ __('Male') }})"
			},
			{
				value: "sv-SE-Wavenet-A",
				label: "Astrid ({{ __('Female') }})"
			},
			{
				value: "sv-SE-Wavenet-B",
				label: "Elin ({{ __('Female') }})"
			},
			{
				value: "sv-SE-Wavenet-C",
				label: "Oskar ({{ __('Male') }})"
			},
			{
				value: "sv-SE-Wavenet-D",
				label: "Hanna ({{ __('Female') }})"
			},
			{
				value: "sv-SE-Wavenet-E",
				label: "Felix ({{ __('Male') }})"
			}
		],
		"ta-IN": [{
			value: "ta-IN-Standard-A",
			label: "Anjali ({{ __('Female') }})"
		},
			{
				value: "ta-IN-Standard-B",
				label: "Karthik ({{ __('Male') }})"
			},
			{
				value: "ta-IN-Standard-C",
				label: "Priya ({{ __('Female') }})"
			},
			{
				value: "ta-IN-Standard-D",
				label: "Ravi ({{ __('Male') }})"
			},
			{
				value: "ta-IN-Wavenet-A",
				label: "Lakshmi ({{ __('Female') }})"
			},
			{
				value: "ta-IN-Wavenet-B",
				label: "Suresh ({{ __('Male') }})"
			},
			{
				value: "ta-IN-Wavenet-C",
				label: "Uma ({{ __('Female') }})"
			},
			{
				value: "ta-IN-Wavenet-D",
				label: "Venkatesh ({{ __('Male') }})"
			}
		],
		"te-IN": [{
			value: "-IN-Standard-A",
			label: "Anjali - ({{ __('Female') }})"
		},
			{
				value: "-IN-Standard-B",
				label: "Karthik - ({{ __('Male') }})"
			}
		],
		"th-TH": [
			//{value:"th-TH-Neural2-C", label: "Neural2 - FEMALE"},
			{
				value: "th-TH-Standard-A",
				label: "Ariya - ({{ __('Female') }})"
			}
		],
		"tr-TR": [{
			value: "tr-TR-Standard-A",
			label: "Ayşe ({{ __('Female') }})"
		},
			{
				value: "tr-TR-Standard-B",
				label: "Berk ({{ __('Male') }})"
			},
			{
				value: "tr-TR-Standard-C",
				label: "Cansu ({{ __('Female') }})"
			},
			{
				value: "tr-TR-Standard-D",
				label: "Deniz ({{ __('Female') }})"
			},
			{
				value: "tr-TR-Standard-E",
				label: "Emre ({{ __('Male') }})"
			},
			{
				value: "tr-TR-Wavenet-A",
				label: "Gül ({{ __('Female') }})"
			},
			{
				value: "tr-TR-Wavenet-B",
				label: "Mert ({{ __('Male') }})"
			},
			{
				value: "tr-TR-Wavenet-C",
				label: "Nilay ({{ __('Female') }})"
			},
			{
				value: "tr-TR-Wavenet-D",
				label: "Selin ({{ __('Female') }})"
			},
			{
				value: "tr-TR-Wavenet-E",
				label: "Tolga ({{ __('Male') }})"
			}
		],
		"uk-UA": [{
			value: "uk-UA-Standard-A",
			label: "Anya - ({{ __('Female') }})"
		},
			{
				value: "uk-UA-Wavenet-A",
				label: "Dasha - ({{ __('Female') }})"
			}
		],
		"vi-VN": [
			//{value:"vi-VN-Neural2-A", label: "Neural2 - FEMALE"},
			//{value:"vi-VN-Neural2-D", label: "Neural2 - MALE"},
			{
				value: "vi-VN-Standard-A",
				label: "Mai ({{ __('Female') }})"
			},
			{
				value: "vi-VN-Standard-B",
				label: "Nam ({{ __('Male') }})"
			},
			{
				value: "vi-VN-Standard-C",
				label: "Hoa ({{ __('Female') }})"
			},
			{
				value: "vi-VN-Standard-D",
				label: "Huy ({{ __('Male') }})"
			},
			{
				value: "vi-VN-Wavenet-A",
				label: "Lan ({{ __('Female') }})"
			},
			{
				value: "vi-VN-Wavenet-B",
				label: "Son ({{ __('Male') }})"
			},
			{
				value: "vi-VN-Wavenet-C",
				label: "Thao ({{ __('Female') }})"
			},
			{
				value: "vi-VN-Wavenet-D",
				label: "Tuan ({{ __('Male') }})"
			}
		]
	}
	const azureVoiceData = {
		"af-ZA": [{
			value: "af-ZA-AdriNeural",
			label: "Adri (Female)"
		},
			{
				value: "af-ZA-WillemNeural",
				label: "Willem (Male)"
			}
		],
		"am-ET": [{
			value: "am-ET-MekdesNeural",
			label: "Mekdes (Female)"
		},
			{
				value: "am-ET-AmehaNeural",
				label: "Ameha (Male)"
			}
		],
		"ar-XA": [{
			value: "ar-AE-FatimaNeural",
			label: "Fatima (Female)"
		},
			{
				value: "ar-AE-HamdanNeural",
				label: "Hamdan (Male)"
			},
			{
				value: "ar-BH-LailaNeural",
				label: "Laila (Female)"
			},
			{
				value: "ar-BH-AliNeural",
				label: "Ali (Male)"
			},
			{
				value: "ar-DZ-AminaNeural",
				label: "Amina (Female)"
			},
			{
				value: "ar-DZ-IsmaelNeural",
				label: "Ismael (Male)"
			}, {
				value: "ar-EG-SalmaNeural",
				label: "Salma (Female)"
			},
			{
				value: "ar-EG-ShakirNeural",
				label: "Shakir (Male)"
			},
			{
				value: "ar-IQ-RanaNeural",
				label: "Rana (Female)"
			},
			{
				value: "ar-IQ-BasselNeural",
				label: "Bassel (Male)"
			},
			{
				value: "ar-JO-SanaNeural",
				label: "Sana (Female)"
			},
			{
				value: "ar-JO-TaimNeural",
				label: "Taim (Male)"
			},
			{
				value: "ar-KW-NouraNeural",
				label: "Noura (Female)"
			},
			{
				value: "ar-KW-FahedNeural",
				label: "Fahed (Male)"
			}, {
				value: "ar-LB-LaylaNeural",
				label: "Layla (Female)"
			},
			{
				value: "ar-LB-RamiNeural",
				label: "Rami (Male)"
			},
			{
				value: "ar-LY-ImanNeural",
				label: "Iman (Female)"
			},
			{
				value: "ar-LY-OmarNeural",
				label: "Omar (Male)"
			},
			{
				value: "ar-MA-MounaNeural",
				label: "Mouna (Female)"
			},
			{
				value: "ar-MA-JamalNeural",
				label: "Jamal (Male)"
			},
			{
				value: "ar-OM-AyshaNeural",
				label: "Aysha (Female)"
			},
			{
				value: "ar-OM-AbdullahNeural",
				label: "Abdullah (Male)"
			}, {
				value: "ar-QA-AmalNeural",
				label: "Amal (Female)"
			},
			{
				value: "ar-QA-MoazNeural",
				label: "Moaz (Male)"
			},
			{
				value: "ar-SA-ZariyahNeural",
				label: "Zariyah (Female)"
			},
			{
				value: "ar-SA-HamedNeural",
				label: "Hamed (Male)"
			},
			{
				value: "ar-SY-AmanyNeural",
				label: "Amany (Female)"
			},
			{
				value: "ar-SY-LaithNeural",
				label: "Laith (Male)"
			}, {
				value: "ar-TN-ReemNeural",
				label: "Reem (Female)"
			},
			{
				value: "ar-TN-HediNeural",
				label: "Hedi (Male)"
			},
			{
				value: "ar-YE-MaryamNeural",
				label: "Maryam (Female)"
			},
			{
				value: "ar-YE-SalehNeural",
				label: "Saleh (Male)"
			}
		],
		"az-AZ": [{
			value: "az-AZ-BanuNeural2",
			label: "Banu (Female)"
		},
			{
				value: "az-AZ-BabekNeural2",
				label: "Babek (Male)"
			}
		],
		"bg-BG": [{
			value: "bg-BG-KalinaNeural",
			label: "Kalina (Female)"
		},
			{
				value: "bg-BG-BorislavNeural",
				label: "Borislav (Male)"
			}
		],
		"bn-BD": [{
			value: "bn-BD-NabanitaNeural2",
			label: "Nabanita (Female)"
		},
			{
				value: "bn-BD-PradeepNeural2",
				label: "Pradeep (Male)"
			}
		],
		"bn-IN": [{
			value: "bn-IN-TanishaaNeural2",
			label: "Tanishaa (Female)"
		},
			{
				value: "bn-IN-BashkarNeural2",
				label: "Bashkar (Male)"
			}
		],
		"bs-BA": [{
			value: "bs-BA-VesnaNeural2",
			label: "Vesna (Female)"
		},
			{
				value: "bs-BA-GoranNeural2",
				label: "Goran (Male)"
			}
		],
		"ca-ES": [{
			value: "ca-ES-JoanaNeural",
			label: "Joana (Female)"
		},
			{
				value: "ca-ES-EnricNeural",
				label: "Enric (Male)"
			},
			{
				value: "ca-ES-AlbaNeural",
				label: "Alba (Female)"
			}
		],
		"cs-CZ": [{
			value: "cs-CZ-VlastaNeural",
			label: "Vlasta (Female)"
		},
			{
				value: "cs-CZ-AntoninNeural",
				label: "Antonin (Male)"
			}
		],
		"cy-GB": [{
			value: "cy-GB-NiaNeural2",
			label: "Nia (Female)"
		},
			{
				value: "cy-GB-AledNeural2",
				label: "Aled (Male)"
			}
		],
		"da-DK": [{
			value: "da-DK-ChristelNeural",
			label: "Christel (Female)"
		},
			{
				value: "da-DK-JeppeNeural",
				label: "Jeppe (Male)"
			}
		],
		"de-AT": [{
			value: "de-AT-IngridNeural",
			label: "Ingrid (Female)"
		},
			{
				value: "de-AT-JonasNeural",
				label: "Jonas (Male)"
			}
		],
		"de-CH": [{
			value: "de-CH-LeniNeural",
			label: "Leni (Female)"
		},
			{
				value: "de-CH-JanNeural",
				label: "Jan (Male)"
			}
		],
		"de-DE": [{
			value: "de-DE-KatjaNeural",
			label: "Katja (Female)"
		},
			{
				value: "de-DE-ConradNeural1",
				label: "Conrad (Male)"
			},
			{
				value: "de-DE-AmalaNeural",
				label: "Amala (Female)"
			},
			{
				value: "de-DE-BerndNeural",
				label: "Bernd (Male)"
			},
			{
				value: "de-DE-ChristophNeural",
				label: "Christoph (Male)"
			},
			{
				value: "de-DE-ElkeNeural",
				label: "Elke (Female)"
			},
			{
				value: "de-DE-GiselaNeural",
				label: "Gisela (Female, Child)"
			},
			{
				value: "de-DE-KasperNeural",
				label: "Kasper (Male)"
			},
			{
				value: "de-DE-KillianNeural",
				label: "Killian (Male)"
			},
			{
				value: "de-DE-KlarissaNeural",
				label: "Klarissa (Female)"
			},
			{
				value: "de-DE-KlausNeural",
				label: "Klaus (Male)"
			},
			{
				value: "de-DE-LouisaNeural",
				label: "Louisa (Female)"
			},
			{
				value: "de-DE-MajaNeural",
				label: "Maja (Female)"
			},
			{
				value: "de-DE-RalfNeural",
				label: "Ralf (Male)"
			},
			{
				value: "de-DE-TanjaNeural",
				label: "Tanja (Female)"
			},
			{
				value: "de-DE-FlorianMultilingualNeural3",
				label: "Florian (Male)"
			},
			{
				value: "de-DE-SeraphinaMultilingualNeural3",
				label: "Seraphina (Female)"
			}
		],
		"el-GR": [{
			value: "el-GR-AthinaNeural",
			label: "Athina (Female)"
		},
			{
				value: "el-GR-NestorasNeural",
				label: "Nestoras (Male)"
			}
		],
		"en-AU": [{
			value: "en-AU-NatashaNeural",
			label: "Natasha (Female)"
		},
			{
				value: "en-AU-WilliamNeural",
				label: "William (Male)"
			},
			{
				value: "en-AU-AnnetteNeural",
				label: "Annette (Female)"
			},
			{
				value: "en-AU-CarlyNeural",
				label: "Carly (Female)"
			},
			{
				value: "en-AU-DarrenNeural",
				label: "Darren (Male)"
			},
			{
				value: "en-AU-DuncanNeural",
				label: "Duncan (Male)"
			},
			{
				value: "en-AU-ElsieNeural",
				label: "Elsie (Female)"
			},
			{
				value: "en-AU-FreyaNeural",
				label: "Freya (Female)"
			},
			{
				value: "en-AU-JoanneNeural",
				label: "Joanne (Female)"
			},
			{
				value: "en-AU-KenNeural",
				label: "Ken (Male)"
			},
			{
				value: "en-AU-KimNeural",
				label: "Kim (Female)"
			},
			{
				value: "en-AU-NeilNeural",
				label: "Neil (Male)"
			},
			{
				value: "en-AU-TimNeural",
				label: "Tim (Male)"
			},
			{
				value: "en-AU-TinaNeural",
				label: "Tina (Female)"
			}
		],
		"en-CA": [{
			value: "en-CA-ClaraNeural",
			label: "Clara (Female)"
		},
			{
				value: "en-CA-LiamNeural",
				label: "Liam (Male)"
			}
		],
		"en-GB": [{
			value: "en-GB-SoniaNeural",
			label: "Sonia (Female)"
		},
			{
				value: "en-GB-RyanNeural",
				label: "Ryan (Male)"
			},
			{
				value: "en-GB-LibbyNeural",
				label: "Libby (Female)"
			},
			{
				value: "en-GB-AbbiNeural",
				label: "Abbi (Female)"
			},
			{
				value: "en-GB-AlfieNeural",
				label: "Alfie (Male)"
			},
			{
				value: "en-GB-BellaNeural",
				label: "Bella (Female)"
			},
			{
				value: "en-GB-ElliotNeural",
				label: "Elliot (Male)"
			},
			{
				value: "en-GB-EthanNeural",
				label: "Ethan (Male)"
			},
			{
				value: "en-GB-HollieNeural",
				label: "Hollie (Female)"
			},
			{
				value: "en-GB-MaisieNeural",
				label: "Maisie (Female, Child)"
			},
			{
				value: "en-GB-NoahNeural",
				label: "Noah (Male)"
			},
			{
				value: "en-GB-OliverNeural",
				label: "Oliver (Male)"
			},
			{
				value: "en-GB-OliviaNeural",
				label: "Olivia (Female)"
			},
			{
				value: "en-GB-ThomasNeural",
				label: "Thomas (Male)"
			}
		],
		"en-HK": [{
			value: "en-HK-YanNeural",
			label: "Yan (Female)"
		},
			{
				value: "en-HK-SamNeural",
				label: "Sam (Male)"
			}
		],
		"en-IE": [{
			value: "en-IE-EmilyNeural",
			label: "Emily (Female)"
		},
			{
				value: "en-IE-ConnorNeural",
				label: "Connor (Male)"
			}
		],
		"en-IN": [{
			value: "en-IN-NeerjaNeural",
			label: "Neerja (Female)"
		},
			{
				value: "en-IN-PrabhatNeural",
				label: "Prabhat (Male)"
			}
		],
		"en-KE": [{
			value: "en-KE-AsiliaNeural",
			label: "Asilia (Female)"
		},
			{
				value: "en-KE-ChilembaNeural",
				label: "Chilemba (Male)"
			}
		],
		"en-NG": [{
			value: "en-NG-EzinneNeural",
			label: "Ezinne (Female)"
		},
			{
				value: "en-NG-AbeoNeural",
				label: "Abeo (Male)"
			}
		],
		"en-NZ": [{
			value: "en-NZ-MollyNeural",
			label: "Molly (Female)"
		},
			{
				value: "en-NZ-MitchellNeural",
				label: "Mitchell (Male)"
			}
		],
		"en-PH": [{
			value: "en-PH-RosaNeural",
			label: "Rosa (Female)"
		},
			{
				value: "en-PH-JamesNeural",
				label: "James (Male)"
			}
		],
		"en-SG": [{
			value: "en-SG-LunaNeural",
			label: "Luna (Female)"
		},
			{
				value: "en-SG-WayneNeural",
				label: "Wayne (Male)"
			}
		],
		"en-TZ": [{
			value: "en-TZ-ImaniNeural",
			label: "Imani (Female)"
		},
			{
				value: "en-TZ-ElimuNeural",
				label: "Elimu (Male)"
			}
		],
		"en-US": [{
			value: "en-US-AvaNeural",
			label: "Ava (Female)"
		},
			{
				value: "en-US-AndrewNeural",
				label: "Andrew (Male)"
			},
			{
				value: "en-US-EmmaNeural",
				label: "Emma (Female)"
			},
			{
				value: "en-US-BrianNeural",
				label: "Brian (Male)"
			},
			{
				value: "en-US-JennyNeural",
				label: "Jenny (Female)"
			},
			{
				value: "en-US-GuyNeural",
				label: "Guy (Male)"
			},
			{
				value: "en-US-AriaNeural",
				label: "Aria (Female)"
			},
			{
				value: "en-US-DavisNeural",
				label: "Davis (Male)"
			},
			{
				value: "en-US-JaneNeural",
				label: "Jane (Female)"
			},
			{
				value: "en-US-JasonNeural",
				label: "Jason (Male)"
			},
			{
				value: "en-US-SaraNeural",
				label: "Sara (Female)"
			},
			{
				value: "en-US-TonyNeural",
				label: "Tony (Male)"
			},
			{
				value: "en-US-NancyNeural",
				label: "Nancy (Female)"
			},
			{
				value: "en-US-AmberNeural",
				label: "Amber (Female)"
			},
			{
				value: "en-US-AnaNeural",
				label: "Ana (Female, Child)"
			},
			{
				value: "en-US-AshleyNeural",
				label: "Ashley (Female)"
			},
			{
				value: "en-US-BrandonNeural",
				label: "Brandon (Male)"
			},
			{
				value: "en-US-ChristopherNeural",
				label: "Christopher (Male)"
			},
			{
				value: "en-US-CoraNeural",
				label: "Cora (Female)"
			},
			{
				value: "en-US-ElizabethNeural",
				label: "Elizabeth (Female)"
			},
			{
				value: "en-US-EricNeural",
				label: "Eric (Male)"
			},
			{
				value: "en-US-JacobNeural",
				label: "Jacob (Male)"
			},
			{
				value: "en-US-JennyMultilingualNeural3",
				label: "Jenny (Female)"
			},
			{
				value: "en-US-MichelleNeural",
				label: "Michelle (Female)"
			},
			{
				value: "en-US-MonicaNeural",
				label: "Monica (Female)"
			},
			{
				value: "en-US-RogerNeural",
				label: "Roger (Male)"
			},
			{
				value: "en-US-RyanMultilingualNeural3",
				label: "Ryan (Male)"
			},
			{
				value: "en-US-SteffanNeural",
				label: "Steffan (Male)"
			},
			{
				value: "en-US-AIGenerate1Neural1",
				label: "AIGenerate1 (Male)"
			},
			{
				value: "en-US-AIGenerate2Neural1",
				label: "AIGenerate2 (Female)"
			},
			{
				value: "en-US-AndrewMultilingualNeural3",
				label: "Andrew (Male)"
			},
			{
				value: "en-US-AvaMultilingualNeural3",
				label: "Ava (Female)"
			},
			{
				value: "en-US-BlueNeural1",
				label: "Blue (Neutral)"
			},
			{
				value: "en-US-BrianMultilingualNeural3",
				label: "Brian (Male)"
			},
			{
				value: "en-US-EmmaMultilingualNeural3",
				label: "Emma (Female)"
			},
			{
				value: "en-US-AlloyMultilingualNeural4",
				label: "Alloy (Male)"
			},
			{
				value: "en-US-EchoMultilingualNeural4",
				label: "Echo (Male)"
			},
			{
				value: "en-US-FableMultilingualNeural4",
				label: "Fable (Neutral)"
			},
			{
				value: "en-US-OnyxMultilingualNeural4",
				label: "Onyx (Male)"
			},
			{
				value: "en-US-NovaMultilingualNeural4",
				label: "Nova (Female)"
			},
			{
				value: "en-US-ShimmerMultilingualNeural4",
				label: "Shimmer (Female)"
			},
			{
				value: "en-US-AlloyMultilingualNeuralHD4",
				label: "Alloy (Male)"
			},
			{
				value: "en-US-EchoMultilingualNeuralHD4",
				label: "Echo (Male)"
			},
			{
				value: "en-US-FableMultilingualNeuralHD4",
				label: "Fable (Neutral)"
			},
			{
				value: "en-US-OnyxMultilingualNeuralHD4",
				label: "Onyx (Male)"
			},
			{
				value: "en-US-NovaMultilingualNeuralHD4",
				label: "Nova (Female)"
			},
			{
				value: "en-US-ShimmerMultilingualNeuralHD4",
				label: "Shimmer (Female)"
			}
		],
		"en-ZA": [{
			value: "en-ZA-LeahNeural",
			label: "Leah (Female)"
		},
			{
				value: "en-ZA-LukeNeural",
				label: "Luke (Male)"
			}
		],
		"es-AR": [{
			value: "es-AR-ElenaNeural",
			label: "Elena (Female)"
		},
			{
				value: "es-AR-TomasNeural",
				label: "Tomas (Male)"
			}
		],
		"es-BO": [{
			value: "es-BO-SofiaNeural",
			label: "Sofia (Female)"
		},
			{
				value: "es-BO-MarceloNeural",
				label: "Marcelo (Male)"
			}
		],
		"es-CL": [{
			value: "es-CL-CatalinaNeural",
			label: "Catalina (Female)"
		},
			{
				value: "es-CL-LorenzoNeural",
				label: "Lorenzo (Male)"
			}
		],
		"es-CO": [{
			value: "es-CO-SalomeNeural",
			label: "Salome (Female)"
		},
			{
				value: "es-CO-GonzaloNeural",
				label: "Gonzalo (Male)"
			}
		],
		"es-CR": [{
			value: "es-CR-MariaNeural",
			label: "Maria (Female)"
		},
			{
				value: "es-CR-JuanNeural",
				label: "Juan (Male)"
			}
		],
		"es-CU": [{
			value: "es-CU-BelkysNeural",
			label: "Belkys (Female)"
		},
			{
				value: "es-CU-ManuelNeural",
				label: "Manuel (Male)"
			}
		],
		"es-DO": [{
			value: "es-DO-RamonaNeural",
			label: "Ramona (Female)"
		},
			{
				value: "es-DO-EmilioNeural",
				label: "Emilio (Male)"
			}
		],
		"es-EC": [{
			value: "es-EC-AndreaNeural",
			label: "Andrea (Female)"
		},
			{
				value: "es-EC-LuisNeural",
				label: "Luis (Male)"
			}
		],
		"es-ES": [{
			value: "es-ES-ElviraNeural",
			label: "Elvira (Female)"
		},
			{
				value: "es-ES-AlvaroNeural",
				label: "Alvaro (Male)"
			},
			{
				value: "es-ES-AbrilNeural",
				label: "Abril (Female)"
			},
			{
				value: "es-ES-ArnauNeural",
				label: "Arnau (Male)"
			},
			{
				value: "es-ES-DarioNeural",
				label: "Dario (Male)"
			},
			{
				value: "es-ES-EliasNeural",
				label: "Elias (Male)"
			},
			{
				value: "es-ES-EstrellaNeural",
				label: "Estrella (Female)"
			},
			{
				value: "es-ES-IreneNeural",
				label: "Irene (Female)"
			},
			{
				value: "es-ES-LaiaNeural",
				label: "Laia (Female)"
			},
			{
				value: "es-ES-LiaNeural",
				label: "Lia (Female)"
			},
			{
				value: "es-ES-NilNeural",
				label: "Nil (Male)"
			},
			{
				value: "es-ES-SaulNeural",
				label: "Saul (Male)"
			},
			{
				value: "es-ES-TeoNeural",
				label: "Teo (Male)"
			},
			{
				value: "es-ES-TrianaNeural",
				label: "Triana (Female)"
			},
			{
				value: "es-ES-VeraNeural",
				label: "Vera (Female)"
			},
			{
				value: "es-ES-XimenaNeural1",
				label: "Ximena (Female)"
			}
		],
		"es-GQ": [{
			value: "es-GQ-TeresaNeural",
			label: "Teresa (Female)"
		},
			{
				value: "es-GQ-JavierNeural",
				label: "Javier (Male)"
			}
		],
		"es-GT": [{
			value: "es-GT-MartaNeural",
			label: "Marta (Female)"
		},
			{
				value: "es-GT-AndresNeural",
				label: "Andres (Male)"
			}
		],
		"es-HN": [{
			value: "es-HN-KarlaNeural",
			label: "Karla (Female)"
		},
			{
				value: "es-HN-CarlosNeural",
				label: "Carlos (Male)"
			}
		],
		"es-MX": [{
			value: "es-MX-DaliaNeural",
			label: "Dalia (Female)"
		},
			{
				value: "es-MX-JorgeNeural",
				label: "Jorge (Male)"
			},
			{
				value: "es-MX-BeatrizNeural",
				label: "Beatriz (Female)"
			},
			{
				value: "es-MX-CandelaNeural",
				label: "Candela (Female)"
			},
			{
				value: "es-MX-CarlotaNeural",
				label: "Carlota (Female)"
			},
			{
				value: "es-MX-CecilioNeural",
				label: "Cecilio (Male)"
			},
			{
				value: "es-MX-GerardoNeural",
				label: "Gerardo (Male)"
			},
			{
				value: "es-MX-LarissaNeural",
				label: "Larissa (Female)"
			},
			{
				value: "es-MX-LibertoNeural",
				label: "Liberto (Male)"
			},
			{
				value: "es-MX-LucianoNeural",
				label: "Luciano (Male)"
			},
			{
				value: "es-MX-MarinaNeural",
				label: "Marina (Female)"
			},
			{
				value: "es-MX-NuriaNeural",
				label: "Nuria (Female)"
			},
			{
				value: "es-MX-PelayoNeural",
				label: "Pelayo (Male)"
			},
			{
				value: "es-MX-RenataNeural",
				label: "Renata (Female)"
			},
			{
				value: "es-MX-YagoNeural",
				label: "Yago (Male)"
			}
		],
		"es-NI": [{
			value: "es-NI-YolandaNeural",
			label: "Yolanda (Female)"
		},
			{
				value: "es-NI-FedericoNeural",
				label: "Federico (Male)"
			}
		],
		"es-PA": [{
			value: "es-PA-MargaritaNeural",
			label: "Margarita (Female)"
		},
			{
				value: "es-PA-RobertoNeural",
				label: "Roberto (Male)"
			}
		],
		"es-PE": [{
			value: "es-PE-CamilaNeural",
			label: "Camila (Female)"
		},
			{
				value: "es-PE-AlexNeural",
				label: "Alex (Male)"
			}
		],
		"es-PR": [{
			value: "es-PR-KarinaNeural",
			label: "Karina (Female)"
		},
			{
				value: "es-PR-VictorNeural",
				label: "Victor (Male)"
			}
		],
		"es-PY": [{
			value: "es-PY-TaniaNeural",
			label: "Tania (Female)"
		},
			{
				value: "es-PY-MarioNeural",
				label: "Mario (Male)"
			}
		],
		"es-SV": [{
			value: "es-SV-LorenaNeural",
			label: "Lorena (Female)"
		},
			{
				value: "es-SV-RodrigoNeural",
				label: "Rodrigo (Male)"
			}
		],
		"es-US": [{
			value: "es-US-PalomaNeural",
			label: "Paloma (Female)"
		},
			{
				value: "es-US-AlonsoNeural",
				label: "Alonso (Male)"
			}
		],
		"es-UY": [{
			value: "es-UY-ValentinaNeural",
			label: "Valentina (Female)"
		},
			{
				value: "es-UY-MateoNeural",
				label: "Mateo (Male)"
			}
		],
		"es-VE": [{
			value: "es-VE-PaolaNeural",
			label: "Paola (Female)"
		},
			{
				value: "es-VE-SebastianNeural",
				label: "Sebastian (Male)"
			}
		],
		"et-EE": [{
			value: "et-EE-AnuNeural2",
			label: "Anu (Female)"
		},
			{
				value: "et-EE-KertNeural2",
				label: "Kert (Male)"
			}
		],
		"eu-ES": [{
			value: "eu-ES-AinhoaNeural2",
			label: "Ainhoa (Female)"
		},
			{
				value: "eu-ES-AnderNeural2",
				label: "Ander (Male)"
			}
		],
		"fa-IR": [{
			value: "fa-IR-DilaraNeural2",
			label: "Dilara (Female)"
		},
			{
				value: "fa-IR-FaridNeural2",
				label: "Farid (Male)"
			}
		],
		"fi-FI": [{
			value: "fi-FI-SelmaNeural",
			label: "Selma (Female)"
		},
			{
				value: "fi-FI-HarriNeural",
				label: "Harri (Male)"
			},
			{
				value: "fi-FI-NooraNeural",
				label: "Noora (Female)"
			}
		],
		"fil-PH": [{
			value: "fil-PH-BlessicaNeural2",
			label: "Blessica (Female)"
		},
			{
				value: "fil-PH-AngeloNeural2",
				label: "Angelo (Male)"
			}
		],
		"fr-BE": [{
			value: "fr-BE-CharlineNeural",
			label: "Charline (Female)"
		},
			{
				value: "fr-BE-GerardNeural",
				label: "Gerard (Male)"
			}
		],
		"fr-CA": [{
			value: "fr-CA-SylvieNeural",
			label: "Sylvie (Female)"
		},
			{
				value: "fr-CA-JeanNeural",
				label: "Jean (Male)"
			},
			{
				value: "fr-CA-AntoineNeural",
				label: "Antoine (Male)"
			},
			{
				value: "fr-CA-ThierryNeural1",
				label: "Thierry (Male)"
			}
		],
		"fr-CH": [{
			value: "fr-CH-ArianeNeural",
			label: "Ariane (Female)"
		},
			{
				value: "fr-CH-FabriceNeural",
				label: "Fabrice (Male)"
			}
		],
		"fr-FR": [{
			value: "fr-FR-DeniseNeural",
			label: "Denise (Female)"
		},
			{
				value: "fr-FR-HenriNeural",
				label: "Henri (Male)"
			},
			{
				value: "fr-FR-AlainNeural",
				label: "Alain (Male)"
			},
			{
				value: "fr-FR-BrigitteNeural",
				label: "Brigitte (Female)"
			},
			{
				value: "fr-FR-CelesteNeural",
				label: "Celeste (Female)"
			},
			{
				value: "fr-FR-ClaudeNeural",
				label: "Claude (Male)"
			},
			{
				value: "fr-FR-CoralieNeural",
				label: "Coralie (Female)"
			},
			{
				value: "fr-FR-EloiseNeural",
				label: "Eloise (Female, Child)"
			},
			{
				value: "fr-FR-JacquelineNeural",
				label: "Jacqueline (Female)"
			},
			{
				value: "fr-FR-JeromeNeural",
				label: "Jerome (Male)"
			},
			{
				value: "fr-FR-JosephineNeural",
				label: "Josephine (Female)"
			},
			{
				value: "fr-FR-MauriceNeural",
				label: "Maurice (Male)"
			},
			{
				value: "fr-FR-YvesNeural",
				label: "Yves (Male)"
			},
			{
				value: "fr-FR-YvetteNeural",
				label: "Yvette (Female)"
			},
			{
				value: "fr-FR-RemyMultilingualNeural3",
				label: "Remy (Male)"
			},
			{
				value: "fr-FR-VivienneMultilingualNeural3",
				label: "Vivienne (Female)"
			}
		],
		"ga-IE": [{
			value: "ga-IE-OrlaNeural2",
			label: "Orla (Female)"
		},
			{
				value: "ga-IE-ColmNeural2",
				label: "Colm (Male)"
			}
		],
		"gl-ES": [{
			value: "gl-ES-SabelaNeural2",
			label: "Sabela (Female)"
		},
			{
				value: "gl-ES-RoiNeural2",
				label: "Roi (Male)"
			}
		],
		"gu-IN": [{
			value: "gu-IN-DhwaniNeural",
			label: "Dhwani (Female)"
		},
			{
				value: "gu-IN-NiranjanNeural",
				label: "Niranjan (Male)"
			}
		],
		"he-IL": [{
			value: "he-IL-HilaNeural",
			label: "Hila (Female)"
		},
			{
				value: "he-IL-AvriNeural",
				label: "Avri (Male)"
			}
		],
		"hi-IN": [{
			value: "hi-IN-SwaraNeural",
			label: "Swara (Female)"
		},
			{
				value: "hi-IN-MadhurNeural",
				label: "Madhur (Male)"
			}
		],
		"hr-HR": [{
			value: "hr-HR-GabrijelaNeural",
			label: "Gabrijela (Female)"
		},
			{
				value: "hr-HR-SreckoNeural",
				label: "Srecko (Male)"
			}
		],
		"hu-HU": [{
			value: "hu-HU-NoemiNeural",
			label: "Noemi (Female)"
		},
			{
				value: "hu-HU-TamasNeural",
				label: "Tamas (Male)"
			}
		],
		"hy-AM": [{
			value: "hy-AM-AnahitNeural2",
			label: "Anahit (Female)"
		},
			{
				value: "hy-AM-HaykNeural2",
				label: "Hayk (Male)"
			}
		],
		"id-ID": [{
			value: "id-ID-GadisNeural",
			label: "Gadis (Female)"
		},
			{
				value: "id-ID-ArdiNeural",
				label: "Ardi (Male)"
			}
		],
		"is-IS": [{
			value: "is-IS-GudrunNeural2",
			label: "Gudrun (Female)"
		},
			{
				value: "is-IS-GunnarNeural2",
				label: "Gunnar (Male)"
			}
		],
		"it-IT": [{
			value: "it-IT-ElsaNeural",
			label: "Elsa (Female)"
		},
			{
				value: "it-IT-IsabellaNeural",
				label: "Isabella (Female)"
			},
			{
				value: "it-IT-DiegoNeural",
				label: "Diego (Male)"
			},
			{
				value: "it-IT-BenignoNeural",
				label: "Benigno (Male)"
			},
			{
				value: "it-IT-CalimeroNeural",
				label: "Calimero (Male)"
			},
			{
				value: "it-IT-CataldoNeural",
				label: "Cataldo (Male)"
			},
			{
				value: "it-IT-FabiolaNeural",
				label: "Fabiola (Female)"
			},
			{
				value: "it-IT-FiammaNeural",
				label: "Fiamma (Female)"
			},
			{
				value: "it-IT-GianniNeural",
				label: "Gianni (Male)"
			},
			{
				value: "it-IT-ImeldaNeural",
				label: "Imelda (Female)"
			},
			{
				value: "it-IT-IrmaNeural",
				label: "Irma (Female)"
			},
			{
				value: "it-IT-LisandroNeural",
				label: "Lisandro (Male)"
			},
			{
				value: "it-IT-PalmiraNeural",
				label: "Palmira (Female)"
			},
			{
				value: "it-IT-PierinaNeural",
				label: "Pierina (Female)"
			},
			{
				value: "it-IT-RinaldoNeural",
				label: "Rinaldo (Male)"
			},
			{
				value: "it-IT-GiuseppeNeural1",
				label: "Giuseppe (Male)"
			}
		],
		"ja-JP": [{
			value: "ja-JP-NanamiNeural",
			label: "Nanami (Female)"
		},
			{
				value: "ja-JP-KeitaNeural",
				label: "Keita (Male)"
			},
			{
				value: "ja-JP-AoiNeural",
				label: "Aoi (Female)"
			},
			{
				value: "ja-JP-DaichiNeural",
				label: "Daichi (Male)"
			},
			{
				value: "ja-JP-MayuNeural",
				label: "Mayu (Female)"
			},
			{
				value: "ja-JP-NaokiNeural",
				label: "Naoki (Male)"
			},
			{
				value: "ja-JP-ShioriNeural",
				label: "Shiori (Female)"
			},
			{
				value: "ja-JP-MasaruMultilingualNeural1,3",
				label: "Masaru (Male)"
			}
		],
		"jv-ID": [{
			value: "jv-ID-SitiNeural2",
			label: "Siti (Female)"
		},
			{
				value: "jv-ID-DimasNeural2",
				label: "Dimas (Male)"
			}
		],
		"ka-GE": [{
			value: "ka-GE-EkaNeural2",
			label: "Eka (Female)"
		},
			{
				value: "ka-GE-GiorgiNeural2",
				label: "Giorgi (Male)"
			}
		],
		"kk-KZ": [{
			value: "kk-KZ-AigulNeural2",
			label: "Aigul (Female)"
		},
			{
				value: "kk-KZ-DauletNeural2",
				label: "Daulet (Male)"
			}
		],
		"km-KH": [{
			value: "km-KH-SreymomNeural2",
			label: "Sreymom (Female)"
		},
			{
				value: "km-KH-PisethNeural2",
				label: "Piseth (Male)"
			}
		],
		"kn-IN": [{
			value: "kn-IN-SapnaNeural2",
			label: "Sapna (Female)"
		},
			{
				value: "kn-IN-GaganNeural2",
				label: "Gagan (Male)"
			}
		],
		"ko-KR": [{
			value: "ko-KR-SunHiNeural",
			label: "SunHi (Female)"
		},
			{
				value: "ko-KR-InJoonNeural",
				label: "InJoon (Male)"
			},
			{
				value: "ko-KR-BongJinNeural",
				label: "BongJin (Male)"
			},
			{
				value: "ko-KR-GookMinNeural",
				label: "GookMin (Male)"
			},
			{
				value: "ko-KR-JiMinNeural",
				label: "JiMin (Female)"
			},
			{
				value: "ko-KR-SeoHyeonNeural",
				label: "SeoHyeon (Female)"
			},
			{
				value: "ko-KR-SoonBokNeural",
				label: "SoonBok (Female)"
			},
			{
				value: "ko-KR-YuJinNeural",
				label: "YuJin (Female)"
			},
			{
				value: "ko-KR-HyunsuNeural1",
				label: "Hyunsu (Male)"
			}
		],
		"lo-LA": [{
			value: "lo-LA-KeomanyNeural2",
			label: "Keomany (Female)"
		},
			{
				value: "lo-LA-ChanthavongNeural2",
				label: "Chanthavong (Male)"
			}
		],
		"lt-LT": [{
			value: "lt-LT-OnaNeural2",
			label: "Ona (Female)"
		},
			{
				value: "lt-LT-LeonasNeural2",
				label: "Leonas (Male)"
			}
		],
		"lv-LV": [{
			value: "lv-LV-EveritaNeural2",
			label: "Everita (Female)"
		},
			{
				value: "lv-LV-NilsNeural2",
				label: "Nils (Male)"
			}
		],
		"mk-MK": [{
			value: "mk-MK-MarijaNeural2",
			label: "Marija (Female)"
		},
			{
				value: "mk-MK-AleksandarNeural2",
				label: "Aleksandar (Male)"
			}
		],
		"ml-IN": [{
			value: "ml-IN-SobhanaNeural2",
			label: "Sobhana (Female)"
		},
			{
				value: "ml-IN-MidhunNeural2",
				label: "Midhun (Male)"
			}
		],
		"mn-MN": [{
			value: "mn-MN-YesuiNeural2",
			label: "Yesui (Female)"
		},
			{
				value: "mn-MN-BataaNeural2",
				label: "Bataa (Male)"
			}
		],
		"mr-IN": [{
			value: "mr-IN-AarohiNeural",
			label: "Aarohi (Female)"
		},
			{
				value: "mr-IN-ManoharNeural",
				label: "Manohar (Male)"
			}
		],
		"ms-MY": [{
			value: "ms-MY-YasminNeural",
			label: "Yasmin (Female)"
		},
			{
				value: "ms-MY-OsmanNeural",
				label: "Osman (Male)"
			}
		],
		"mt-MT": [{
			value: "mt-MT-GraceNeural2",
			label: "Grace (Female)"
		},
			{
				value: "mt-MT-JosephNeural2",
				label: "Joseph (Male)"
			}
		],
		"my-MM": [{
			value: "my-MM-NilarNeural2",
			label: "Nilar (Female)"
		},
			{
				value: "my-MM-ThihaNeural2",
				label: "Thiha (Male)"
			}
		],
		"nb-NO": [{
			value: "nb-NO-PernilleNeural",
			label: "Pernille (Female)"
		},
			{
				value: "nb-NO-FinnNeural",
				label: "Finn (Male)"
			},
			{
				value: "nb-NO-IselinNeural",
				label: "Iselin (Female)"
			}
		],
		"ne-NP": [{
			value: "ne-NP-HemkalaNeural2",
			label: "Hemkala (Female)"
		},
			{
				value: "ne-NP-SagarNeural2",
				label: "Sagar (Male)"
			}
		],
		"nl-BE": [{
			value: "nl-BE-DenaNeural",
			label: "Dena (Female)"
		},
			{
				value: "nl-BE-ArnaudNeural",
				label: "Arnaud (Male)"
			}
		],
		"nl-NL": [{
			value: "nl-NL-FennaNeural",
			label: "Fenna (Female)"
		},
			{
				value: "nl-NL-MaartenNeural",
				label: "Maarten (Male)"
			},
			{
				value: "nl-NL-ColetteNeural",
				label: "Colette (Female)"
			}
		],
		"pl-PL": [{
			value: "pl-PL-AgnieszkaNeural",
			label: "Agnieszka (Female)"
		},
			{
				value: "pl-PL-MarekNeural",
				label: "Marek (Male)"
			},
			{
				value: "pl-PL-ZofiaNeural",
				label: "Zofia (Female)"
			}
		],
		"ps-AF": [{
			value: "ps-AF-LatifaNeural2",
			label: "Latifa (Female)"
		},
			{
				value: "ps-AF-GulNawazNeural2",
				label: "GulNawaz (Male)"
			}
		],
		"pt-BR": [{
			value: "pt-BR-FranciscaNeural",
			label: "Francisca (Female)"
		},
			{
				value: "pt-BR-AntonioNeural",
				label: "Antonio (Male)"
			},
			{
				value: "pt-BR-BrendaNeural",
				label: "Brenda (Female)"
			}
		],
		"pt-PT": [{
			value: "pt-PT-RaquelNeural",
			label: "Raquel (Female)"
		},
			{
				value: "pt-PT-DuarteNeural",
				label: "Duarte (Male)"
			},
			{
				value: "pt-PT-FernandaNeural",
				label: "Fernanda (Female)"
			}
		],
		"ro-RO": [{
			value: "ro-RO-AlinaNeural",
			label: "Alina (Female)"
		},
			{
				value: "ro-RO-EmilNeural",
				label: "Emil (Male)"
			}
		],
		"ru-RU": [{
			value: "ru-RU-SvetlanaNeural",
			label: "Svetlana (Female)"
		},
			{
				value: "ru-RU-DmitryNeural",
				label: "Dmitry (Male)"
			},
			{
				value: "ru-RU-DariyaNeural",
				label: "Dariya (Female)"
			}
		],
		"si-LK": [{
			value: "si-LK-ThiliniNeural2",
			label: "Thilini (Female)"
		},
			{
				value: "si-LK-SameeraNeural2",
				label: "Sameera (Male)"
			}
		],
		"sk-SK": [{
			value: "sk-SK-ViktoriaNeural",
			label: "Viktoria (Female)"
		},
			{
				value: "sk-SK-LukasNeural",
				label: "Lukas (Male)"
			}
		],
		"sl-SI": [{
			value: "sl-SI-PetraNeural",
			label: "Petra (Female)"
		},
			{
				value: "sl-SI-RokNeural",
				label: "Rok (Male)"
			}
		],
		"so-SO": [{
			value: "so-SO-UbaxNeural2",
			label: "Ubax (Female)"
		},
			{
				value: "so-SO-MuuseNeural2",
				label: "Muuse (Male)"
			}
		],
		"sq-AL": [{
			value: "sq-AL-AnilaNeural2",
			label: "Anila (Female)"
		},
			{
				value: "sq-AL-IlirNeural2",
				label: "Ilir (Male)"
			}
		],
		"sr-LATN-RS": [{
			value: "sr-Latn-RS-NicholasNeural2",
			label: "Nicholas (Male)"
		},
			{
				value: "sr-Latn-RS-SophieNeural2",
				label: "Sophie (Female)"
			}
		],
		"sr-RS": [{
			value: "sr-RS-SophieNeural2",
			label: "Sophie (Female)"
		},
			{
				value: "sr-RS-NicholasNeural2",
				label: "Nicholas (Male)"
			}
		],
		"su-ID": [{
			value: "su-ID-TutiNeural2",
			label: "Tuti (Female)"
		},
			{
				value: "su-ID-JajangNeural2",
				label: "Jajang (Male)"
			}
		],
		"sv-SE": [{
			value: "sv-SE-SofieNeural",
			label: "Sofie (Female)"
		},
			{
				value: "sv-SE-MattiasNeural",
				label: "Mattias (Male)"
			},
			{
				value: "sv-SE-HilleviNeural",
				label: "Hillevi (Female)"
			}
		],
		"sw-KE": [{
			value: "sw-KE-ZuriNeural2",
			label: "Zuri (Female)"
		},
			{
				value: "sw-KE-RafikiNeural2",
				label: "Rafiki (Male)"
			}
		],
		"sw-TZ": [{
			value: "sw-TZ-RehemaNeural",
			label: "Rehema (Female)"
		},
			{
				value: "sw-TZ-DaudiNeural",
				label: "Daudi (Male)"
			}
		],
		"ta-IN": [{
			value: "ta-IN-PallaviNeural",
			label: "Pallavi (Female)"
		},
			{
				value: "ta-IN-ValluvarNeural",
				label: "Valluvar (Male)"
			}
		],
		"ta-LK": [{
			value: "ta-LK-SaranyaNeural",
			label: "Saranya (Female)"
		},
			{
				value: "ta-LK-KumarNeural",
				label: "Kumar (Male)"
			}
		],
		"ta-MY": [{
			value: "ta-MY-KaniNeural",
			label: "Kani (Female)"
		},
			{
				value: "ta-MY-SuryaNeural",
				label: "Surya (Male)"
			}
		],
		"ta-SG": [{
			value: "ta-SG-VenbaNeural",
			label: "Venba (Female)"
		},
			{
				value: "ta-SG-AnbuNeural",
				label: "Anbu (Male)"
			}
		],
		"te-IN": [{
			value: "te-IN-ShrutiNeural",
			label: "Shruti (Female)"
		},
			{
				value: "te-IN-MohanNeural",
				label: "Mohan (Male)"
			}
		],
		"th-TH": [{
			value: "th-TH-PremwadeeNeural",
			label: "Premwadee (Female)"
		},
			{
				value: "th-TH-NiwatNeural",
				label: "Niwat (Male)"
			},
			{
				value: "th-TH-AcharaNeural",
				label: "Achara (Female)"
			}
		],
		"tr-TR": [{
			value: "tr-TR-EmelNeural",
			label: "Emel (Female)"
		},
			{
				value: "tr-TR-AhmetNeural",
				label: "Ahmet (Male)"
			}
		],
		"uk-UA": [{
			value: "uk-UA-PolinaNeural",
			label: "Polina (Female)"
		},
			{
				value: "uk-UA-OstapNeural",
				label: "Ostap (Male)"
			}
		],
		"ur-IN": [{
			value: "ur-IN-GulNeural",
			label: "Gul (Female)"
		},
			{
				value: "ur-IN-SalmanNeural",
				label: "Salman (Male)"
			}
		],
		"ur-PK": [{
			value: "ur-PK-UzmaNeural",
			label: "Uzma (Female)"
		},
			{
				value: "ur-PK-AsadNeural",
				label: "Asad (Male)"
			}
		],
		"uz-UZ": [{
			value: "uz-UZ-MadinaNeural2",
			label: "Madina (Female)"
		},
			{
				value: "uz-UZ-SardorNeural2",
				label: "Sardor (Male)"
			}
		],
		"vi-VN": [{
			value: "vi-VN-HoaiMyNeural",
			label: "Hoai My (Female)"
		},
			{
				value: "vi-VN-NamMinhNeural",
				label: "Nam Minh (Male)"
			}
		],
		"wuu-CN": [{
			value: "wuu-CN-XiaotongNeural2",
			label: "Xiaotong (Female)"
		},
			{
				value: "wuu-CN-YunzheNeural2",
				label: "Yunzhe (Male)"
			}
		],
		"yue-CN": [{
			value: "yue-CN-XiaoMinNeural2",
			label: "Xiao Min (Female)"
		},
			{
				value: "yue-CN-YunSongNeural2",
				label: "Yun Song (Male)"
			}
		],
		"zh-CN": [{
			value: "zh-CN-XiaoxiaoNeural",
			label: "Xiaoxiao (Female)"
		},
			{
				value: "zh-CN-YunxiNeural",
				label: "Yunxi (Male)"
			},
			{
				value: "zh-CN-YunjianNeural",
				label: "Yunjian (Male)"
			},
			{
				value: "zh-CN-XiaoyiNeural",
				label: "Xiaoyi (Female)"
			},
			{
				value: "zh-CN-YunyangNeural",
				label: "Yunyang (Male)"
			},
			{
				value: "zh-CN-XiaochenNeural",
				label: "Xiaochen (Female)"
			},
			{
				value: "zh-CN-XiaohanNeural",
				label: "Xiaohan (Female)"
			},
			{
				value: "zh-CN-XiaomengNeural",
				label: "Xiaomeng (Female)"
			},
			{
				value: "zh-CN-XiaomoNeural",
				label: "Xiaomo (Female)"
			},
			{
				value: "zh-CN-XiaoqiuNeural",
				label: "Xiaoqiu (Female)"
			},
			{
				value: "zh-CN-XiaoruiNeural",
				label: "Xiaorui (Female)"
			},
			{
				value: "zh-CN-XiaoshuangNeural",
				label: "Xiaoshuang (Female, Child)"
			},
			{
				value: "zh-CN-XiaoyanNeural",
				label: "Xiaoyan (Female)"
			},
			{
				value: "zh-CN-XiaoyouNeural",
				label: "Xiaoyou (Female, Child)"
			},
			{
				value: "zh-CN-XiaozhenNeural",
				label: "Xiaozhen (Female)"
			},
			{
				value: "zh-CN-YunfengNeural",
				label: "Yunfeng (Male)"
			},
			{
				value: "zh-CN-YunhaoNeural",
				label: "Yunhao (Male)"
			},
			{
				value: "zh-CN-YunxiaNeural",
				label: "Yunxia (Male)"
			},
			{
				value: "zh-CN-YunyeNeural",
				label: "Yunye (Male)"
			},
			{
				value: "zh-CN-YunzeNeural",
				label: "Yunze (Male)"
			},
			{
				value: "zh-CN-XiaochenMultilingualNeural",
				label: "Xiaochen (Female)"
			},
			{
				value: "zh-CN-XiaorouNeural",
				label: "Xiaorou (Female)"
			},
			{
				value: "zh-CN-XiaoxiaoDialectsNeural",
				label: "Xiaoxiao (Female)"
			},
			{
				value: "zh-CN-XiaoxiaoMultilingualNeural",
				label: "Xiaoxiao (Female)"
			},
			{
				value: "zh-CN-XiaoyuMultilingualNeural",
				label: "Xiaoyu (Female)"
			},
			{
				value: "zh-CN-YunjieNeural",
				label: "Yunjie (Male)"
			},
			{
				value: "zh-CN-YunyiMultilingualNeural",
				label: "Yunyi (Male)"
			},
			{
				value: "zh-CN-GUANGXI-YunqiNeural",
				label: "Yunqi (Male)"
			},
			{
				value: "zh-CN-henan-YundengNeural",
				label: "Yundeng (Male)"
			},
			{
				value: "zh-CN-liaoning-XiaobeiNeural",
				label: "Xiaobei (Female)"
			},
			{
				value: "zh-CN-liaoning-YunbiaoNeural",
				label: "Yunbiao (Male)"
			},
			{
				value: "zh-CN-shaanxi-XiaoniNeural",
				label: "Xiaoni (Female)"
			},
			{
				value: "zh-CN-shandong-YunxiangNeural",
				label: "Yunxiang (Male)"
			},
			{
				value: "zh-CN-sichuan-YunxiNeural",
				label: "Yunxi (Male)"
			},
		],
		"zh-HK": [{
			value: "zh-HK-HiuMaanNeural",
			label: "Hiu Maan (Female)"
		},
			{
				value: "zh-HK-WanLungNeural",
				label: "Wan Lung (Male)"
			},
			{
				value: "zh-HK-HiuGaaiNeural",
				label: "Hiu Gaai (Female)"
			}
		],
		"zh-TW": [{
			value: "zh-TW-HsiaoChenNeural",
			label: "Hsiao Chen (Female)"
		},
			{
				value: "zh-TW-YunJheNeural",
				label: "Yun Jhe (Male)"
			},
			{
				value: "zh-TW-HsiaoYuNeural",
				label: "Hsiao Yu (Female)"
			}
		],
		"zu-ZA": [{
			value: "zu-ZA-ThandoNeural",
			label: "Thando (Female)"
		},
			{
				value: "zu-ZA-ThembaNeural",
				label: "Themba (Male)"
			}
		]

	};


	function country2flag(languageCode) {
		var countryCode = languageCode.match(/[A-Z]{2}$/i);
		if (!countryCode) {
			return null;
		}
		var flag = countryCode[0].replace(/./g, function(letter) {
			return String.fromCodePoint(letter.charCodeAt(0) % 32 + 0x1F1E5);
		});
		return flag;
	}

	$(document).ready(function() {
		"use strict";

		populateVoiceSelect();
		populatePaceSelect();

		$("#languages").on("change", function() {
			populateVoiceSelect();
			populatePaceSelect();
		});

		$("#voice").on("change", function() {
			populatePaceSelect();
		});

		function populateVoiceSelect() {
			const selectedLanguage = $("#languages").val();
			const selectedOptions = voicesData[selectedLanguage];

			const voiceSelect = $("#voice");

			voiceSelect.empty();

			if (selectedOptions) {
				if (@json($settings_two->feature_tts_google) == true) {
					selectedOptions.forEach(option => {
						$("<option></option>")
							.val(option.value)
							.text(option.label)
							.attr('name', option.label)
							.attr('platform', "google")
							.appendTo(voiceSelect);
					});
				}

				if (@json($settings_two->feature_tts_openai) == true) {
					if (allowedOpenAIList.includes(selectedLanguage)) {
						openaiVoiceData.forEach(option => {
							$("<option></option>")
								.val(option.value)
								.text(option.label)
								.attr('platform', "openai")
								.attr('name', option.label + (" (OpenAI)"))
								.appendTo(voiceSelect);
						});
					}
				}

				if (@json($settings_two->feature_tts_elevenlabs) == true) {
					const playButton = $('#playVoiceButton');
					if (allowedElevenLabsList.includes(selectedLanguage)) {
						elevenLabsVoices.forEach(option => {
							$("<option></option>")
								.val(option.voice_id)
								.text(option.name)
								.attr('name', option.name + (" ({{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->label() }})"))
								.attr('platform', "{{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->value }}")
								.appendTo(voiceSelect);
						});

						voiceSelect.on('change', function() {
							const selectedOption = $(this).find('option:selected');
							const selectedVoiceId = selectedOption.val();
							const selectedVoice = elevenLabsVoices.find(voice => voice.voice_id === selectedVoiceId);

							if (selectedVoice && selectedVoice.preview_url) {
								playButton.show();
								playButton.off('click');
								playButton.on('click', function() {
									const audio = new Audio(selectedVoice.preview_url);
									audio.play();
								});
							} else {
								playButton.hide();
							}
						});
					}
				}
			} else {
				if (@json($settings_two->feature_tts_google) == true) {
					$("<option></option>")
						.val("en-US-Standard-A")
						.text("Default ({{ __('English') }})")
						.attr('platform', "google")
						.appendTo(voiceSelect);
				}

				if (@json($settings_two->feature_tts_openai) == true) {
					openaiVoiceData.forEach(option => {
						$("<option></option>")
							.val(option.value)
							.text(option.label)
							.attr('platform', "openai")
							.attr('name', option.label + (" (OpenAI)"))
							.appendTo(voiceSelect);
					});
				}

				if (@json($settings_two->feature_tts_elevenlabs) == true) {
					elevenLabsVoices.forEach(option => {
						$("<option></option>")
							.val(option.voice_id)
							.text(option.name)
							.attr('name', option.name + (" ({{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->label() }})"))
							.attr('platform', "{{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->value }}")
							.appendTo(voiceSelect);
					});
				}
			}

			@includeFirst(['azure-tts::feature_tts_azure', 'default.panel.user.openai.components.feature_tts_azure', 'vendor.empty'])
			@includeFirst(['speechify-tts::feature_tts_speechify', 'default.panel.user.openai.components.feature_tts_speechify', 'vendor.empty'])
		}

		function populatePaceSelect() {
			const selectedPlatform = $('#voice option:selected').attr('platform');
			const selectedOptions_google = [{
				value: "x-slow",
				label: "Very Slow"
			}, {
				value: "slow",
				label: "Slow"
			}, {
				value: "medium",
				label: "Medium"
			}, {
				value: "fast",
				label: "Fast"
			}, {
				value: "x-fast",
				label: "Very Fast"
			}, ];

			const selectedOptions_openai = [{
				value: "{{ \App\Domains\Entity\Enums\EntityEnum::TTS_1->value }}",
				label: "Normal"
			}, {
				value: "{{ \App\Domains\Entity\Enums\EntityEnum::TTS_1_HD->value }}",
				label: "Neural"
			}];

			const selectedOptions_elevenlabs = [{
				value: "100",
				label: "Slow"
			}, {
				value: "50",
				label: "Medium"
			}, {
				value: "0",
				label: "Fast"
			}, ];

			const paceSelect = $("#pace");

			paceSelect.empty();

			if (selectedPlatform == 'google') {
				$("#break").parent().removeClass('hidden');
				selectedOptions_google.forEach(option => {
					$("<option></option>")
						.val(option.value)
						.text(option.label)
						.attr('platform', "google")
						.appendTo(paceSelect);
				});
			} else if (selectedPlatform == "openai") {
				$("#break").parent().addClass('hidden');
				selectedOptions_openai.forEach(option => {
					$("<option></option>")
						.val(option.value)
						.text(option.label)
						.attr('platform', "openai")
						.appendTo(paceSelect);
				});
			} else if (selectedPlatform == "{{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->value }}") {
				$("#break").parent().addClass('hidden');
				selectedOptions_elevenlabs.forEach(option => {
					$("<option></option>")
						.val(option.value)
						.text(option.label)
						.attr('platform', "{{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->value }}")
						.appendTo(paceSelect);
				});
			}
		}

		$('.add-new-text').click(function() {
			var selectedVoice = $('#voice option:selected').val();
			var selectedVoiceText = $('#voice option:selected').text();
			var selectedLang = $('#languages option:selected').val();
			var selectedLanguage = $('#languages option:selected').attr('language');
			var selectedPace = $('#pace option:selected').val();
			var selectedBreak = $('#break option:selected').val();
			var selectedVoiceOpenAI = $('#voice_openai option:selected').val();
			var selectedModelOpenAI = $('#model_openai option:selected').val();
			var selectedPlatform = $('#voice option:selected').attr('platform');

			const speechTemplate = document.querySelector('#speech-template').content.cloneNode(true);
			const speechEl = speechTemplate.querySelector('.speech');
			const speechTemplateLang = speechEl.querySelector('.data-lang');
			const speechTemplateVoice = speechEl.querySelector('.data-voice');
			const speechTemplatePreview = speechEl.querySelector('.preview-speech');
			const speechTemplateDelete = speechEl.querySelector('.delete-speech');
			const speechTemplateTextarea = speechEl.querySelector('textarea');

			let speechContent = '';

			let tts = @json($settings_two->tts);

			speechTemplateLang.innerText = country2flag(selectedLang);
			speechTemplateVoice.innerText = selectedVoiceText;
			selectedPlatform === '{{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->value }}' && speechTemplateTextarea.setAttribute('name',
				selectedVoiceText);
			speechTemplateTextarea.setAttribute('data-platform', selectedPlatform);
			speechTemplateTextarea.setAttribute('data-voice', selectedVoice);
			speechTemplateTextarea.setAttribute('data-language', selectedLanguage);
			speechTemplateTextarea.setAttribute('data-lang', selectedLang);
			speechTemplateTextarea.setAttribute('data-pace', selectedPace);
			speechTemplateTextarea.setAttribute('data-break', selectedBreak);
			speechTemplateTextarea.setAttribute('data-modelopenai', selectedModelOpenAI);
			speechTemplateTextarea.setAttribute('data-voiceopenai', selectedVoiceOpenAI);

			if (selectedPlatform !== '{{ \App\Domains\Engine\Enums\EngineEnum::ELEVENLABS->value }}' && selectedPlatform !== 'openai') {
				const sayAsTemplate = document.querySelector('#speech-say-as-template').content
					.cloneNode(true);
				const sayAsEl = sayAsTemplate.querySelector('.say-as');
				speechEl.querySelector('.say-as-container').appendChild(sayAsEl);
			}

			$('.speeches').append(speechEl);
		});

		$(document).on('click', '.delete-speech', function() {
			$(this).closest('.speech').remove();
		});

		$(document).on('change', '.say-as', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'currency') {
				selectedValue = "<say-as interpret-as='currency' language='en-US'>$42.01</say-as>";
			} else if (selectedValue === 'telephone') {
				selectedValue =
					"<say-as interpret-as='telephone' google:style='zero-as-zero'>1800-202-1212</say-as>";
			} else if (selectedValue === 'verbatim') {
				selectedValue = "<say-as interpret-as='verbatim'>abcdefg</say-as>";
			} else if (selectedValue === 'date') {
				selectedValue =
					"<say-as interpret-as='date' format='yyyymmdd' detail='1'>1960-09-10</say-as>";
			} else if (selectedValue === 'characters') {
				selectedValue = "<say-as interpret-as='characters'>can</say-as>";
			} else if (selectedValue === 'cardinal') {
				selectedValue = "<say-as interpret-as='cardinal'>12345</say-as>";
			} else if (selectedValue === 'ordinal') {
				selectedValue = "<say-as interpret-as='ordinal'>1</say-as>";
			} else if (selectedValue === 'fraction') {
				selectedValue = "<say-as interpret-as='fraction'>5+1/2</say-as>";
			} else if (selectedValue === 'bleep') {
				selectedValue = "<say-as interpret-as='expletive'>censor this</say-as>";
			} else if (selectedValue === 'unit') {
				selectedValue = "<say-as interpret-as='unit'>10 foot</say-as>";
			} else if (selectedValue === 'time') {
				selectedValue = "<say-as interpret-as='time' format='hms12'>2:30pm</say-as>";
			} else {
				selectedValue = '';
			}
			var textarea = $(this).closest('.speech').find('textarea');
			var existingValue = textarea.val();
			// textarea.val(existingValue + selectedValue);
			sayAs = selectedValue;
			$(this).val('0');
		});

		// Preview
		$(document).on('click', '.preview-speech', function() {

			const previewButton = $(this);
			var speechElement = $(this).closest('.speech');
			var textareaValue = speechElement.find('textarea').val();

			if (textareaValue) {
				previewButton.addClass('loading');

				var formData = new FormData();
				var speechData = [];

				var data = {
					voice: speechElement.find('textarea').attr('data-voice'),
					lang: speechElement.find('textarea').attr('data-lang'),
					language: speechElement.find('textarea').attr('data-language'),
					pace: speechElement.find('textarea').attr('data-pace'),
					platform: speechElement.find('textarea').attr('data-platform'),
					break: speechElement.find('textarea').attr('data-break'),
					voice_openai: speechElement.find('textarea').attr('data-voiceopenai'),
					model_openai: speechElement.find('textarea').attr('data-modelopenai'),
					content: speechElement.find('textarea').val() + ' ' + sayAs,
					name: speechElement.find('textarea').attr('name'),
				};
				speechData.push(data);

				var jsonData = JSON.stringify(speechData);
				formData.append('speeches', jsonData);
				formData.append('preview', true);

				$.ajax({
					type: "post",
					headers: {
						'X-CSRF-TOKEN': "{{ csrf_token() }}",
					},
					url: "/dashboard/user/openai/generate-speech",
					data: formData,
					contentType: false,
					processData: false,
					success: function(data) {
						toastr.success("{{ __('Generated') }}");
						document.getElementById("generate_speech_button").disabled = false;
						document.getElementById("generate_speech_button").innerHTML = "{{ __('Generate') }}";
						speechElement.find('.data-audio').addClass('active').attr('data-audio', data.audioPath);
						generateWaveForm(speechElement[0].querySelector('.data-audio'));
						previewButton.removeClass('loading');
					},
					error: function(data) {
						var err = data.responseJSON.errors;
						$.each(err, function(index, value) {
							toastr.error(value);
						});
						document.getElementById("generate_speech_button").disabled = false;
						previewButton.removeClass('loading');
					},
				});
			} else {
				toastr.error("{{ __('Input is empty!') }}");
			}

		});
	});
</script>
