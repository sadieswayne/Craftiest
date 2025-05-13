@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Payment Gateways'))
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        {!! $view !!}
    </div>
@endsection
