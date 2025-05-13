@extends('panel.layout.app')

@section('title', __('Team Members'))
@section('titlebar_actions', '')
@section('content')
    <div class="py-10">
        <div class="flex flex-wrap gap-y-8">
            @include('panel.user.team.partials.overview')
            @include('panel.user.team.partials.invite-a-friend')
            @include('panel.user.team.partials.team-members')
        </div>
    </div>
@endsection
