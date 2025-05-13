@php
    $layout = isset($layout) ? $layout : 'default';
    $col_class = 'mx-auto w-full';

    switch ($layout) {
        case 'wide':
            $col_class .= ' lg:w-1/2';
            break;
        case 'fullwidth':
            $col_class .= '';
            break;
        default:
            $col_class .= ' lg:w-5/12';
            break;
    }
@endphp

@extends('panel.layout.app')

@section('content')
    <div class="lqd-page-settings py-10">
        <div class="{{ $col_class }}">
            <x-card
                class="lqd-settings-card"
                variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
                size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : 'lg' }}"
                roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
            >
                @yield('settings')
            </x-card>
        </div>
    </div>
@endsection
