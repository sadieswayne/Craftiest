@php
    $filters = ['All', \App\Enums\AITokenType::WORD->value, \App\Enums\AITokenType::IMAGE->value];
@endphp

@extends('panel.layout.settings')
@section('title', __('API Cost Management'))
@section('titlebar_actions', '')
@section('settings')
    <div x-data="{ 'activeFilter': 'All' }">
        <form
            class="flex flex-col gap-5"
            id="cost_form"
            action="{{ route('dashboard.admin.finance.api-cost-management.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            <h4 class="mb-0">
                {{ __('Editing: Active API Integrations') }}
            </h4>
            <label>
                {{ __('Here, you can easily monitor, analyze, and optimize your API usage costs to ensure your AI solutions remain cost-effective. Increase the amount of multiplier if you want to charge more for the related API usage. Only active API solutions will be displayed here.') }}
            </label>

            <ul class="flex w-full justify-between gap-3 rounded-full bg-foreground/10 p-1 text-xs font-medium">
                @foreach ($filters as $filter)
                    <li>
                        <button
                            @class([
                                'px-6 py-3 leading-tight rounded-full transition-all hover:bg-background/80 [&.lqd-is-active]:bg-background [&.lqd-is-active]:shadow-[0_2px_12px_hsl(0_0%_0%/10%)]',
                                'lqd-is-active' => $loop->first,
                            ])
                            @click="activeFilter = '{{ $filter }}'"
                            :class="{ 'lqd-is-active': activeFilter == '{{ $filter }}' }"
                            type="button"
                        >
                            {{ ucfirst($filter) }}
                        </button>
                    </li>
                @endforeach
            </ul>

            @php
                $index = 0;
            @endphp
            @foreach ($groupedAiModels as $category => $groupedAiModel)
                @php
                    $formattedCategory = ucwords(str_replace('_', ' ', $category));
                    $index++;
                @endphp
                <x-form-step
                    step="{{ $index }}"
                    label="{{ str_replace(['ai', 'Ai', 'Aİ'], 'AI', ucfirst($formattedCategory)) }}"
                />

                @foreach ($groupedAiModel as $aiModel)
                    @foreach ($aiModel->tokens as $aiToken)
                        <x-card
                            data-cat="{{ $aiToken->type }}"
                            size="none"
                            variant="shadow"
                            ::class="{ 'hidden': !$el.getAttribute('data-cat')?.includes(activeFilter) && activeFilter !== 'All' }"
                        >
                            <x-forms.input
                                type="number"
                                name="{{ $aiToken->entity_id }}"
                                value="{{ $aiToken->cost_per_token }}"
                                label="{{ __($aiModel->key->value) }}"
                                tooltip="{{ __($aiModel->title) }}"
                                step="0.01"
                                stepper
                            />
                        </x-card>
                    @endforeach
                @endforeach
            @endforeach

            @if ($app_is_demo)
                <x-button
                    type="button"
                    onclick="return toastr.info('This feature is disabled in Demo version.');"
                >
                    {{ __('Save') }}
                </x-button>
            @else
                <x-button
                    id="cost_button"
                    type="submit"
                    form="cost_form"
                >
                    {{ __('Save') }}
                </x-button>
            @endif
        </form>
    </div>
@endsection
