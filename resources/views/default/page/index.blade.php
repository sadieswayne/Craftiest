@extends('blog.app')

@section('content')
    @if ($page->titlebar_status)
        <section
            class="site-section relative flex min-h-[200px] items-center justify-center overflow-hidden bg-[#4384ea] pb-52 pt-64 text-center text-white max-md:pb-16 max-md:pt-48"
            id="banner"
        >
            <div class="absolute start-0 top-0 h-full w-full overflow-hidden">
                <div class="banner-bg absolute left-0 top-0 h-full w-full"></div>
            </div>
            <div class="container relative">
                <div class="mx-auto flex w-1/2 flex-col items-center max-lg:w-2/3 max-md:w-full">
                    <div class="banner-title-wrap relative">
                        <h1
                            class="banner-title translate-y-7 font-body font-bold -tracking-wide text-white opacity-0 transition-all ease-out group-[.page-loaded]/body:translate-y-0 group-[.page-loaded]/body:opacity-100">
                            {{ $page->title }}
                        </h1>
                    </div>
                </div>
            </div>
            <div class="banner-divider absolute inset-x-0 -bottom-[2px]">
                <svg
                    class="h-auto w-full fill-background"
                    width="1440"
                    height="105"
                    viewBox="0 0 1440 105"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    preserveAspectRatio="none"
                >
                    <path d="M0 0C240 68.7147 480 103.072 720 103.072C960 103.072 1200 68.7147 1440 0V104.113H0V0Z" />
                </svg>
            </div>
        </section>
    @endif
    <section class="page-content page-single-content">
        <div class="container py-36">
            <div class="row">
                <div class="mx-auto w-full lg:w-10/12 xl:w-8/12">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </section>
@endsection
