<section
    class="relative flex min-h-[200px] items-center justify-center overflow-hidden pb-28 pt-52 text-center text-black max-md:pb-16 max-md:pt-48"
    id="banner"
>

    <div class="container relative">
        <div class="mx-auto flex w-1/2 flex-col items-center max-lg:w-2/3 max-md:w-full">
            <div class="banner-title-wrap relative">
                @if (isset($post->category))
                    @php $cat = explode(',', $post->category); @endphp
                    <a
                        class="font-normal text-black"
                        href="{{ url('/blog/category', $cat[0]) }}"
                    >
                        <span class="rounded-md bg-gradient-to-r from-purple-100 via-purple-200 to-slate-200 px-4 py-1">
                            {{ $cat[0] }}
                        </span>
                    </a>
                @elseif(isset($hero['subtitle']))
                    <span class="rounded-md bg-gradient-to-r from-purple-100 via-purple-200 to-slate-200 px-4 py-1">{{ __($hero['subtitle']) }}</span>
                @endif
                <h1
                    class="mb-8 mt-4 translate-y-7 font-body text-[55px] font-semibold -tracking-wide text-black opacity-0 transition-all ease-out group-[.page-loaded]/body:translate-y-0 group-[.page-loaded]/body:opacity-100">
                    @if (isset($post->title))
                        {{ $post->title }}
                    @elseif(isset($hero['title']))
                        @if ($hero['type'] == 'author')
                            {{ ucfirst(App\Models\User::where('id', $hero['title'])->first()->name) }}
                        @else
                            {{ $hero['title'] }}
                        @endif
                    @else
                        {{ __('Blog Posts') }}
                    @endif
                </h1>
                @if (isset($hero['description']))
                    <p class="text-[20px] font-medium text-[#0E3F58]">
                        {{ __($hero['description']) }}
                    </p>
                @endif
                @if (isset($post->seo_description))
                    <p class="text-[20px] font-medium text-[#0E3F58]">
                        {{ $post->seo_description }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>
