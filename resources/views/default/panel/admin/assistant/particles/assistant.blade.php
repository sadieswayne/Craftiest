<div :class="{ 'hidden': activeTab !== 'assistant' }">

    <x-form-step
            step="1"
            label="{{ __('Assistant Settings') }}"
            style="margin-bottom: 20px;"
    />

    <x-forms.input
            id="name"
            name="name"
            size="lg"
            placeholder="{{ __('Type your name here') }}"
            label="{{ __('Assistant Name') }}"
            value="{{ $assistant ? $assistant['name'] : '' }}"
            style="margin-bottom: 20px;"
    />

    <x-forms.input
            id="content_text"
            name="instructions"
            placeholder="{{ __('Type your content here') }}"
            label="{{ __('Instructions') }}"
            rows="6"
            type="textarea"
            size="lg"
            style="margin-bottom: 20px;"
    >{{ $assistant ? $assistant['instructions'] : '' }}</x-forms.input>

    <x-forms.input
            id="description"
            name="description"
            placeholder="{{ __('Type your description here') }}"
            label="{{ __('Description') }}"
            rows="6"
            type="textarea"
            size="lg"
            style="margin-bottom: 20px;"
    >{{ $assistant ? $assistant['description'] : '' }}</x-forms.input>

    <x-forms.input
            type="select"
            size="lg"
            name="model"
            label="{{ __('Models') }}"
    >

       @if(!empty($models))
            @foreach($models as $model)
                <option value="{{ $model['id'] }}" {{ $assistant && $assistant['model'] == $model['id'] ? 'selected' : '' }}>
                    {{ $model['id'] }}
                </option>
            @endforeach
       @endif

    </x-forms.input>

</div>
