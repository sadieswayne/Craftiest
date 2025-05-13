@extends(config('elseyyid-location.layout'))
@section(config('elseyyid-location.content_section'))
        @include('langs::includes.tools')
        <h2 class="text-center">String {{$string->en}} ({{$string->code}})</h2>

        @foreach (collect($string)->except(['code','created_at','updated_at'])->toArray() as $key => $element)
            <div class="mb-2" ><code class="rounded-md px-2">{{$key}} {{country2flag($key)}}</code></div>
            <a href="#" class="testEdit" data-type="textarea" data-column="code" data-url="{{url('translations/lang/update/'.$string->code)}}" data-pk="{{$string->code}}" data-title="change" data-name="{{$key}}">{{$element}}</a> <br>
            <hr class="mt-3">

        @endforeach

@endsection
@section(config('elseyyid-location.scripts_section'))
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
$.fn.editable.defaults.mode = 'inline';
$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary btn-sm editable-submit"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M9 15l2 2l4 -4"></path></svg></button><button type="button" class="btn btn-default btn-sm editable-cancel"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M10 12l4 4m0 -4l-4 4"></path></svg></button>';
$(document).ready(function() {
    $('.testEdit').editable({
        rows: 3,
        params: function(params) {
            // add additional params from data-attributes of trigger element
            params.name = $(this).editable().data('name');
            return params;
        },
        error: function(response, newValue) {
            if(response.status === 500) {
                return 'Server error. Check entered data.';
            } else {
                return response.responseText;
                // return "Error.";
            }
        }
    });
});
</script>
@endsection
