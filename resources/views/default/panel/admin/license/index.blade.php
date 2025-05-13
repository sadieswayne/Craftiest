@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Update License'))
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        <div class="mx-auto w-full space-y-8 lg:w-1/2">
            @if ($settings_two->liquid_license_type == 'Regular License')
                <x-alert
                    class="justify-center text-center"
                    variant="warn"
                >
                    <p>
                        {{ __('Your are using Regular License. Please upgrade to Extended License.') }}
                        <br>
                        <a
                            class="unerline"
                            href="https://magicaidocs.liquid-themes.com/upgrading-to-extended-license/"
                            target="_blank"
                        >
                            {{ __('How can i upgrade?') }}
                        </a>
                    </p>
                </x-alert>
            @elseif($settings_two->liquid_license_type == 'Extended License')
                <x-alert
                    class="justify-center text-center"
                    variant="success"
                >
                    {{ __('You are using Extended License.') }}
                </x-alert>
            @endif

            @include('vendor.installer.magicai_c4st_Act', ['site_name' => $settings_two->site_name])
        </div>
    </div>

@endsection
