@includeWhen(setting('single_page_checkout', 0), 'panel.user.finance.partials.singlePageCheckout')
<div
    class="mb-5 rounded-lg border px-4"
    x-data="{ inputShow: false }"
>
    <x-button
        class="w-full justify-between rounded-lg py-4"
        variant="link"
        size="lg"
        @click.prevent="inputShow = !inputShow"
    >
        {{ __('Have a coupon?') }}
        <x-tabler-chevron-down
            class="size-5"
            ::class="{ 'rotate-180': inputShow }"
        />
    </x-button>

    <div
        class="mb-4 mt-3 hidden gap-3 rounded-lg border pe-3"
        x-init
        x-trap="inputShow"
        :class="{ 'hidden': !inputShow, 'flex': inputShow }"
    >
        <x-forms.input
            class:container="grow"
            class="border-none bg-transparent"
            size="lg"
            name="code"
            placeholder="{{ __('Coupon Code') }}"
        />
        <x-button
            onclick="applyCoupon();"
            variant="link"
            href="javascript:void(0);"
        >
            {{ __('Apply') }}
            <x-tabler-circle-arrow-right-filled class="size-5" />
        </x-button>
    </div>
</div>

<script>
    function applyCoupon() {
        var couponCode = document.querySelector('input[name="code"]').value;
        // Check if the coupon code is not empty
        if (couponCode.trim() !== '') {
            // Make an AJAX request to the server-side endpoint
            fetch('/dashboard/coupons/validate-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        code: couponCode
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    // Process the response data
                    if (data.valid) {

                        var currentURL = window.location.href;
                        var couponCodeParam = encodeURIComponent(couponCode);

                        // Check if the coupon code parameter already exists in the URL
                        if (currentURL.includes('coupon=')) {
                            // If it exists, replace the existing value
                            currentURL = currentURL.replace(/coupon=([^&]+)/, 'coupon=' + couponCodeParam);
                        } else {
                            // If it doesn't exist, add it as a new parameter
                            currentURL += (currentURL.includes('?') ? '&' : '?') + 'coupon=' + couponCodeParam;
                        }

                        // Redirect to the updated URL
                        window.location.href = currentURL;

                    } else {
                        // Coupon is invalid, show an error message
                        toastr.error("{{ __('Invalid coupon code. Please try again') }}");
                    }
                })
                .catch(error => {
                    // Handle any errors that occurred during the AJAX request
                    console.error('Error:', error);
                });

            // Clear the input field after applying the coupon
            // document.querySelector('input[name="code"]').value = '';
        } else {
            // Display an error message if the coupon code is empty
            toastr.error("{{ __('Please enter a coupon code') }}");
        }
    }
</script>
