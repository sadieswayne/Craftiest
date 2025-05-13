@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', $title)
@section('titlebar_actions', '')

@section('settings')
    <form
            class="flex flex-col gap-5"
            id="user_edit_form"
            method="post"
            enctype="multipart/form-data"
            action="{{ $action }}"
    >
        @csrf
        @method($method)

        <div class="space-y-3">
            @if($chatbot)
                <livewire:chatbot-domains :chatbot="$chatbot" wire:key="chatbots-{{ $chatbot->id }}" />
            @else
                <x-alert variant="info" class="mt-1 w-full py-2.5">
                    <p>
                        {{ __('No chatbot yet.') }}
                    </p>
                </x-alert>
            @endif
        </div>
    </form>

@endsection