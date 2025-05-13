@extends('panel.layout.app')
@section('title', __('Add or Edit Email Templates'))
@section('additional_css')
@endsection
@section('content')
    <div
            class="page-header"
            xmlns="http://www.w3.org/1999/html"
    >
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <div class="hstack gap-1">
                        <a
                                class="page-pretitle flex items-center"
                                href="{{  (route('dashboard.index')) }}"
                        >
                            <svg
                                    class="!me-2 rtl:-scale-x-100"
                                    width="8"
                                    height="10"
                                    viewBox="0 0 6 10"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                        d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"
                                />
                            </svg>
                            {{ __('Back to dashboard') }}
                        </a>
                        <span class="page-pretitle flex items-center">/</span>
                        <a
                                class="page-pretitle flex items-center"
                                href="{{ route('dashboard.chatbot.index') }}"
                        >
                            {{ __('ChatBot') }}
                        </a>
                    </div>
                    <h2 class="page-title mb-2">
                        {{ __('Add or Edit Template') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <form
                            id="email_templates_form"
                            onsubmit="return chatbotSave({{ $chatbotData != null ? $chatbotData->id : null }});"
                            action=""
                            enctype="multipart/form-data"
                    >

                        <div class="mb-[20px]">
                            <label
                                    class="form-label"
                                    for="title"
                            >
                                {{ __('Name') }}
                            </label>
                            <input
                                    class="form-control"
                                    id="title"
                                    type="text"
                                    name="title"
                                    value="{{ $chatbotData != null ? $chatbotData->title : 'MagicAI Bot' }}"
                                    placeholder="{{ __('MagicAI Bot') }}"
                            >
                        </div>

                        <div class="mb-[20px]">
                            <label
                                    class="form-label"
                                    for="role"
                            >
                                {{ __('Role') }}
                            </label>
                            <input
                                    class="form-control"
                                    id="role"
                                    type="text"
                                    name="role"
                                    value="{{ $chatbotData != null ? $chatbotData->role : 'Support' }}"
                                    placeholder="{{ __('Support') }}"
                            >
                        </div>

                        <div class="mb-[20px]">
                            <label
                                    class="form-label"
                                    for="model"
                            >
                                {{ __('Model') }}
                            </label>
                            <select
                                    class="form-select"
                                    id="model"
                                    name="model"
                            >
                                <option
                                        value="{{\App\Domains\Entity\Enums\EntityEnum::GPT_3_5_TURBO_16K->value}}"
                                        {{ $chatbotData != null && $chatbotData->model == $chatbotData->model ? 'selected' : null }}
                                >{{\App\Domains\Entity\Enums\EntityEnum::GPT_3_5_TURBO_16K->label()}}</option>
                                @php
                                    if ($chatbotData != null) {
                                        App\Http\Controllers\AIFineTuneController::getFineModelOption($chatbotData->model);
                                    }
                                @endphp
                            </select>
                        </div>

                        <div class="mb-[20px]">
                            <label
                                    class="form-label"
                                    for="first_message"
                            >
                                {{ __('First Message') }}
                            </label>
                            <input
                                    class="form-control"
                                    id="first_message"
                                    type="text"
                                    name="first_message"
                                    value="{{ $chatbotData != null ? $chatbotData->first_message : 'I am AI Assistant. How can I help you?' }}"
                                    placeholder="{{ __('I am AI Assistant. How can I help you?') }}"
                            >
                        </div>

                        <div class="mb-[20px]">
                            <label
                                    class="form-label"
                                    for="instructions"
                            >
                                {{ __('Instructions') }}
                            </label>
                            <textarea
                                    class="form-control"
                                    id="instructions"
                                    type="text"
                                    rows="5"
                                    name="instructions"
                                    placeholder="{{ __('You are a assistant and your name is MagicAI.') }}"
                            >{{ $chatbotData != null ? $chatbotData->instructions : 'Your name is John Doe. Remember that you are an assistant who only gives information about wordpress and don\'t give any other information.' }}</textarea>
                        </div>

                        <div class="mb-[20px]">
                            <label
                                    class="form-label"
                                    for="image"
                            >
                                {{ __('Image') }}
                            </label>
                            <input
                                    class="form-control"
                                    id="image"
                                    type="file"
                                    name="image"
                            >
                        </div>

                        <div class="row mb-[20px]">
                            <div class="col-md-6">
                                <label
                                        class="form-label"
                                        for="width"
                                >
                                    {{ __('Custom Width') }}
                                </label>
                                <input
                                        class="form-control"
                                        id="width"
                                        type="text"
                                        name="width"
                                        value="{{ $chatbotData != null ? $chatbotData->width : null }}"
                                        placeholder="300px"
                                >
                            </div>
                            <div class="col-md-6">
                                <label
                                        class="form-label"
                                        for="height"
                                >
                                    {{ __('Custom Height') }}
                                </label>
                                <input
                                        class="form-control"
                                        id="height"
                                        type="text"
                                        name="height"
                                        value="{{ $chatbotData != null ? $chatbotData->height : null }}"
                                        placeholder="340px"
                                >
                            </div>
                        </div>

                        <button
                                class="btn btn-primary w-full !py-3"
                                id="chatbot_button"
                                form="email_templates_form"
                        >
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/chatbot.js') }}"></script>
@endpush
