@extends('layout.error', ['disable_navbar' => true, 'disable_header' => true])

@section('error_code', '404')
@section('error_title', __('Looks like you’re lost.'))
@section('error_subtitle', __('We can’t seem to find the page you’re looking for.'))
