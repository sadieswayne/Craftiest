<script>
	let stablediffusionType = "text-to-image";

	let resultVideoId = "";
	let intervalId = -1;
	let sourceImgUrl = "";
	let checking = false;

	function hideLoadingIndicators() {
		document.getElementById("openai_generator_button").disabled = false;
		document.getElementById("openai_generator_button").innerHTML = "Regenerate";
		Alpine.store('appLoadingIndicator').hide();
		document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');
	}

	function checkVideoDone() {
		'use strict';
		if (checking) return;
		checking = true;

		let formData = new FormData();
		formData.append('id', resultVideoId);
		formData.append('url', sourceImgUrl);
		formData.append('size', `${postImageWidth}x${postImageHeight}`);

		$.ajax({
			type: "post",
			headers: {
				'X-CSRF-TOKEN': "{{ csrf_token() }}",
			},
			url: "/dashboard/user/openai/check/videoprogress",
			data: formData,
			contentType: false,
			processData: false,
			success: function(res) {
				checking = false;
				if (res.status == 'finished') {
					clearInterval(intervalId);
					intervalId = -1;
					const videoContainer = document.querySelector('.video-results');
					const videoResultTemplate = document.querySelector('#video_result').content.cloneNode(
						true);
					const delete_url =
						`${server}/dashboard/user/openai/documents/delete/image/${res.video.slug}`;

					videoResultTemplate.querySelector('.video-result').classList.remove('lqd-is-loading');
					videoResultTemplate.querySelector('.video-result').setAttribute('data-id', res.video.id);
					videoResultTemplate.querySelector('.lqd-video-result-video source').setAttribute('src',
						res.video.output);
					videoResultTemplate.querySelector('.lqd-video-result-view').setAttribute('data-payload',
						JSON.stringify(res.video));

					videoResultTemplate.querySelector('.lqd-video-result-download').setAttribute('href', res
						.video.output);
					videoResultTemplate.querySelector('.lqd-video-result-download').setAttribute('download', res
						.video.slug);
					videoResultTemplate.querySelector('.lqd-video-result-play').setAttribute('href', res
						.video.output);

					videoContainer.insertBefore(videoResultTemplate, videoContainer.firstChild);

					hideLoadingIndicators();

					refreshFsLightbox();
				} else if (res.status == 'in-progress') {

				}
			},
			error: function(data) {
				checking = false;
				clearInterval(intervalId);
				document.getElementById("openai_generator_button").disabled = false;
				document.getElementById("openai_generator_button").innerHTML = "Generate";
				Alpine.store('appLoadingIndicator').hide();
				document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
				if (data.responseJSON.errors) {
					$.each(data.responseJSON.errors, function(index, value) {
						toastr.error(value);
					});
				} else if (data.responseJSON.message) {
					toastr.error(data.responseJSON.message);
				}
			}
		});
	}

	function sendOpenaiGeneratorForm(ev) {


		ev?.preventDefault();
		ev?.stopPropagation();

		@if ($openai->type == 'video')
		if (resizedImage == undefined) {
			toastr.warning('Please input image');
			Alpine.store('appLoadingIndicator').hide();
			return false;
		}
		if (!((imageWidth == 1024 && imageHeight == 576) || (imageWidth == 768 && imageHeight == 768) || (
			imageWidth == 576 && imageHeight == 1024))) {
			toastr.warning('Image size should be  1024x576, 576x1024, 768x768');
			return false;
		}
		postImageWidth = imageWidth;
		postImageHeight = imageHeight;
		@endif

		document.getElementById("openai_generator_button").disabled = true;
		document.getElementById("openai_generator_button").innerHTML = magicai_localize.please_wait;


		Alpine.store('appLoadingIndicator').show();
		@if ($openai->type == 'image')
		var imageGenerator = document.querySelector('[data-generator-name][data-active=true]')?.getAttribute(
			'data-generator-name');
		@endif
		var formData = new FormData();
		formData.append('post_type', '{{ $openai->slug }}');
		formData.append('openai_id', {{ $openai->id }});
		formData.append('custom_template', {{ $openai->custom_template }});
		@if ($openai->type == 'text')
		formData.append('maximum_length', $("#maximum_length").val());
		formData.append('number_of_results', $("#number_of_results").val());
		formData.append('creativity', $("#creativity").val());
		formData.append('tone_of_voice', $("#tone_of_voice").val());
		formData.append('tone_of_voice_custom', $("#tone_of_voice_custom").val());
		formData.append('language', $("#language").val());
		@endif

			@if ($openai->type == 'audio' || $openai->type == \App\Domains\Entity\Enums\EntityEnum::ISOLATOR->value)
		if ($('#file').prop('files').length == 0) {
			toastr.warning('Please upload an audio file');
			hideLoadingIndicators();
			return false;
		}
		formData.append('file', $('#file').prop('files')[0]);
		@endif

		@if ($openai->type == 'image')
		formData.append('image_generator', imageGenerator);

		if (imageGenerator == 'openai') {
			formData.append('image_style', $("#image_style").val());
			formData.append('image_lighting', $("#image_lighting").val());
			formData.append('image_mood', $("#image_mood").val());
			// formData.append('image_model', document.getElementById('image_model').value)
			formData.delete('size');
			//if(document.getElementById('image_model').value == 'dall-e-2'){
			formData.append('image_number_of_images', $("#image_number_of_images").val());
			formData.append('size', $("#size").val());
			formData.append('quality', $("#image_quality").val());
			// } else {
			//     formData.append('image_number_of_images', $("#image_number_of_images_3").val());
			//     formData.append('size', $("#size_3").val());
			// }
		} else {
			formData.append('type', stablediffusionType);
			formData.append('negative_prompt', $("#negative_prompt").val());
			formData.append('style_preset', $("#style_preset").val());
			formData.append('image_mood', $("#image_mood_stable").val());
			formData.append('sampler', $("#sampler").val());
			formData.append('clip_guidance_preset', $("#clip_guidance_preset").val());
			formData.append('image_resolution', $("#image_resolution").val());
			formData.append('image_number_of_images', $("#image_number_of_images_stable").val());

			switch (stablediffusionType) {
				case 'text-to-image':
					formData.append("stable_description", $("#txt2img_description").val());
					break;
				case 'image-to-image':
					formData.append("stable_description", $("#img2img_description").val());
					formData.append("image_src", resizedImage);
					break;
				case 'upscale':
					formData.append("stable_description", "upscale");
					formData.append("image_src", resizedImage);
					break;
				case 'multi-prompt':
					$('.multi_prompts_description').each(function(idx, e) {
						formData.append("stable_description[]", $(e).val())
					})
					break;
			}
		}
		@endif

		@if ($openai->type == 'video')
		formData.append("image_src", resizedImage);
		formData.append('seed', $("#video_seed").val());
		formData.append('cfg_scale', $("#video_cfg_scale").val());
		formData.append('motion_bucket_id', $("#video_motion_bucket_id").val());
		@endif

			@foreach (json_decode($openai->questions) ?? [] as $question)
		if ("{{ $question->name }}" != "size")
			formData.append("{{ $question->name }}", $("#{{ $question->name }}").val());
		@endforeach

		$.ajax({
			type: "post",
			headers: {
				'X-CSRF-TOKEN': "{{ csrf_token() }}",
			},
			url: "/dashboard/user/openai/generate",
			data: formData,
			contentType: false,
			processData: false,
			success: function(res) {

				if (res.status !== 'success' && (res.message)) {
					toastr.error(res.message);
					hideLoadingIndicators();
					return;
				}

				//show successful message
				@if ($openai->type == 'image')
				toastr.success(`Image Generated Successfully`);
				@elseif ($openai->type == 'video')
					resultVideoId = res.id;
				@else
				toastr.success("{{ __('Generated Successfully!') }}");
				@endif

				setTimeout(function() {
					@if ($openai->type == 'image')

					const images = res.images;
					const currenturl = window.location.href;
					const server = currenturl.split('/')[0];
					const imageContainer = document.querySelector('.image-results');
					const imageResultTemplate = document.querySelector('#image_result').content
						.cloneNode(true);

					images.forEach((image) => {
						const delete_url =
							`${server}/dashboard/user/openai/documents/delete/image/${image.slug}`;

						imageResultTemplate.querySelector('.image-result').setAttribute('data-id', image.id);
						imageResultTemplate.querySelector('.image-result').classList.remove('lqd-is-loading');
						imageResultTemplate.querySelector('.image-result').setAttribute(
							'data-generator', image.response == "SD" ? "sd" : "de");
						imageResultTemplate.querySelector('.lqd-image-result-img')
							.setAttribute('src', image.output);
						imageResultTemplate.querySelector('.lqd-image-result-type')
							.innerHTML = image.response == "SD" ? "SD" : "DE";
						imageResultTemplate.querySelector('.lqd-image-result-view')
							.setAttribute('data-payload', JSON.stringify(image));

						imageResultTemplate.querySelector('.lqd-image-result-delete')
							.setAttribute('href', delete_url);
						imageResultTemplate.querySelector('.lqd-image-result-download')
							.setAttribute('href', image.output);
						imageResultTemplate.querySelector('.lqd-image-result-download')
							.setAttribute('download', image.slug);
						imageResultTemplate.querySelector('.lqd-image-result-title')
							.setAttribute('title', image.input);
						imageResultTemplate.querySelector('.lqd-image-result-title')
							.innerText = image.input;
						imageContainer.insertBefore(imageResultTemplate, imageContainer
							.firstChild);

					})
					@if ($openai->type != 'image')
					refreshFsLightbox();
					@endif
						@elseif ($openai->type == 'video')
						sourceImgUrl = res.sourceUrl;
					intervalId = setInterval(checkVideoDone, 10000);
					@elseif ($openai->type == 'audio' || $openai->type == \App\Domains\Entity\Enums\EntityEnum::ISOLATOR->value)
					$("#generator_sidebar_table").html(res?.data?.html2 || res.html2);
					var audioElements = document.querySelectorAll('.data-audio');
					if (audioElements.length) {
						audioElements.forEach(generateWaveForm);
					}
					@else
					if ($("#code-output").length) {
						$("#workbook_textarea").html(res.data.html2);
						const codeLang = document.querySelector('#code_lang');
						const codePre = document.querySelector('#code-pre');
						const codeOutput = codePre?.querySelector('#code-output');

						if (codeOutput) {
							let codeOutputText = codeOutput.textContent;
							const codeBlocks = codeOutputText.match(/```[A-Za-z_]*\n[\s\S]+?```/g);
							if (codeBlocks) {
								codeBlocks.forEach((block) => {
									const language = block.match(/```([A-Za-z_]*)/)[1];
									const code = block.replace(/```[A-Za-z_]*\n/, '').replace(/```/, '').replace(/&/g, '&amp;').replace(/</g,
										'&lt;').replace(/>/g, '&gt;').replace(
										/"/g, '&quot;').replace(/'/g, '&#039;');
									const wrappedCode = `<pre><code class="language-${language}">${code}</code></pre>`;
									codeOutputText = codeOutputText.replace(block, wrappedCode);
								});
							}

							codePre.innerHTML = codeOutputText;

							codePre.querySelectorAll('pre').forEach(pre => {
								pre.classList.add(`language-${codeLang && codeLang.value !== '' ? codeLang.value : 'javascript'}`);
							})

							// saving for copy
							window.codeRaw = codeOutput.innerText;

							codePre.querySelectorAll('code').forEach(block => {
								Prism.highlightElement(block);
							});
						};
					} else {
						tinymce.activeEditor.destroy();
						$("#generator_sidebar_table").html(res.data.html2);
						getResult();
					}
					@endif
					@if ($openai->type != 'video')
					hideLoadingIndicators();
					@endif
					@if ($openai->type != 'image')
					refreshFsLightbox();
					@endif
				}, 750);
			},
			error: function(data) {
				console.log(data);
				document.getElementById("openai_generator_button").disabled = false;
				document.getElementById("openai_generator_button").innerHTML = "Genarate";
				Alpine.store('appLoadingIndicator').hide();
				document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
				if (data.responseJSON.errors) {
					$.each(data.responseJSON.errors, function(index, value) {
						toastr.error(value);
					});
				} else if (data.responseJSON.message) {
					toastr.error(data.responseJSON.message);
				}
			}
		});
		return false;
	}
</script>
