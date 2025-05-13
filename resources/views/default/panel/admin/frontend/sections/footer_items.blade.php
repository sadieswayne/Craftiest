<x-form-step
        step="9"
        label="{{ __('Footer Items') }}"
>
</x-form-step>

<div class="col-md-12">
    <div class="mb-3">
        @foreach (\App\Models\Section\FooterItem::all() ?? [] as $key => $item)
            <div class="mb-3 grid gap-3">
                <input
                        class="form-control"
                        id="footer_item_{{ $item['id'] }}"
                        type="text"
                        name="footer_item_{{ $item['id'] }}"
                        value="{{ $item['item'] }}"
                >
            </div>
            <hr>
        @endforeach
    </div>
</div>