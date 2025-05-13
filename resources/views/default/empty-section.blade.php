@php
    $sales_prev_week = cache('sales_previous_week');
    $sales_this_week = cache('sales_this_week');

    $popular_tools_data = cache('popular_tools_data');
    $popular_plans_data = cache('popular_plans_data');
    $user_behavior_data = cache('user_behavior_data');
    $currencySymbol = currency()->symbol;
@endphp

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Overview'))

@section('content')

@endsection

@push('script')

@endpush
