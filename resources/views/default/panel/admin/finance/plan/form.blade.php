@extends('panel.layout.app')
@section('title', $title)
@section('titlebar_actions', '')


@section('content')
    @if($subscription)
        <livewire:admin.finance.plan.subscription-plan-create :plan="$item" />
    @else
        <livewire:admin.finance.plan.token-pack-plan-create :plan="$item" />
    @endif
@endsection