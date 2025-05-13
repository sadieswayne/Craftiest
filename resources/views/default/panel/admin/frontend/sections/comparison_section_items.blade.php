<x-form-step
        step="6"
        label="{{ __('Comparison section items') }}"
>
</x-form-step>

<div class="col-md-12">
    <div class="mb-3">
        @foreach ($comparison_section_items ?? [] as $key => $item)
            <div class="mb-3 grid gap-3">
                <input
                        class="form-control"
                        id="comparison_section_item_label_{{ $item['id'] }}"
                        type="text"
                        name="comparison_section_item_label_{{ $item['id'] }}"
                        value="{{ $item['label'] }}"
                >
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" {{ $item['others'] ? 'checked': ''  }} id="comparison_section_item_others_{{ $item['id'] }}">
                            <label class="form-check-label" for="comparison_section_item_others_{{ $item['id'] }}">
                                Others
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" {{ $item['ours'] ? 'checked': ''  }} value="" id="comparison_section_item_ours_{{ $item['id'] }}">
                            <label class="form-check-label" for="comparison_section_item_ours_{{ $item['id'] }}">
                                Ours
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        @endforeach
    </div>
</div>