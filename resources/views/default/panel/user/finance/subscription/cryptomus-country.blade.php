@extends('panel.layout.app')
@section('title', __('Subscription Payment'))

@section('titlebar_actions', '')

@section('additional_css')
    <style>
        #bank-form {
            width: 100%;
            align-self: center;
            box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.055), 0px 2px 5px 0px rgba(50, 50, 93, 0.068), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.021);
            border-radius: 7px;
            padding: 40px;
        }
        .hidden {
            display: none;
        }
    </style>
@endsection

@section('content')
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-8 col-lg-8">

                    <form>
                        @csrf
                        <div class="section">
                            <div>
                                <label class="mb-2">@lang('Country')</label>
                                <select class="form-control mb-3 mt-2" name="country_code">
                                    @foreach(\App\Services\CountryCodeService::countryCodes($without = []) as $code => $country)
                                        <option {{ request()->header('CF-IPCountry') == $code ? 'selected' : '' }} value="{{ $code }}"> {{ $country. " ($code)" }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <x-button type="submit" class="mb-3 full">
                                Selected Country
                            </x-button>
                        </div>
                    </form>
                    <p>{{ __('By purchasing you confirm our') }} <a href="{{ url('/') . '/terms' }}">{{ __('Terms and Conditions') }}</a> </p>
                </div>
            </div>
        </div>
    </div>
@endsection

