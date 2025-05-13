@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __($openai->title))
@section('titlebar_subtitle', __($openai->description))

@section('content')
    @php

        $view = match (true) {
            $openai->type === 'image' => 'panel.user.openai.components.generator_image',
            $openai->type === 'video' => 'panel.user.openai.components.generator_video',
            $openai->type === 'voiceover' => 'panel.user.openai.components.generator_voiceover',
            $openai->type === \App\Domains\Entity\Enums\EntityEnum::ISOLATOR->value => 'ai-voice-isolator::generator_voice_isolator',
            $openai->type === 'video-to-video' && \App\Helpers\Classes\MarketplaceHelper::isRegistered('ai-video-to-video') => 'ai-video-to-video::component',
            default => 'panel.user.openai.components.generator_others',
        };
    @endphp
    <div class="py-10">
        @includeIf($view)
    </div>
@endsection

@push('script')
    <script>
        var sayAs = '';
    </script>
    <script src="{{ custom_theme_url('/assets/js/panel/openai_generator.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/fslightbox/fslightbox.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/wavesurfer/wavesurfer.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/voiceover.js') }}"></script>

    @includeWhen($openai->type === 'voiceover' || $openai->type === \App\Domains\Entity\Enums\EntityEnum::ISOLATOR->value, 'panel.user.openai.scripts.voiceover-isolator')

    @includeWhen($openai->type === 'code', 'panel.user.openai.scripts.code')

    @includeWhen($openai->type !== 'video-to-video', 'panel.user.openai.scripts.common')
@endpush
