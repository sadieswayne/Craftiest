<div
    class="fixed bottom-12 left-1/2 z-50 -translate-x-1/2 rounded-full bg-white p-2 drop-shadow-2xl max-sm:w-11/12"
    id="gdpr"
>
    <div class="flex items-center justify-between gap-6 text-[12px]">
        <div class="content-left pl-4">
            {!! __($setting->gdpr_content) !!}
        </div>
        <div class="content-right text-end">
            <button class="cursor-pointer rounded-full bg-black px-4 py-2 text-white">{!! __($setting->gdpr_button) !!}</button>
        </div>
    </div>
</div>
