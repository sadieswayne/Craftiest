@extends(config('elseyyid-location.layout'))
@section(config('elseyyid-location.content_section'))
        @include('langs::includes.tools')
        <h2 class="text-center">{{__('Search Result for')}} '{{$search_value }}'</h2>
        @if (count($result) > 0)
            <div class="container">
                @foreach ($result as $element)
                    <div class="row">
                        <div class="col-10">
                            {{$element->en}} <br>
                        </div>
                        <div class="col-2 text-right">
                            <a href="{{route('elseyyid.translations.lang.string',$element->code)}}" class="btn btn-xs btn-default">
                                {{__('Show')}} 
                                <svg class="ml-2" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.76045 10.3668C7.18545 9.79279 6.83545 9.01279 6.83545 8.13779C6.83545 6.38479 8.24745 4.97179 9.99945 4.97179C10.8664 4.97179 11.6644 5.32279 12.2294 5.89679" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.1049 8.6989C12.8729 9.9889 11.8569 11.0069 10.5679 11.2409" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.65451 13.4723C3.06751 12.2263 1.72351 10.4063 0.749512 8.1373C1.73351 5.8583 3.08651 4.0283 4.68351 2.7723C6.27051 1.5163 8.10151 0.834297 9.99951 0.834297C11.9085 0.834297 13.7385 1.5263 15.3355 2.7913" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M17.4473 4.9908C18.1353 5.9048 18.7403 6.9598 19.2493 8.1368C17.2823 12.6938 13.8063 15.4388 9.99929 15.4388C9.13629 15.4388 8.28529 15.2988 7.46729 15.0258" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </div>
                    <hr class="mt-2">
                @endforeach
            </div>
            @else
                <div class="col-xs-12">
                    <h3>No results for {{ $search_value }}</h3>
                </div>
        @endif
@endsection
