@php
    $voice_tones = ['Professional', 'Funny', 'Casual', 'Excited', 'Witty', 'Sarcastic', 'Feminine', 'Masculine', 'Bold', 'Dramatic', 'Grumpy', 'Secretive'];
@endphp

@extends('panel.layout.settings')
@section('title', __('Brand Voice'))
@section('titlebar_subtitle',
    __('Generate AI content exclusive to your brand and eliminate the need for repetitive
    introductions of your company.'))
@section('titlebar_actions')
    <div class="flex space-x-1 lg:justify-end">
        <x-button
            variant="ghost-shadow"
            href="{{ route('dashboard.user.brand.index') }}"
        >
            {{ __('Manage Voices') }}
        </x-button>
        <x-button href="{{ route('dashboard.user.brand.create') }}">
            <x-tabler-plus class="size-4" />
            {{ __('New Company') }}
        </x-button>
    </div>
@endsection

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="custom_company_form"
        enctype="multipart/form-data"
    >
        @csrf
        @method($method)
        <x-form-step
            step="1"
            label="{{ __('Company') }}"
        />

        <x-forms.input
            id="c_name"
            size="lg"
            label="{{ __('Company Name') }}"
            tooltip="{{ __('Enter the name of your company or organization.') }}"
            placeholder="{{ __('The official name of your business entity.') }}"
            name="c_name"
            required
            value="{{ $item?->name }}"
        />

        <x-forms.input
            id="c_industry"
            size="none"
            name="c_industry"
            type="select"
            multiple
            label="{{ __('Industry') }}"
            tooltip="{{ __('The field or sector of business activity your company primarily belongs to.') }}"
            add-new
        >
            @foreach (explode(',', $item?->industry) ?? [] as $industry)
                @if ($industry == null)
                    @continue
                @endif
                <option
                    value="{{ $industry }}"
                    @selected($industry !== '')
                >
                    {{ $industry }}
                </option>
            @endforeach
        </x-forms.input>

        <x-forms.input
            id="c_description"
            label="{{ __('Description') }}"
            tooltip="{{ __('A concise summary describing your company, its mission, and what sets it apart.') }}"
            type="textarea"
            rows="3"
            required
            name="c_description"
            size="lg"
            placeholder="{{ __('Provide a brief description of your company.') }}"
        >{{ $item?->description }}</x-forms.input>

        <x-forms.input
            id="c_website"
            label="{{ __('Website') }}"
            tooltip="{{ __('Please provide the full web address (URL) of your company’s official website.') }}"
            name="c_website"
            size="lg"
            placeholder="{{ __('Enter the URL of your company’s website.') }}"
            value="{{ $item?->website }}"
        />

        <x-forms.input
            id="c_tagline"
            label="{{ __('Tagline') }}"
            tooltip="{{ __('A memorable and succinct phrase encapsulating your company’s mission or value proposition.') }}"
            size="lg"
            name="c_tagline"
            placeholder="{{ __('Write a catchy tagline for your company.') }}"
            value="{{ $item?->tagline }}"
        />

        <x-forms.input
            id="tone_of_voice"
            size="lg"
            type="select"
            label="{{ __('Tone of Voice') }}"
            name="tone_of_voice"
        >
            @foreach ($voice_tones as $tone)
                <option
                    value="{{ $tone }}"
                    @selected($setting->openai_default_tone_of_voice == $tone)
                >
                    {{ __($tone) }}
                </option>
            @endforeach
        </x-forms.input>

        <x-forms.input
            id="target_audience"
            label="{{ __('Target Audience') }}"
            tooltip="{{ __('Describe the primary demographic or audience your company is targeting.') }}"
            placeholder="{{ __('Describe the primary demographic or audience your company is targeting.') }}"
            type="textarea"
            rows="3"
            name="target_audience"
        >{{ $item?->target_audience }}</x-forms.input>

        <x-forms.input
            id="c_logo"
            label="{{ __('Brand Voice') }}"
            tooltip="{{ __('Describe the primary demographic or audience your company is targeting.') }}"
            type="file"
            name="c_logo"
            value="{{ $item?->logo }}"
            size="lg"
        />

        <x-forms.input
            id="c_color"
            label="{{ __('Brand Color') }}"
            tooltip="{{ __('Pick a color for for the icon container shape. Color is in HEX format.') }}"
            type="color"
            name="c_color"
            value="{{ $item != null ? $item?->brand_color : '#8fd2d0' }}"
            size="lg"
        />

        <x-form-step
            step="2"
            label="{{ __('Products or Services') }}"
        >
            <x-button
                class="add-more ms-auto inline-flex size-8 items-center justify-center rounded-full bg-background text-foreground transition-all"
                size="none"
                type="button"
                variant="ghost-shadow"
            >
                <x-tabler-plus class="size-5" />
            </x-button>
        </x-form-step>

        @if ($item?->id && $item?->products?->count())
            @foreach ($item->products ?? [] as $product)
                <x-card
                    class="user-input-group relative"
                    class:body="flex flex-col gap-5"
                    data-input-name="{{ $product?->name }}"
                    data-inputs-id="{{ $loop->index + 1 }}"
                >
                    <x-forms.input
                        class="input_name"
                        size="lg"
                        label="{{ __('Name') }}"
                        tooltip="{{ __('The primary item or service your company provides to its customers.') }}"
                        value="{{ $product?->name }}"
                    />

                    <x-forms.input
                        class="input_type"
                        size="lg"
                        type="select"
                        label="{{ __('Type') }}"
                    >
                        <option
                            value="0"
                            @selected($product?->type == 0)
                        >
                            {{ __('Product') }}
                        </option>
                        <option
                            value="1"
                            @selected($product?->type == 1)
                        >
                            {{ __('Service') }}
                        </option>
                        <option
                            value="3"
                            @selected($product?->type == 2)
                        >
                            {{ __('Other') }}
                        </option>
                    </x-forms.input>

                    <x-forms.input
                        class="input_features"
                        type="textarea"
                        size="lg"
                        rows="3"
                        placeholder="{{ __('Explain the features of your Product/Service.') }}"
                        label="{{ __('Key Features') }}"
                        tooltip="{{ __('Describe the key services your company offers to its clients or customers.') }}"
                    >{{ $product?->key_features }}</x-forms.input>

                    <x-button
                        class="remove-inputs-group absolute -end-3 -top-3 size-6"
                        size="none"
                        variant="danger"
                        type="button"
                    >
                        <x-tabler-minus class="size-4" />
                    </x-button>
                </x-card>
            @endforeach
            <div class="add-more-placeholder"></div>
        @else
            <x-card
                class="user-input-group relative"
                class:body="flex flex-col gap-5"
                data-inputs-id="1"
            >
                <x-forms.input
                    class="input_name"
                    size="lg"
                    label="{{ __('Name') }}"
                    tooltip="{{ __('The primary item or service your company provides to its customers.') }}"
                />

                <x-forms.input
                    class="input_type"
                    size="lg"
                    type="select"
                    label="{{ __('Type') }}"
                >
                    <option value="0">
                        {{ __('Product') }}
                    </option>
                    <option value="1">
                        {{ __('Service') }}
                    </option>
                    <option value="3">
                        {{ __('Other') }}
                    </option>
                </x-forms.input>

                <x-forms.input
                    class="input_features"
                    type="textarea"
                    size="lg"
                    rows="3"
                    placeholder="{{ __('Explain the features of your Product/Service.') }}"
                    label="{{ __('Key Features') }}"
                    tooltip="{{ __('Describe the key services your company offers to its clients or customers.') }}"
                ></x-forms.input>

                <x-button
                    class="remove-inputs-group absolute -end-3 -top-3 size-6"
                    size="none"
                    variant="danger"
                    type="button"
                >
                    <x-tabler-minus class="size-4" />
                </x-button>
            </x-card>

            <div class="add-more-placeholder"></div>
        @endif

        @if ($app_is_demo)
            <x-button
                type="button"
                onclick="return toastr.info('This feature is disabled in Demo version.');"
            >
                {{ __('Save') }}
            </x-button>
        @else
            <x-button
                id="custom_company_button"
                type="button"
                form="custom_company_form"
            >
                {{ __('Save') }}
            </x-button>
        @endif

    </form>

    <template id="user-input-company">
        <x-card
            class="user-input-group relative"
            class:body="flex flex-col gap-5"
            data-inputs-id="1"
        >
            <x-forms.input
                class="input_name"
                size="lg"
                label="{{ __('Name') }}"
                tooltip="{{ __('The primary item or service your company provides to its customers.') }}"
            />

            <x-forms.input
                class="input_type"
                size="lg"
                type="select"
                label="{{ __('Type') }}"
            >
                <option value="0">
                    {{ __('Product') }}
                </option>
                <option value="1">
                    {{ __('Service') }}
                </option>
                <option value="3">
                    {{ __('Other') }}
                </option>
            </x-forms.input>

            <x-forms.input
                class="input_features"
                type="textarea"
                size="lg"
                rows="3"
                placeholder="{{ __('Explain the features of your Product/Service.') }}"
                label="{{ __('Key Features') }}"
                tooltip="{{ __('Describe the key services your company offers to its clients or customers.') }}"
            ></x-forms.input>

            <x-button
                class="remove-inputs-group absolute -end-3 -top-3 size-6"
                size="none"
                variant="danger"
                type="button"
            >
                <x-tabler-minus class="size-4" />
            </x-button>
        </x-card>
    </template>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            "use strict";

            const colorInput = document.querySelector('#c_color');
            const colorValue = document.querySelector('#c_color_value');
            const chatCompletionFillBtn = document.querySelector('.chat-completions-fill-btn');

            colorInput?.addEventListener('input', ev => {
                const input = ev.currentTarget;

                if (colorValue) {
                    colorValue.value = input.value
                };
            });

            colorValue?.addEventListener('input', ev => {
                const input = ev.currentTarget;

                if (colorInput) {
                    colorInput.value = input.value
                };
            });


            const slugify = str =>
                `**${ str.toLowerCase().trim().replace( /[^\w\s-]/g, '' ).replace( /[\s_-]+/g, '-' ).replace( /^-+|-+$/g, '' ) }** `;
            /** @type {HTMLTemplateElement} */
            const userInputTemplate = document.querySelector('#user-input-company');
            const addMorePlaceholder = document.querySelector('.add-more-placeholder');
            let currentInputGroupts = document.querySelectorAll('.user-input-group');
            let lastInputsParent = [...currentInputGroupts].at(-1);
            let lastInpusGroupId = lastInputsParent ? parseInt(lastInputsParent.getAttribute('data-inputs-id'),
                10) : 0;

            $(".add-more").click(function() {
                const button = this;
                const currentInputs = document.querySelectorAll(
                    '.input_name, .input_features, .input_type');
                let anInputIsEmpty = false;
                currentInputs.forEach(input => {
                    const {
                        value
                    } = input;
                    if (!value || value.length === 0 || value.replace(/\s/g, '') === '') {
                        return anInputIsEmpty = true;
                    }
                });
                if (anInputIsEmpty) {
                    return toastr.error('Please fill all fields in User Group Input areas.');
                }
                const newInputsMarkup = userInputTemplate.content.cloneNode(true);
                const newInputsWrapper = newInputsMarkup.firstElementChild;
                newInputsWrapper.dataset.inputsId = lastInpusGroupId + 1;
                addMorePlaceholder.before(newInputsMarkup);
                currentInputGroupts = document.querySelectorAll('.user-input-group');
                lastInputsParent = [...currentInputGroupts].at(-1);
                if (currentInputGroupts.length > 1) {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.removeAttribute(
                        'disabled'));
                }
                lastInpusGroupId++;
                const timeout = setTimeout(() => {
                    newInputsWrapper.querySelector('.input_name').focus();
                    clearTimeout(timeout);
                }, 100);
                return;
            });

            $("body").on("click", ".remove-inputs-group", function() {
                const button = $(this);
                const parent = button.closest('.user-input-group');
                const inputsId = parent.attr('data-inputs-id');

                $(`[data-inputs-id=${ inputsId }]`).remove();

                currentInputGroupts = document.querySelectorAll('.user-input-group');
                lastInputsParent = [...currentInputGroupts].at(-1);

                if (currentInputGroupts.length > 1) {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.removeAttribute(
                        'disabled'));
                } else {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.setAttribute(
                        'disabled', true));
                }
            });

            $('body').on('input', '.input_name', ev => {
                const input = ev.currentTarget;
                const parent = input.closest('.user-input-group');
                const parentId = parent.getAttribute('data-inputs-id');
                const inputName = slugify(input.value);
                let button = document.querySelector(`button[data-inputs-id="${ parentId }"]`);

                if (!button) {
                    button = document.createElement('button');
                    button.className =
                        'bg-[#EFEFEF] text-black cursor-pointer py-[0.15rem] px-[0.5rem] border-none rounded-full transition-all duration-300 hover:bg-black hover:!text-white';
                    button.dataset.inputsId = parentId;
                    button.type = 'button';
                }

                parent.dataset.inputName = inputName;
                button.dataset.inputName = inputName;
                button.innerText = inputName;
            });


        });

        $('#custom_company_button').click(function() {
            // document.getElementById("custom_company_button").disabled = true;
            document.getElementById("custom_company_button").innerHTML = magicai_localize.please_wait;

            var input_name = [];
            $(".input_name").each(function() {
                input_name.push($(this).val());
            });

            console.log(input_name);
            var input_features = [];
            $(".input_features").each(function() {
                input_features.push($(this).val());
            });

            var input_type = [];
            $(".input_type").each(function() {
                input_type.push($(this).val());
            });

            $('#c_industry option').prop('selected', true);

            var formData = new FormData();
            formData.append('c_name', $("#c_name").val());
            formData.append('c_industry', $('#c_industry').val());
            formData.append('c_description', $("#c_description").val());
            formData.append('c_website', $("#c_website").val());
            formData.append('c_tagline', $("#c_tagline").val());
            formData.append('c_logo', $('#c_logo').prop('files')[0]);
            formData.append('c_color', $("#c_color").val());
            formData.append('input_name', input_name);
            formData.append('input_features', input_features);
            formData.append('input_type', input_type);
            formData.append('tone_of_voice', $("#tone_of_voice").val());
            formData.append('target_audience', $("#target_audience").val());
            formData.append('tone_of_voice_custom', $("#tone_of_voice_custom").val());
            formData.append('_method', $('[name="_method"]').val());

            $.ajax({
                type: "post",
                url: '{{ $action }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    toastr.success('Template Saved Succesfully.');
                    document.getElementById("custom_company_button").disabled = false;
                    document.getElementById("custom_company_button").innerHTML = "Save";
                    setTimeout(function() {
                        window.location.href = "/dashboard/user/brand";
                    }, 200);
                },
                error: function(data) {
                    var err = data.responseJSON.errors;
                    $.each(err, function(index, value) {
                        toastr.error(value);
                    });
                    document.getElementById("custom_company_button").disabled = false;
                    document.getElementById("custom_company_button").innerHTML = "Save";
                }
            });
            return false;
        })
    </script>
@endpush
