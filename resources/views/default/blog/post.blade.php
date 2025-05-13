@extends('blog.app')

@section('content')
    @include('blog.hero')

    <section class="page-content blog-single-page">
        <div class="container mb-20">

            @if (isset($post->feature_image) && !empty($post->feature_image))
                <div class="feature-image mx-auto mb-10">
                    <img class="w-full rounded-3xl" src="{{ custom_theme_url($post->feature_image, true) }}"
                        alt="{{ $post->title }}">
                </div>
            @endif
            <div class="blog-content content mx-auto w-full lg:w-9/12">
                {!! $post->content !!}
            </div>

            @include('blog.part.tag-share')
            <hr class="mb-10 mt-10">
            @include('blog.part.author')
            <hr class="mb-10 mt-10">
            @include('blog.part.prev-next')
            @include('blog.part.related')
        </div>
    </section>
@endsection
