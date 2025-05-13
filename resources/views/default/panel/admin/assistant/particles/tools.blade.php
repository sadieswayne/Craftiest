@php
    $fileSearchActive = false;
    $codeInterpreterActive = false;

    if (isset($assistant["tools"])) {
        foreach ($assistant["tools"] as $tool) {
            if (isset($tool['type'])) {
                if ($tool['type'] === 'file_search') {
                    $fileSearchActive = true;
                }
                if ($tool['type'] === 'code_interpreter') {
                    $codeInterpreterActive = true;
                }
            }
        }
    }
@endphp



<div
        class="hidden"
        :class="{ 'hidden': activeTab !== 'tools' }"
>

    <x-form-step
            step="1"
            label="{{ __('Options') }}"
            style="margin-bottom: 20px;"
    />

    <x-forms.input
            type="select"
            size="lg"
            name="file_search"
            label="{{ __('File search') }}"
            style="margin-bottom: 20px;"
    >
        <option value="1" {{ $fileSearchActive ? 'selected' : '' }}>{{ __('Active') }}</option>
        <option value="0" {{ !$fileSearchActive ? 'selected' : '' }}>{{ __('Passive') }}</option>
    </x-forms.input>

    <x-forms.input
            type="select"
            size="lg"
            name="code_interpreter"
            label="{{ __('Code interpreter') }}"
            style="margin-bottom: 20px;"
    >
        <option value="1" {{ $codeInterpreterActive ? 'selected' : '' }}>{{ __('Active') }}</option>
        <option value="0" {{ !$codeInterpreterActive ? 'selected' : '' }}>{{ __('Passive') }}</option>
    </x-forms.input>

    <x-forms.input
            id="rangeInput"
            name="temperature"
            placeholder="{{ __('Select a value between 0 and 1') }}"
            label="{{ __('Temperature') }}"
            type="range"
            min="0"
            max="2"
            step="0.01"
            size="lg"
            style="margin-bottom: 20px;"
            value="{{ $assistant ? $assistant['temperature'] : 1 }}"
            oninput="updateRangeValue(this.value)"
    />

    <x-forms.input
            id="rangeInput"
            name="top_p"
            placeholder="{{ __('Select a value between 0 and 1') }}"
            label="{{ __('Top P') }}"
            type="range"
            min="0"
            max="1"
            step="0.01"
            size="lg"
            style="margin-bottom: 20px;"
            value="{{ $assistant ? $assistant['top_p'] : 0.5 }}"
            oninput="updateRangeValue(this.value)"
    />

</div>

@push("script")
    <script>
        function updateRangeValue(value) {
            document.getElementById('rangeValue').textContent = value;

        }
    </script>
@endpush

