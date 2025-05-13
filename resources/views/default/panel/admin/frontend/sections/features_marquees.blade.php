<x-form-step
        step="7"
        label="{{ __('Features Marquee') }}"
>
</x-form-step>

<div class="col-md-12">
    <div class="mb-3">
        @foreach (\App\Models\Section\FeaturesMarquee::all() ?? [] as $key => $item)
            <div class="mb-3 grid gap-3">
                <input
                        class="form-control"
                        id="features_marquee_{{ $item['id'] }}"
                        type="text"
                        name="features_marquee_{{ $item['id'] }}"
                        value="{{ $item['title'] }}"
                >
            </div>
            <hr>
        @endforeach
    </div>
</div>