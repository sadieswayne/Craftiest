@aware(['icon', 'action', 'error'])
@props([
    'icon' => null,
    'action' => null,
    'error' => null,
    'ace' => false,
    'aceMode' => null,
    'aceOptions' => []
])
@if($aceMode)
    @php
    $aceOptions['mode'] = $aceMode;
    @endphp
@endif
<x-form.wrapper>
    <textarea {{ $attributes->class('form-control lqd-input-size-none !w-full rounded-lg') }} @if($ace) x-data="aceEditor('{{ json_encode($aceOptions) }}')" @endif
    @if($error)
        aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
            @endif
    >{{ $slot ?? '' }}</textarea>
</x-form.wrapper>

@pushOnceFor('script:ace')
{{--<script src="{{ Vite::themeAsset('libs/ace/src-min-noconflict/ace.js') }}" type="text/javascript" charset="utf-8"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.35.0/ace.min.js" integrity="sha512-GvzpUyByBrTHeLRBgrFrSnDcXdakzubmOAwoC7Jo2FNayYURzIeX1tRZKGyRxVCfz0Go2EIV69o7i/vvbyywgQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.35.0/mode-html.min.js" integrity="sha512-OuMheKcxCbuxeCERKqwnJPQn4U8J5Nt9TGFUFcr78yQjeJBSRLVdru4fz49bFuSHH/Ki52Fe58S7fZpve3hNsw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}
@endPushOnceFor

@pushOnceFor('css:ace')
<style type="text/css" media="screen">
    .ace_editor {
        min-height: 200px;
    }
</style>
@endPushOnceFor
