@if ($relatedPosts && filled($relatedPosts))
    <h3 class="mb-16 mt-32 text-center text-[25px]">{{ __('You may also like') }}</h3>
    <div class="mx-auto flex w-2/3 flex-col gap-10 md:flex-row">
        @foreach ($relatedPosts as $post)
            @include('blog.part.card')
        @endforeach
    </div>
@endif
