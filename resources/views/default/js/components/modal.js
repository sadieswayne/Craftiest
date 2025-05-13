export default function(e = {show: false, focusable: false}) {
    return {
        show: e.show,
        focusable: e.focusable,
        closeModal() {
            this.show = false
        },
        openModal() {
            this.show = true
        },
        focusables() {
            // All focusable element types...
            let selector =
                'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'

            return (
                [...this.$el.querySelectorAll(selector)]
                    // All non-disabled elements...
                    .filter((el) => !el.hasAttribute('disabled'))
            )
        },
        firstFocusable() {
            return this.focusables()[0]
        },
        lastFocusable() {
            return this.focusables().slice(-1)[0]
        },
        nextFocusable() {
            return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable()
        },
        prevFocusable() {
            return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable()
        },
        nextFocusableIndex() {
            return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1)
        },
        prevFocusableIndex() {
            return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1
        },
        init() {
            this.$watch('show', value => {
                if (value) {
                    document.body.classList.add('overflow-y-hidden')
                    if (this.focusable !== false) {
                        setTimeout(() => this.firstFocusable().focus(), 100)
                    }
                } else {
                    document.body.classList.remove('overflow-y-hidden')
                }
            });
        },
    }
}
