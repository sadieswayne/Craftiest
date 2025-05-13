@extends('vendor.installer.layouts.master', ['stepShow' => false])

@section('template_title')
    {{ trans('Activate you license') }}
@endsection

@section('title')
    {{ trans('installer_messages.welcome.title') }}
@endsection

@section('container')
    <div class="mx-auto">
        @includeWhen(is_null($portal), 'vendor.installer.magicai_c4st_Act', ['button' => 'flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white', 'target' => '','return_url' => route('LaravelInstaller::license') . '?license=verified'])
        @includeWhen($portal, 'vendor.installer.magicai_license_token', ['button' => 'flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white'])
    </div>
@endsection
