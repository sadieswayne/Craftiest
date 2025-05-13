@extends('blog.app')

@section('content')
    @include('blog.hero')

    <section class="page-content blog-content">
        <div class="container mb-20">
            <div class="mb-16 grid grid-cols-1 gap-14 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    @include('blog.part.card')
                @endforeach
            </div>
            {{ $posts->links('pagination::bootstrap-5-alt') }}
        </div>
    </section>
@endsection
