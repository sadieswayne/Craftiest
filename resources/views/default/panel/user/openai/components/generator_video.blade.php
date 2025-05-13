@php
    $items_per_page = 5;
    $total_items = $userOpenai->total();
@endphp

<div class="flex flex-wrap gap-y-10">
    <x-card
        class="lqd-video-generator border-0 bg-[#F2F1FD] dark:bg-surface"
        size="lg"
    >
        <form
            class="flex flex-col gap-4"
            id="openai_generator_form"
            onsubmit="return sendOpenaiGeneratorForm();"
        >
            <h3>
                {{ __('Source Image') }}
            </h3>

            <div
                class="flex w-full flex-col gap-5"
                ondrop="dropHandler(event, 'img2img_src');"
                ondragover="dragOverHandler(event);"
            >
                <label
                    class="lqd-filepicker-label flex min-h-64 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-foreground/10 bg-background text-center transition-colors hover:bg-background/80"
                    for="img2img_src"
                >
                    <div class="flex flex-col items-center justify-center py-6">
                        <x-tabler-cloud-upload
                            class="mb-4 size-11"
                            stroke-width="1.5"
                        />

                        <p class="mb-1 text-sm font-semibold">
                            {{ __('Drop your image here or browse. 1024x576, 576x1024, 768x768 images are avaiable.') }}
                        </p>

                        <p class="file-name mb-0 text-2xs">
                            {{ __('(Only jpg, png will be accepted)') }}
                        </p>
                    </div>

                    <input
                        class="hidden"
                        id="img2img_src"
                        type="file"
                        accept=".png, .jpg, .jpeg"
                        onchange="handleFileSelect('img2img_src')"
                    />
                </label>
            </div>

            <div class="my-2 flex flex-col flex-wrap justify-between gap-3 md:flex-row">
                <x-forms.input
                    class:container="grow"
                    id="video_seed"
                    label="{{ __('Seed') }}"
                    tooltip="{{ __('A specific value from 0 to 4294967294 that is used to guide the randomness of the generation.') }}"
                    type="number"
                    name="video_seed"
                    min="0"
                    max="4294967294"
                    value="0"
                    size="lg"
                />

                <x-forms.input
                    class:container="grow"
                    id="video_cfg_scale"
                    label="{{ __('Fidelity') }}"
                    tooltip="{{ __('A specific value from 0 to 10 to express how strongly the video sticks to the original image.') }}"
                    type="number"
                    name="video_cfg_scale"
                    min="0"
                    max="10"
                    value="0"
                    size="lg"
                />

                <x-forms.input
                    class:container="grow"
                    id="video_motion_bucket_id"
                    label="{{ __('Motion intensity') }}"
                    tooltip="{{ __('Lower values generally result in less motion in the output video, while higher values generally result in more motion. The range is 0 ~ 255') }}"
                    type="number"
                    name="video_motion_bucket_id"
                    min="1"
                    max="255"
                    value="127"
                    size="lg"
                />
            </div>
            <x-button
                id="openai_generator_button"
                size="lg"
                type="submit"
            >
                {{ __('Generate') }}
                <x-tabler-arrow-right class="size-5" />
            </x-button>
        </form>
    </x-card>

    <div
        class="w-full"
        x-data="{
            modalShow: false,
            activeItem: null,
            activeItemId: null,
            setActiveItem(data) {
                this.activeItem = data;
                this.activeItemId = data.id;
                videoItem = document.querySelector(`.lqd-modal-fig > video`);
                currentVideoItem = document.querySelector(`.lqd-modal-fig > video > source`);

                const currenturl = window.location.href;
                const server = currenturl.split('/')[0];
                const delete_url = `${server}/dashboard/user/openai/documents/delete/image/${data.slug}`;
                deleteVideoBtn = document.querySelector(`.lqd-modal-img-content .delete`);
                deleteVideoBtn.href = delete_url;

                currentVideoItem.src = data.output;
                videoItem.load();

            },
            prevItem() {
                const currentEl = document.querySelector(`.video-result[data-id='${this.activeItemId}']`);
                const prevEl = currentEl?.previousElementSibling;
                if (!prevEl) return;
                const data = JSON.parse(prevEl.querySelector('a.lqd-video-result-view').getAttribute('data-payload') || {});
                this.setActiveItem(data);
            },
            nextItem() {
                const currentEl = document.querySelector(`.video-result[data-id='${this.activeItemId}']`);
                const nextEl = currentEl?.nextElementSibling;
                if (!nextEl) return;
                const data = JSON.parse(nextEl.querySelector('a.lqd-video-result-view').getAttribute('data-payload') || {});
                this.setActiveItem(data);
            },
        }"
        @keyup.escape.window="modalShow = false"
    >
        <h2 class="mb-5">{{ __('Result') }}</h2>
        <div class="video-results grid grid-cols-2 gap-8 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($userOpenai->take($items_per_page) as $item)
                <div
                    class="video-result group w-full"
                    data-id="{{ $item->id }}"
                >
                    <figure
                        class="lqd-video-result-fig relative mb-3 aspect-square overflow-hidden rounded-lg shadow-md transition-all group-hover:-translate-y-1 group-hover:scale-105 group-hover:shadow-lg"
                    >
                        <video
                            class="lqd-video-result-video h-full w-full object-cover object-center"
                            loading="lazy"
                        >
                            <source
                                loading="lazy"
                                src="{{ $item->output }}"
                                type="video/mp4"
                            >
                        </video>
                        <div class="lqd-video-result-actions absolute inset-0 flex w-full flex-col items-center justify-center gap-2 p-4">
                            <div class="flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <x-button
                                    class="lqd-video-result-play size-9 rounded-full bg-background text-foreground hover:bg-primary hover:text-primary-foreground"
                                    data-fslightbox="video-gallery"
                                    size="none"
                                    href="{{ $item->output }}"
                                >
                                    <x-tabler-player-play-filled class="size-4" />
                                </x-button>
                                <x-button
                                    class="lqd-video-result-download size-9 rounded-full bg-background text-foreground hover:bg-primary hover:text-primary-foreground"
                                    size="none"
                                    download="{{ $item->slug }}"
                                    href="{{ $item->output }}"
                                >
                                    <x-tabler-download class="size-5" />
                                </x-button>
                                <x-button
                                    class="lqd-video-result-view size-9 rounded-full bg-background text-foreground hover:bg-primary hover:text-primary-foreground"
                                    data-payload="{{ json_encode($item) }}"
                                    @click.prevent="setActiveItem( JSON.parse($el.getAttribute('data-payload') || {}) ); modalShow = true"
                                    size="none"
                                    href="#"
                                >
                                    <x-tabler-info-circle class="size-5" />
                                </x-button>
                            </div>
                        </div>
                    </figure>
                </div>
            @endforeach
        </div>

        @if ($userOpenai->count() > 0)
            <div
                class="lqd-load-more-trigger group min-h-px w-full py-8 text-center font-medium text-heading-foreground"
                data-all-loaded="false"
            >
                <span class="lqd-load-more-trigger-loading flex items-center justify-center gap-2 text-center leading-tight group-[&[data-all-loaded=true]]:hidden">
                    {{ __('Loading more') }}
                    <span class="flex gap-1">
                        @foreach ([0, 1, 2] as $item)
                            <span
                                class="inline-block h-[3px] w-[3px] animate-bounce-load-more rounded-full bg-current"
                                style="animation-delay: {{ $loop->index / 14 }}s"
                            ></span>
                        @endforeach
                    </span>
                </span>
                <span class="lqd-load-more-trigger-all-loaded hidden items-center justify-center gap-2 text-center group-[&[data-all-loaded=true]]:flex">
                    {{ __('All videos loaded') }}
                    <x-tabler-check class="size-5" />
                </span>
            </div>
        @endif

        <div
            class="lqd-modal-img fixed start-0 top-0 z-[999] hidden h-screen w-screen flex-col items-center border p-3 [&.is-active]:flex"
            id="modal_image"
            x-data="{}"
            :class="{ 'is-active': modalShow }"
        >
            <div
                class="lqd-modal-img-backdrop absolute start-0 top-0 z-0 h-screen w-screen bg-black/10 backdrop-blur-sm"
                @click="modalShow = false"
            ></div>

            <div class="lqd-modal-img-content-wrap relative z-10 my-auto max-h-[90vh] w-full overflow-y-auto">
                <div class="container max-w-6xl">
                    <div class="lqd-modal-img-content relative flex flex-wrap justify-between rounded-xl bg-background !p-5 xl:min-h-[570px]">
                        <a
                            class="absolute end-2 top-3 z-10 flex size-9 items-center justify-center rounded-full border bg-background text-inherit shadow-sm transition-all hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black"
                            @click.prevent="modalShow = false"
                            href="#"
                        >
                            <x-tabler-x class="size-4" />
                        </a>

                        <figure class="lqd-modal-fig relative aspect-square min-h-[1px] w-full rounded-lg bg-cover bg-center max-md:min-h-[350px] md:w-6/12">
                            <video
                                class="lqd-modal-vid mx-auto h-full w-auto"
                                loading="lazy"
                                controls
                            >
                                <source
                                    src=""
                                    type="video/mp4"
                                >
                                <a
                                    class="absolute !bottom-7 !end-7 inline-flex size-9 items-center justify-center rounded-full bg-background text-inherit shadow-sm transition-all hover:scale-105"
                                    href="#"
                                    :href="activeItem?.output"
                                    download
                                    target="_blank"
                                >
                                    <x-tabler-download class="size-5" />
                                </a>
                            </video>
                        </figure>

                        <div class="videoinfo relative flex w-full flex-col p-3 md:!w-5/12">
                            <div class="relative flex flex-col items-start !pb-6">
                                <h3 class="!mb-4">
                                    {{ __('Video Details') }}
                                </h3>
                            </div>

                            <div class="mt-auto flex flex-wrap justify-between !gap-y-3 text-[13px] font-medium">
                                <x-button
                                    class="delete w-full"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    href=""
                                    onclick="return confirm('Are you sure?')"
                                >
                                    {{ __('Delete Video') }}
                                </x-button>
                                <div class="w-full md:!w-[30%]">
                                    <div class="lqd-modal-img-info rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Date')</p>
                                        <p
                                            class="mb-0 opacity-60"
                                            x-text="activeItem?.format_date ?? '{{ __('None') }}'"
                                        ></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="lqd-modal-img-info rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Resolution')</p>
                                        <p
                                            class="mb-0 opacity-60"
                                            x-text="activeItem?.payload.size ?? '{{ __('None') }}'"
                                        ></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="lqd-modal-img-info rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Credit')</p>
                                        <p
                                            class="mb-0 opacity-60"
                                            x-text="activeItem?.credits ?? '{{ __('None') }}'"
                                        >
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prev/Next buttons -->
                        <a
                            class="absolute -start-5 top-1/2 z-10 inline-flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-background text-inherit shadow-sm transition-all hover:scale-110 hover:bg-primary hover:text-primary-foreground"
                            href="#"
                            @click.prevent="prevItem()"
                        >
                            <x-tabler-chevron-left class="size-5" />
                        </a>
                        <a
                            class="absolute -end-5 top-1/2 z-10 inline-flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-background text-inherit shadow-sm transition-all hover:scale-110 hover:bg-primary hover:text-primary-foreground"
                            href="#"
                            @click.prevent="nextItem()"
                        >
                            <x-tabler-chevron-right class="size-5" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="prompt-template">
    <div class="each-prompt d-flex align-items-center mt-3">
        <input
            class="input-required form-control rounded-pill multi_prompts_description border border-primary bg-[#fff] text-[#000] placeholder:text-black placeholder:text-opacity-50 focus:border-white focus:bg-white dark:!border-none dark:!bg-[--lqd-header-search-bg] dark:placeholder:text-[#a5a9b1] dark:focus:!bg-[--lqd-header-search-bg]"
            type="text"
            name="titles[]"
            placeholder="Type another title or description"
            required
        >
        <button
            class="text-heading border-none bg-transparent"
            data-toggle="remove-parent"
            type="button"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 32 32"
                width="24px"
                height="24px"
                fill="currentColor"
            >
                <path
                    d="M 15 4 C 14.476563 4 13.941406 4.183594 13.5625 4.5625 C 13.183594 4.941406 13 5.476563 13 6 L 13 7 L 7 7 L 7 9 L 8 9 L 8 25 C 8 26.644531 9.355469 28 11 28 L 23 28 C 24.644531 28 26 26.644531 26 25 L 26 9 L 27 9 L 27 7 L 21 7 L 21 6 C 21 5.476563 20.816406 4.941406 20.4375 4.5625 C 20.058594 4.183594 19.523438 4 19 4 Z M 15 6 L 19 6 L 19 7 L 15 7 Z M 10 9 L 24 9 L 24 25 C 24 25.554688 23.554688 26 23 26 L 11 26 C 10.445313 26 10 25.554688 10 25 Z M 12 12 L 12 23 L 14 23 L 14 12 Z M 16 12 L 16 23 L 18 23 L 18 12 Z M 20 12 L 20 23 L 22 23 L 22 12 Z"
                />
            </svg>
        </button>
    </div>
