@aware(['error', 'label', 'error'])
@props([
    'label' => null,
    'tooltip' => null,
    'checked' => false,
    'error' => null,
    'position' => 'right',
    'change' => null,
    'name' => null,
    'xModel' => null,
])
<label
    {{ $attributes->withoutTwMergeClasses()->twMerge('form-check form-switch flex items-center gap-2 mb-0') }}
    :for="$id('text-input')"
>
    @if ($position === 'left')
        @if ($tooltip)
            <x-info-tooltip
                class="block"
                text="{{ $tooltip }}"
            />
        @endif
        <span class="form-check-label !m-0 text-2xs">{{ $label }}</span>
    @endif

    <input
        {{ $attributes->twMergeFor('input', 'form-check-input cursor-pointer') }}
        :id="$id('text-input')"
        type="checkbox"
        @if ($attributes->has('wire:model')) wire:model="{{ $attributes->get('wire:model') }}" @endif
        @if ($error) aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
        @endif
    @if ($checked) checked @endif
    @if ($name) name="{{ $name }}" @endif
    @if ($change) @change="{{ $change }}" @endif
    @if ($xModel) x-model="{{ $xModel }}" @endif
    >

    @if ($position === 'right')
        <span class="form-check-label !m-0 text-2xs">{{ $label }}</span>

        @if ($tooltip)
            <x-info-tooltip
                class="block"
                text="{{ $tooltip }}"
            />
        @endif
    @endif
</label>
