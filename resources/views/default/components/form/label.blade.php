@aware(['id', 'label', 'tooltip', 'error'])
@props([
    'id' => null,
    'tooltip' => null,
    'label' => null,
    'error' => null,
])
<label
    {{ $attributes->class(['form-label']) }}
    :for="$id('text-input')"
>
    <span>{{ $label }}</span>

    @if ($tooltip)
        <x-info-tooltip
            class="ml-2 block"
            text="{{ $tooltip }}"
        />
    @endif
</label>
