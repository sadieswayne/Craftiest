<div
    class="flex w-full flex-col gap-5"
>
    <x-forms.input
        id="open_router_model"
        label="{{ __('Other Models') }}"
        name="open_router_model"
        type="select"
        size="lg"
     >
        <option value="">{{ __('Select Model') }}</option>
        @foreach(\App\Extensions\OpenRouter\System\Enums\OpenRouterEngine::cases() as $engine)
            <option value="{{$engine->value}}">
                {{ $engine->label() }}
            </option>
        @endforeach
    </x-forms.input>
</div>
