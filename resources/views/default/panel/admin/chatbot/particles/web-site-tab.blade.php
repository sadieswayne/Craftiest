<div :class="{ 'hidden': activeTab !== 'website' }">
    <div
        x-data="{ url: null }"
        class="flex flex-col gap-5"
        id="web-site-form"
    >
        @csrf
        <x-form-step
            step="1"
            label="{{ __('Add URL') }}"
        />

        <div class="flex flex-wrap gap-x-6 gap-y-2 text-2xs">
            <label
                class="cursor-pointer"
                for="web_site"
            >
                <input
                    class="peer invisible h-0 w-0"
                    id="web_site"
                    value="web_site"
                    type="radio"
                    name="type"
                    checked
                >
                <span class="inline-block opacity-25 peer-checked:underline peer-checked:underline-offset-2 peer-checked:opacity-100">
                    @lang('Website')
                </span>
            </label>
            <label
                class="cursor-pointer"
                for="single"
            >
                <input
                    class="peer invisible h-0 w-0"
                    id="single"
                    value="single"
                    type="radio"
                    name="type"
                >
                <span class="inline-block opacity-25 peer-checked:underline peer-checked:underline-offset-2 peer-checked:opacity-100">
                    @lang('Single URL')
                </span>
            </label>

        </div>

        <div class="relative">
            <x-forms.input
                id="url"
                size="lg"
                name="url"
                placeholder="https://example.com"
                @keydown.enter.prevent="document.getElementById('web-site-form-submit').click();"
            />
            <x-button
                class="size-11 group absolute end-2 top-1/2 -translate-y-1/2 text-primary hover:-translate-y-1/2 hover:scale-110"
                id="web-site-form-submit"
                data-action="{{ route('dashboard.admin.chatbot.web-sites', $item) }}"
                variant="link"
                size="none"
                type="button"
            >
                <x-tabler-refresh
                    class="size-5 group-[&.lqd-is-busy]:animate-spin"
                    stroke-width="2.2"
                />
                <span class="sr-only">
                    @lang('Fetch')
                </span>
            </x-button>
        </div>
    </div>

    <form
        class="training-form mt-14"
        id="form-train-web-site"
        action="{{ route('dashboard.admin.chatbot.training', $item->id) }}"
    >
        @csrf
        <input
            id="type"
            type="hidden"
            name="type"
            value="url"
        >
        @php
            $websites = $data->where('type', 'url');
        @endphp

        <x-form-step
            class="mb-6"
            step="2"
            label="{{ __('Select Pages') }}"
        >
            <small class="ms-auto text-2xs">
                <button
                    class="group font-semibold"
                    data-select="all"
                    type="button"
                >
                    <span class="group-[&.has-selected]:hidden">
                        @lang('Select All')
                    </span>
                    <span class="hidden group-[&.has-selected]:block">
                        @lang('Deselect All')
                    </span>
                </button>
            </small>
        </x-form-step>

        <input
            id="pages_total_count"
            type="hidden"
            value="{{ $websites?->count() }}"
        >
        <div
            class="pages space-y-4"
            id="chatbotPages"
        >
            @include('panel.admin.chatbot.particles.web-site.crawler', ['items' => $websites])
        </div>
    </form>
</div>
