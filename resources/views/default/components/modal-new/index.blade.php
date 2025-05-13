@props([
    'id' => null,
    'maxWidth' => 'sm',
    'title' => null,
    'triggerAs' => 'button',
    'description' => null,
    'icon' => null,
    'buttons' => null,
    'focusable' => false,
    'cancelAttributes' => [],
    'showCancelButton' => false,
    'cancelButtonText' => __('Cancel'),
    'dismiss' => true,
    'noCancelClick' => false,
    'inlineButtons' => false,
])
@php
    $hasWireModel = $attributes->has('wire:model.live');
    $id = $id ?? md5($hasWireModel ? $attributes->wire('model') : $title ?? $description);

    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth ?? '2xl'];

    if ($showCancelButton || $dismiss) {
        $cancelAttributes = new \Illuminate\View\ComponentAttributeBag($noCancelClick ? $cancelAttributes : array_merge(['@click' => 'closeModal'], $cancelAttributes));
    }

@endphp

<div
        @if ($hasWireModel) x-data="modal({
            show: @entangle($attributes->wire('model')),
            focusable: {{ $focusable ? 'true' : 'false' }},
        })"
        @else
            x-data="modal({
            show: false,
            focusable: {{ $focusable ? 'true' : 'false' }},
        })" @endif
        x-on:close.stop="show = false"
        x-on:keydown.escape.window="show = false"
        x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
        x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
        class="grid"
        {{-- x-show="show" --}}
        id="{{ $id }}"
        {{ $attributes->except('wire:model.live')->merge() }}>
    @isset($trigger)
        @if ($triggerAs === 'button')
            <x-button :attributes="$trigger->attributes->merge([
                '@click' => 'openModal',
            ])">
                {!! $trigger !!}
            </x-button>
        @else
            <a href="#" {{ $trigger->attributes->merge([
                '@click' => 'openModal',
            ]) }}>{!! $trigger !!}</a>
        @endif
    @endisset

    <div
            x-show="show"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            x-id="['modal-title']"
            :aria-labelledby="$id('modal-title')"
            class="fixed inset-0 z-[99] px-4 pt-4 pb-20 sm:pb-32">
        {{-- Overlay --}}
        <div x-show="show" @click="closeModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50"></div>
        {{-- Panel --}}
        <div
                x-show="show"
                x-transition
                @click="closeModal"
                class="relative flex h-full w-full items-center justify-center">
            <div
                    @click.stop
                    {{-- x-trap.noscroll.inert="show" --}}
                    class="{{ $maxWidth }} inline-block transform overflow-hidden rounded-lg bg-gray-50 px-4 pt-5 pb-4 text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:p-6 sm:align-middle">
                <div class="relative flex w-full">
                    <div class="grid gap-4">
                        <div
                                class="@if ($dismiss) justify-items-start @else justify-items-center sm:justify-items-start @endif grid w-full items-center gap-2 sm:flex">
                            @if ($icon)
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-50">
                                    @svg($icon, 'h-5 w-5 text-gray-500 dark:text-gray-100')
                                </div>
                            @endif
                            <h3 class="@if ($dismiss) pr-10 @endif text-base font-medium leading-6 text-gray-900 dark:text-gray-200"
                                :id="$id('modal-title')">{{ $title }}</h3>
                        </div>
                        @if ($description)
                            <div class="mt-1 mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-100">{!! $description !!}</span>
                            </div>
                        @endif
                    </div>
                    @if ($dismiss)
                        <div class="absolute right-0">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" {{ $cancelAttributes }}
                                class="inline-flex rounded-md bg-gray-100/50 p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:bg-gray-300/50 dark:text-gray-300 dark:hover:bg-gray-300 dark:hover:text-gray-50">
                                    <span class="sr-only">{{ __('Dismiss') }}</span>
                                    {{-- Heroicon name: solid/x --}}
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                @unless($inlineButtons)
                    <div class="my-4">
                        {!! $slot ?? '' !!}
                    </div>
                @endif
                @if ($buttons)
                    @if ($inlineButtons)
                        <div
                                class="justify-stretch mt-4 grid w-full items-center gap-y-4 space-x-0 sm:mt-5 sm:mb-0 sm:flex sm:items-end sm:justify-between sm:space-x-4">
                            @endif
                            @if ($inlineButtons)
                                <div class="w-full">
                                    {!! $slot ?? '' !!}
                                </div>
                            @endif
                            <div class="@if ($inlineButtons) self-center  @else mt-5 sm:mt-6 @endif grid justify-center gap-4 sm:flex sm:justify-end">
                                @if ($showCancelButton)
                                    <div>
                                        <x-button
                                                variant="outline"
                                                tag="button"
                                                :attributes="$cancelAttributes">{{ $cancelButtonText }}</x-button>
                                    </div>
                                @endif
                                @if($buttons !== true)
                                    {!! $buttons !!}
                                @endif
                            </div>
                            @if ($inlineButtons)
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