</template>

<template id="video_result">
    <div class="video-result lqd-loading-skeleton lqd-is-loading group w-full">
        <figure
            class="lqd-video-result-fig relative mb-3 aspect-square overflow-hidden rounded-lg shadow-md transition-all group-hover:-translate-y-1 group-hover:scale-105 group-hover:shadow-lg"
            data-lqd-skeleton-el
        >
            <video
                class="lqd-video-result-video h-full w-full object-cover object-center"
                loading="lazy"
            >
                <source
                    src=""
                    loading="lazy"
                    type="video/mp4"
                >
            </video>
            <div class="lqd-video-result-actions absolute inset-0 flex w-full flex-col items-center justify-center gap-2 p-4">
                <div class="flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                    <x-button
                        class="lqd-video-result-play size-9 rounded-full bg-background text-foreground hover:bg-primary hover:text-primary-foreground"
                        data-fslightbox="video-gallery"
                        size="none"
                        href="#"
                    >
                        <x-tabler-player-play-filled class="size-4" />
                    </x-button>

                    <x-button
                        class="lqd-video-result-download size-9 rounded-full bg-background text-foreground hover:bg-primary hover:text-primary-foreground"
                        size="none"
                        download="true"
                        href="#"
                    >
                        <x-tabler-download class="size-5" />
                    </x-button>
                    <x-button
                        class="lqd-video-result-view size-9 rounded-full bg-background text-foreground hover:bg-primary hover:text-primary-foreground"
                        @click.prevent="setActiveItem( JSON.parse($el.getAttribute('data-payload') || {})); modalShow = true"
                        size="none"
                        href="#"
                    >
                        <x-tabler-info-circle class="size-5" />
                    </x-button>
                </div>
            </div>
        </figure>
    </div>
