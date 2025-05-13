<x-form-step
        step="8"
        label="{{ __('Plan footer text') }}"
>
</x-form-step>

<div class="col-md-12">
    <div class="mb-3">
        <div class="mb-3 grid gap-3">
            <input
                    class="form-control"
                    id="plan_footer_text"
                    type="text"
                    name="plan_footer_text"
                    value="{{ $fSectSettings->plan_footer_text }}"
            >
        </div>
    </div>
</div>