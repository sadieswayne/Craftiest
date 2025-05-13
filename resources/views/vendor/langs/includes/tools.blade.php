<div class="row">

    <div class="col-md-12 mb-2">
        <div class="alert alert-danger" role="alert">
            <strong>{{__('Take a backup before process!')}}</strong><br>
            {{__('If you have previously created or edited a language file (JSON), the Generate process will overwrite those files.')}}
        </div>
    </div>

    {{--
    <div class="flex justify-between mb-4">
            <a href="{{route('elseyyid.translations.home')}}" class="btn btn-default flex space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M3.6 9h16.8"></path><path d="M3.6 15h16.8"></path><path d="M11.5 3a17 17 0 0 0 0 18"></path><path d="M12.5 3a17 17 0 0 1 0 18"></path></svg>
                {{__('All Locations')}}
            </a>

        <a href="{{route('elseyyid.translations.lang.reinstall')}}" class="btn btn-default flex space-x-2 ml-auto mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M19.933 13.041a8 8 0 1 1 -9.925 -8.788c3.899 -1 7.935 1.007 9.425 4.747"></path><path d="M20 4v5h-5"></path></svg>
            {{__('Reinstall Language Files')}}
        </a>

        <a href="{{route('elseyyid.translations.lang.publishAll')}}" class="btn btn-primary flex space-x-2">
            <svg class="ml-2" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8"></path><path d="M3 10h18"></path><path d="M10 3v18"></path><path d="M16 22l5 -5"></path><path d="M21 21.5v-4.5h-4.5"></path></svg>
            {{__('Publish All Json Files')}}
        </a>
    </div>

    <div class="col-12 mb-3">
        <form action="{{route('elseyyid.translations.lang.search')}}" class="relative" method="GET">
            <input type="text" class="form-control rounded-full" name="search" id="new-search" placeholder="{{__('Search')}}">
            <button type="submit" class='absolute top-1.5 right-1.5 btn btn-success btn-block flex space-x-2'>
                {{__('Search')}}
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
            </button>
        </form>
    </div>
   --}}

    <div class="col-md-6 mb-2">
        <form action="{{route('elseyyid.translations.lang.newLang')}}" class="relative" method="GET" onSubmit="if(!confirm('{{__('Are you sure you want to create a new language?')}}')){return false;}">
            <select class="form-control rounded-full bg-[#F1EDFF]" name="newLang" id="new-lang">
                <option value="" disabled selected>{{__('Add new language ↓')}}</option>
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @if( !in_array($localeCode, getDefinedLangs()) )
                        <option value="{{$localeCode}}"><span class="text-[21px] !me-2">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }} </span>{{ucfirst($properties['native'])}}</option>
                    @endif
                @endforeach
            </select>
            <button @if($app_is_demo) type="button" onclick="return toastr.info('This feature is disabled in Demo version.')" @else type="submit" @endif class='btn btn-default btn-block absolute top-2.5 right-2.5 flex p-1'>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 5l0 14"></path>
                    <path d="M5 12l14 0"></path>
                 </svg>
            </button>
        </form>
    </div>

    <div class="col-md-6 mb-2">
        <form action="{{route('elseyyid.translations.lang.setLocale')}}" class="relative" method="GET" onSubmit="if(!confirm('{{__('Are you sure you want to change default language?')}}')){return false;}">
            <select class="form-control rounded-full bg-[#F1EDFF]" name="setLocale" id="setLocale">
                <option value="" disabled selected>{{__('Select Default Language ↓')}}</option>
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @if(in_array( $localeCode, explode(',', $settings_two->languages) ))
                        <option value="{{$localeCode}}" @if( $settings_two->languages_default === $localeCode) {{'selected'}} @endif><span class="text-[21px] !me-2">{{ country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)) }}</span> {{ucfirst($properties['native'])}} @if( $settings_two->languages_default === $localeCode){{__('(Default Language)')}}@endif</option>
                    @endif
                @endforeach
            </select>
            <button @if($app_is_demo) type="button" onclick="return toastr.info('This feature is disabled in Demo version.')" @else type="submit" @endif class='btn btn-default btn-block absolute top-2.5 right-2.5 flex p-1'>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 5l0 14"></path>
                    <path d="M5 12l14 0"></path>
                 </svg>
            </button>
        </form>
    </div>

    <div class="col-12 mt-4 mb-4">
        <hr>
    </div>
</div>
