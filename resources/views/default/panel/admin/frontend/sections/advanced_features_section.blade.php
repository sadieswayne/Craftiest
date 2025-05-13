<x-form-step
        step="5"
        label="{{ __('Advanced Features Section') }}"
>
</x-form-step>

<div class="col-md-12">
    <div class="mb-3">
        <div class="mt-3">
            <label class="mb-2">Advanced features section title</label>
            <input
                    class="form-control mt-2"
                    id="advanced_features_section_title"
                    type="text"
                    name="advanced_features_section_title"
                    value="{{ $fSectSettings->advanced_features_section_title ?: 'Driving Innovation.' }}"
            >
        </div>
        <div class=" mt-3">
            <label class="mb-2">Advanced features section description</label>
            <input
                    class="form-control mt-2"
                    id="advanced_features_section_description"
                    type="text"
                    name="advanced_features_section_description"
                    value="{{ $fSectSettings->advanced_features_section_description ?: ' Optimize your content for search engines and reach more customers and increase your online visibility. ' }}"
            >
        </div>


        <hr>

        @foreach ($advanced_features_section ?? [] as $key => $item)
            <div class="mb-3 grid gap-3">




                <input
                        class="form-control"
                        id="advanced_features_title_{{ $item['id'] }}"
                        type="text"
                        name="advanced_features_title_{{ $item['id'] }}"
                        value="{{ $item['title'] }}"
                >
                <input
                        class="form-control"
                        id="advanced_features_description_{{ $item['id'] }}"
                        type="text"
                        name="advanced_features_description_{{ $item['id'] }}"
                        value="{{ $item['description'] }}"
                >
                <input
                        class="form-control"
                        id="advanced_features_image_{{ $item['id'] }}"
                        type="file"
                        name="advanced_features_image_{{ $item['id'] }}"
                        value="{{ $item['image'] }}"
                >
                <img
                        class="w-1/4"
                        src="{{ $item['image'] }}"
                        alt="{{ $item['title'] }}"
                >
            </div>
            <hr>
        @endforeach
    </div>
</div>