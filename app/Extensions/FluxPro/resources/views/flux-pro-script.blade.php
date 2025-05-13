<script>
    function sendFluxProGeneratorForm(ev) {
        ev?.preventDefault();
        ev?.stopPropagation();

        const submitBtn = document.getElementById("flux_generator_button");

        document.getElementById("flux_generator_button").disabled = true;
        document.getElementById("flux_generator_button").innerHTML = magicai_localize.please_wait;

        Alpine.store('appLoadingIndicator').show();
        submitBtn.classList.add('lqd-form-submitting');
        submitBtn.disabled = true;

        @if ($openai->type == 'image')
        var imageGenerator = document.querySelector('[data-generator-name][data-active=true]')?.getAttribute(
            'data-generator-name');
        @endif
        var formData = new FormData();
        formData.append('post_type', '{{ $openai->slug }}');
        formData.append('openai_id', {{ $openai->id }});
        formData.append('custom_template', {{ $openai->custom_template }});
        formData.append('description', $("#description_flux_pro").val());
        formData.append('model', '{{ setting('fal_ai_default_model', 'flux-pro') }}');
        formData.append('image_number_of_images', 1);
        formData.append('image_generator', 'flux-pro');
        formData.append('image_mood', null);
        formData.append('size', null);
        formData.append('image_style', null);
        formData.append('image_lighting', null);
        formData.append('quality', null);
        formData.append('type', null);
        formData.append('stable_description', null);
        formData.append('negative_prompt', null);
        formData.append('style_preset', null);
        formData.append('sampler', null);
        formData.append('clip_guidance_preset', null);
        formData.append('image_resolution', '1x1');
        formData.append('description_flux_pro', $('#description_flux_pro').val());

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
					document.getElementById("flux_generator_button").disabled = false;
					document.getElementById("flux_generator_button").innerHTML = "Regenerate";

					toastr.error(res.message);
                    hideLoadingIndicators();
                    return;
                }

//show successful message
                @if ($openai->type == 'image')
                toastr.success(`Image Generated Successfully in ${res.image_storage}`);
                @elseif ($openai->type == 'video')
                    resultVideoId = res.id;
                @else
                toastr.success("{{ __('Generated Successfully!') }}");
                @endif

                document.getElementById("flux_generator_button").disabled = false;
                document.getElementById("flux_generator_button").innerHTML = "Regenerate";
                Alpine.store('appLoadingIndicator').hide();

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
                            'data-generator', 'fl');
                        imageResultTemplate.querySelector('.lqd-image-result-img')
                            .setAttribute('src', image.output);
                        imageResultTemplate.querySelector('.lqd-image-result-img')
                            .setAttribute('id', image.img_id);
                        imageResultTemplate.querySelector('.lqd-image-result-type')
                            .innerHTML = 'FL';
                        imageResultTemplate.querySelector('.lqd-image-result-view')
                            .setAttribute('data-payload', JSON.stringify(image));

                        imageResultTemplate.querySelector('.lqd-image-result-view')
                            .setAttribute('id', image.img_id + '-payload');

                        imageResultTemplate.querySelector('.lqd-image-result-delete')
                            .setAttribute('href', delete_url);
                        imageResultTemplate.querySelector('.lqd-image-result-download')
                            .setAttribute('href', image.output);
                        imageResultTemplate.querySelector('.lqd-image-result-download')
                            .setAttribute('id', image.img_id + '-download');

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
                    @elseif ($openai->type == 'audio' || $openai->type == 'isolator')
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
                document.getElementById("flux_generator_button").disabled = false;
                document.getElementById("flux_generator_button").innerHTML = "Regenerate";
                Alpine.store('appLoadingIndicator').hide();
                document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
                if (data.responseJSON.errors) {
                    $.each(data.responseJSON.errors, function(index, value) {
                        toastr.error(value);
                    });
                } else if (data.responseJSON.message) {
                    toastr.error(data.responseJSON.message);
                }

				return false;
            }
        });

		return false;
    }


    function checkImageStatus() {
        fetch('/dashboard/user/openai/generator/check/status')
            .then(response => response.json())
            .then(data => {
                // console.log(data);
                if(data.data) {
                    for (const [id, item] of Object.entries(data.data)) {
                        let imgElement = document.getElementById(item.imgId);
                        let imgElementPayloadId = document.getElementById(item.payloadId);
                        let imgElementDownload = document.getElementById(item.imgId+'-download');
                        if (imgElement) {
                            imgElement.src = item.img;
                            imgElementDownload.setAttribute('href', item.img)
                            imgElementDownload.setAttribute('target', '_blank')
                            imgElementPayloadId.setAttribute('data-payload', JSON.stringify(item));
                            refreshFsLightbox();
                        }
                    }
                }

            })
            .catch(error => console.error('Error:', error));
    }

    document.addEventListener('DOMContentLoaded', function () {
        setInterval(checkImageStatus, 5000);
    });
</script>
