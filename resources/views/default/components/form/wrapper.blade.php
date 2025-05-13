@aware([
    'action',
    'icon',
    'stepper',
])
@props([
    'action' => null,
    'icon' => null,
    'stepper' => null,
    'force' => false,
    'inputActions' => null,
])
@if($action || $stepper || $icon || $force)
    <div class="relative" @if($stepper)
         x-data="{
            value: @if($attributes->has('wire:model')) @entangle($attributes->wire('model')) @else {{ $attributes->get('value')?? 0 }} @endif,
            min: {{ $attributes->has('min') ? $attributes->get('min') : 0 }},
            max: {{ $attributes->has('max') ? $attributes->get('max') : 999999 }},
            step: {{ $attributes->has('step') ? $attributes->get('step') : 1 }},
        }"
    @endif>
        {{ $slot }}

        {{-- Icon --}}
        @if ($icon)
            {!! $icon !!}
        @endif

        {{-- Action --}}
        @if ($action)
            <div class="absolute inset-y-0 end-0 border-s">
                {{ $action }}
            </div>
        @endif

        @if($inputActions)
            {!! $inputActions !!}
        @endif
    </div>
@else
    {{ $slot }}
@endif