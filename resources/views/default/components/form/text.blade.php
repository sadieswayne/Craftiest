@aware(['icon', 'action', 'size', 'error', 'stepper'])
@props([
    'icon' => null,
    'action' => null,
    'stepper' => null,
    'error' => null,
    'sizes' => [
        'none' => 'lqd-input-size-none rounded-lg',
        'sm' => 'lqd-input-sm h-9 rounded-md',
        'md' => 'lqd-input-md h-10 rounded-lg',
        'lg' => 'lqd-input-lg h-11 rounded-xl',
        'xl' => 'lqd-input-xl h-14 rounded-2xl px-6',
        '2xl' => 'lqd-input-2xl h-16 rounded-full px-8',
    ],
    'size' => 'lg',
    'type' => 'text',
])
<x-form.wrapper>
    <input
        {{ $attributes->class(['form-control', 'form-input-stepper' => $stepper, 'border-2 border-rose-500' => $error]) }}
        :id="$id('text-input')"
        type="{{ $type }}"
        @if ($attributes->has('wire:model')) x-data="{ value: @entangle($attributes->wire('model')) }"
               @if ($stepper)
                   x-model="value"
                    x-on:input="value = (value).toString().includes('.') ? parseFloat(value).toFixed(2) : value"
               @else
                   x-model="value" @endif
        @endif
    @if ($error) aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
    @endif/>
</x-form.wrapper>
