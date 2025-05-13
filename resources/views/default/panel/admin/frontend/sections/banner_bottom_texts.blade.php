<x-form-step
        step="4"
        label="{{ __('Banner bottom texts') }}"
>
</x-form-step>

<div class="col-md-12">
    <div class="mb-3">
        @foreach (\App\Models\Section\BannerBottomText::all() ?? [] as $key => $item)
            <div class="mb-3 grid gap-3">
                <input
                        class="form-control"
                        id="banner_bottom_text_{{ $item['id'] }}"
                        type="text"
                        name="banner_bottom_text_{{ $item['id'] }}"
                        value="{{ $item['text'] }}"
                >
            </div>
            <hr>
        @endforeach
    </div>
</div>