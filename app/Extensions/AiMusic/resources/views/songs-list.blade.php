@if (filled($list))
	<div
		class="image-results lqd-ai-videos-list grid grid-cols-1 gap-9 md:grid-cols-2 lg:grid-cols-4"
		x-data="{
                        activeIndex: -1,
                        videoSrc: null,
                        videoLoading: true,
                        setVideoSrc(src) {
                            if (!src || src === '') {
                                this.$refs.video.pause();
                                window.document.documentElement.classList.remove('overflow-hidden');
                            } else {
                                window.document.documentElement.classList.add('overflow-hidden');
                            }

                            this.videoLoading = true;
                            this.videoSrc = src;

                            if (src) {
                                this.$nextTick(() => {
                                    this.$refs.video.addEventListener('loadeddata', () => {
                                        this.videoLoading = false;
                                        this.$refs.video?.play();
                                    });
                                });
                            }
                        },
                        setActiveIndex({ action, index }) {
                            if (action === '<') {
                                this.activeIndex = this.activeIndex === 0 ? this.activeIndex : this.activeIndex - 1;
                            } else if (action === '>') {
                                this.activeIndex = this.activeIndex === {{ count($list) - 1 }} ? this.activeIndex : this.activeIndex + 1;
                            } else {
                                this.activeIndex = index;
                            }
                        },
                        prevVideo() {
                            this.setActiveIndex({ action: '<' });
                            this.setVideoSrc(this.$refs[`image-result-${this.activeIndex}`]?.getAttribute('data-video-src'));
                        },
                        nextVideo() {
                            this.setActiveIndex({ action: '>' });
                            this.setVideoSrc(this.$refs[`image-result-${this.activeIndex}`]?.getAttribute('data-video-src'));
                        },
                        getPrevVideo() {
                            return this.$refs[`image-result-${this.activeIndex - 1}`]?.getAttribute('data-video-src');
                        },
                        getNextVideo() {
                            return this.$refs[`image-result-${this.activeIndex + 1}`]?.getAttribute('data-video-src');
                        }
                    }"
		x-on:keydown.escape.window="setVideoSrc(null)"
	>
		@foreach ($list as $entry)
			@include('ai-music::song-item', ['entry' => $entry, 'loop' => $loop])
		@endforeach

		{{-- Modal --}}
		<div
			class="ai-avatar-videos-modal group/modal invisible fixed inset-0 z-[9999] grid h-screen w-screen grid-cols-1 place-content-center opacity-0 transition-all duration-500 [&.active]:pointer-events-auto [&.active]:visible [&.active]:opacity-100"
			:class="{ 'active': videoSrc != null }"
		>
			<div
				class="ai-avatar-videos-modal-backdrop absolute inset-0 bg-black/20 backdrop-blur-sm"
				@click="setVideoSrc(null)"
			></div>
			<button
				class="size-10 absolute end-5 top-5 z-3 inline-grid place-content-center rounded-full bg-white/80 text-black transition-all hover:border-red-500 hover:bg-red-500 hover:text-white"
				@click="setVideoSrc(null)"
				type="button"
			>
				<x-tabler-x class="size-4"/>
			</button>

			<div class="ai-avatar-videos-modal-video-wrap relative z-1 flex w-full items-center justify-between gap-4 p-5">
				<button
					class="size-10 z-3 inline-grid place-content-center rounded-full bg-white/80 text-black transition-all hover:border-red-500 hover:bg-red-500 hover:text-white disabled:pointer-events-none disabled:opacity-50"
					@click="prevVideo()"
					type="button"
					:disabled="getPrevVideo() == null || getPrevVideo() == ''"
				>
					<x-tabler-chevron-left class="size-4"/>
				</button>

				<div class="flex aspect-video max-h-[80vh] max-w-[calc(100vw-5rem)] grow items-center justify-center">
					<video
						class="ai-avatar-videos-modal-video max-h-full max-w-full"
						src="#"
						x-bind:src="videoSrc"
						x-ref="video"
						controls
					></video>
				</div>

				<button
					class="size-10 z-3 inline-grid place-content-center rounded-full bg-white/80 text-black transition-all hover:border-red-500 hover:bg-red-500 hover:text-white disabled:pointer-events-none disabled:opacity-50"
					@click="nextVideo()"
					type="button"
					:disabled="getNextVideo() == null || getNextVideo() == ''"
				>
					<x-tabler-chevron-right class="size-4"/>
				</button>
			</div>
		</div>
	</div>
@endif
