@extends('panel.layout.settings')
@section('title', __('AI Models'))
@section('titlebar_actions', '')
@section('settings')
    <div x-data="{ 'activeFilter': 'All' }">
        <form
            class="flex flex-col gap-5"
            id="cost_form"
            action="{{ route('dashboard.admin.ai-chat-model.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            <h4 class="mb-0">
                {{ __('Editing: AI Models') }}
            </h4>
            <label>
                {{ __('Manage available AI models visible to users within AI chat, AI Editor and AI Writer : Control the selection and presentation of AI models accessible to users during chat interactions. Manage the availability of models across different pricing plans.') }}
            </label>

            <x-alert class="rounde">
                {{ __('Only activated AI models are displayed here. Make sure to add your API Keys to use all AI Models (OpenAI, Gemini or Anthropic).') }}
                <x-tabler-arrow-up-right class="size-4 inline align-text-bottom"/>
            </x-alert>

            @foreach ($enablesEngines as $aiEngine)
                @php
                    $entitiesDrivers = \App\Domains\Entity\EntityStats::word()->filterByEngine($aiEngine)->list();
                    if(count($entitiesDrivers) === 0) {
                        continue;
                    }
                @endphp
                <x-form-step
                    class="-mb-2"
                    step="{{ $loop->iteration }}"
                    label="{{ $aiEngine->label() }}"
                />

                <div class="w-full space-y-5">
                    @foreach ($entitiesDrivers as $entity)
                        <x-card
                            class="p-2"
                            size="none"
                            variant="shadow"
                        >
                            <x-forms.input
                                type="text"
                                name="selected_title[{{ $entity->model()->id }}]"
                                value="{!! $entity->model()->selected_title !!}"
                                label="{{ __($entity->enum()->value) }}"
                                tooltip="{{ __($entity->label()) }}"
                                labelExtra=""
                                switcher
                            >

                                <x-dropdown.dropdown class="mt-2 w-full">
                                    <x-slot:trigger
                                        class="w-full justify-start text-start"
                                    >
                                        <small>View Included Pricing Plans</small>
                                        <x-tabler-arrow-down class="size-3"/>
                                    </x-slot:trigger>

                                    <x-slot:dropdown
                                        class="min-w-52"
                                    >
                                        <div class="p-2 text-2xs">
                                            @foreach ($plans as $plan)
                                                @php
                                                    $checked = $entity->model()->aiFinance?->pluck('plan_id')?->toArray() ?: [];
                                                @endphp
                                                <x-forms.input
                                                    class:container="h-full bg-input-background mt-2"
                                                    class:label="w-full border h-full rounded px-3 py-4 hover:bg-foreground/5 transition-colors"
                                                    id="ai_model_{{ $entity->enum()->value . '_' . $plan->id }}"
                                                    :checked="in_array($plan->id, $checked, true)"
                                                    type="checkbox"
                                                    name="selected_plans[{{ $entity->model()->id }}][{{ $plan->id }}]"
                                                    value="{{ $plan->id }}"
                                                    label="{{ $plan->name }}"
                                                    custom
                                                />
                                            @endforeach

                                            <x-forms.input
                                                class:container="h-full bg-input-background mt-2"
                                                class:label="w-full border h-full rounded px-3 py-4 hover:bg-foreground/5 transition-colors"
                                                id="ai_model_{{ $entity->enum()->value.'_no_plan_users' }}"
                                                :checked="$entity->model()->is_selected === 1"
                                                type="checkbox"
                                                name="no_plan_users[{{  $entity->model()->id }}]"
                                                value="{{ $entity->model()->id }}"
                                                label="{{ trans('No Plan Users') }}"
                                                custom
                                            />

                                        </div>

                                    </x-slot:dropdown>
                                </x-dropdown.dropdown>
                            </x-forms.input>
                        </x-card>
                    @endforeach
                </div>
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
