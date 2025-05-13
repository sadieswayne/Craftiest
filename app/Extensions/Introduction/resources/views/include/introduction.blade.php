<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.innerWidth >= 768) {
            const steps = @json(\App\Models\Extensions\Introduction::getFormattedSteps());
            @if (auth()->user()->tour_seen == 0 && \App\Helpers\Classes\Helper::setting('tour_seen') == 1)
            introJs().setOptions({
                showBullets: false,
                steps: steps.map(step => {
                    step.element = document.querySelector(step.element);
                    return step;
                })
            })
                .oncomplete(function () {
                    markTourSeen();
                })
                .onexit(function () {
                    markTourSeen();
                })
                .start();

            function markTourSeen() {
                fetch('/dashboard/user/mark-tour-seen', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                });
            }
            @endif
        }
    });
</script>