</template>

@push('script')
    <script>
        var resizedImage;
        var imageWidth = -1;
        var imageHeight = -1;
        var postImageWidth = -1;
        var postImageHeight = -1;

        const currenturl = window.location.href;
        const server = currenturl.split('/')[0];

        function dropHandler(ev, id) {
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();
            $('#' + id)[0].files = ev.dataTransfer.files;
            resizeImage();
            $('#' + id).prev().find(".file-name").text(ev.dataTransfer.files[0].name);
        }

        function dragOverHandler(ev) {
            ev.preventDefault();
        }

        function handleFileSelect(id) {
            $('#' + id).prev().find(".file-name").text($('#' + id)[0].files[0].name);
        }

        function resizeImage(e) {

            var file;
            file = $("#img2img_src")[0].files[0];
            if (file == undefined) return;
            var reader = new FileReader();

            reader.onload = function(event) {
                var img = new Image();

                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext("2d");

                    imageWidth = this.width;
                    imageHeight = this.height;

                    canvas.width = this.width;
                    canvas.height = this.height;
                    var ctx = canvas.getContext("2d");
                    ctx.drawImage(img, 0, 0, this.width, this.height);

                    var dataurl = canvas.toDataURL("image/png");

                    var byteString = atob(dataurl.split(',')[1]);
                    var mimeString = dataurl.split(',')[0].split(':')[1].split(';')[0];
                    var ab = new ArrayBuffer(byteString.length);
                    var ia = new Uint8Array(ab);
                    for (var i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    var blob = new Blob([ab], {
                        type: mimeString
                    });

                    resizedImage = new File([blob], file.name);
                }
                img.src = event.target.result;
            }

            reader.readAsDataURL(file);

        }
        document.getElementById("img2img_src").addEventListener('change', resizeImage);

        (() => {
            "use strict";

            const itemsPerPage = {{ $items_per_page }};
            let offset = itemsPerPage; // Declare offset globally
            let totalItems = {{ $total_items }};
            let nextCount = Math.min(totalItems - itemsPerPage, itemsPerPage);
            let loadingQueue = [];

            const imageContainer = document.querySelector('.video-results');
            const imageResultTemplate = document.querySelector('#video_result');
            const loadMoreTrigger = document.querySelector('.lqd-load-more-trigger');
            const loadMoreObserver = new IntersectionObserver(([entry], observer) => {
                if (entry.isIntersecting) {
                    if (loadMoreTrigger.classList.contains('lqd-is-loading')) return;
                    createSkeleton(imageResultTemplate, nextCount);
                    lazyLoadImages();
                }
            }, {
                threshold: [1]
            });

            if (loadMoreTrigger) { // Check if the element exists
                loadMoreObserver.observe(loadMoreTrigger);
            }

            function createSkeleton(template, limit = 5) {
                const skeletonTemplates = [];
                for (let i = 0; i < limit; i++) {
                    const skeleton = template.content.cloneNode(true);
                    skeletonTemplates.push(skeleton);
                    imageContainer.append(skeleton);
                    loadingQueue.push(imageContainer.lastElementChild);
                }

                return skeletonTemplates;
            }

            function lazyLoadImages() {
                loadMoreTrigger.classList.add('lqd-is-loading');

                fetch(`{{ route('dashboard.user.openai.lazyloadimage') }}?offset=${offset}&post_type=ai_video`)
                    .then(response => response.json())
                    .then(data => {
                        const videos = data.images;
                        const currenturl = window.location.href;
                        const server = currenturl.split('/')[0];

                        nextCount = Math.min(data.count_remaining, itemsPerPage);

                        videos.forEach((video, index) => {
                            const videoResultTemplate = loadingQueue[index];
                            const delete_url = `${server}/dashboard/user/openai/documents/delete/image/${video.slug}`;

                            videoResultTemplate.setAttribute('data-id', video.id);
                            videoResultTemplate.querySelector('.lqd-video-result-video').setAttribute('src', video.output);
                            videoResultTemplate.querySelector('.lqd-video-result-video source').setAttribute('src', video.output);
                            videoResultTemplate.querySelector('.lqd-video-result-view').setAttribute('data-payload', JSON.stringify(video));

                            videoResultTemplate.querySelector('.lqd-video-result-download').setAttribute('href', video.output);
                            videoResultTemplate.querySelector('.lqd-video-result-download').setAttribute('download', video.slug);
                            videoResultTemplate.querySelector('.lqd-video-result-play').setAttribute('href', video.output);

                            videoResultTemplate.classList.remove('lqd-is-loading');
                        });

                        loadingQueue = [];

                        // Update the offset for the next lazy loading request
                        offset += videos.length;
                        // Refresh lightbox, check if there are more videos
                        refreshFsLightbox();

                        loadMoreTrigger.classList.remove('lqd-is-loading');

                        if (data.count_remaining <= 0) {
                            loadMoreTrigger.setAttribute('data-all-loaded', 'true');
                            loadMoreObserver.disconnect();
                        } else {
                            // check if loadMoreTrigger is in view. if it is load more images
                            if (loadMoreTrigger.getBoundingClientRect().top <= window.innerHeight) {
                                createSkeleton(imageResultTemplate, nextCount);
                                lazyLoadImages();
                            }
                        }

                    });
            }
        })();
    </script>
@endpush
