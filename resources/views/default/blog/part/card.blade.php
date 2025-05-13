<article class="flex w-full flex-col rounded-2xl shadow-[0_2px_4px_rgba(149,146,157,0.15)]">
    <figure>
        <a href="{{ url('/blog', $post->slug) }}">
            <img
                class="h-40 w-full rounded-t-2xl object-cover"
                src="{{ custom_theme_url($post->feature_image, true) }}"
                alt="{{ $post->title }}"
            >
        </a>
    </figure>
    <div class="flex min-h-[180px] flex-col p-5 font-medium">
        <div class="mb-3 flex justify-between space-x-6 text-black">
            <time
                class="text-sm"
                datetime="{{ $post->updated_at }}"
            >{{ date('d M', strtotime($post->updated_at)) }}</time>
            <div class="relative -top-2 grow border-b"></div>
            <a
                class="text-sm"
                href="{{ url('/blog/author', $post->user_id) }}"
            >{{ App\Models\User::where('id', $post->user_id)->first()?->name }}</a>
        </div>
        <h2 class="mb-4 !text-[21px] leading-[26px] tracking-tight"><a href="{{ url('/blog', $post->slug) }}">{{ $post->title }}</a></h2>
        <a
            class="mt-auto flex items-center text-[13px] text-black"
            href="{{ url('/blog', $post->slug) }}"
        >
            {{ __('Read More') }}
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    stroke="none"
                    d="M0 0h24v24H0z"
                    fill="none"
                ></path>
                <path d="M9 6l6 6l-6 6"></path>
            </svg>
        </a>
    </div>
</article>
