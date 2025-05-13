<div magic-load>
    @include('panel.chatbot.widget')
</div>

@assets
<style>
    [magic-load] {
        display: none! important;
    }
</style>
<link rel="stylesheet" href="{{ url('/themes/default/assets/css/frontend/flickity.min.css', secure: true) }}">

{!! \App\Support\Chatbot\ChatbotHelper::getViteAssets('resources/views/default/js/chatbotApp.js') !!}

<script src="{{ url('/themes/default/assets/libs/jquery/jquery.min.js', secure: true) }}"></script>

{{--<script>--}}
{{--    document.querySelectorAll('[magic-load]').forEach(function (element) {--}}
{{--        element.removeAttribute('magic-load');--}}
{{--    });--}}
{{--</script>--}}
@endassets