export default function (e = { text: null, message: 'Copied!' }) {
    return {
        onClipboard: false,
        text: e.text,
        message: e.message || 'Copied!',
        init() {
            /**
             * x-data="{ text: 'Hello, World!' }" data-message="Copied!"
             * x-data="{ text: 'Hello, World!', message: 'Copied!' }"
             */
            this.message = this.$el.dataset.message || this.message;
        },
        clipboard: {
            ['@click']() {
                this.onClipboardClick();
            },
        },
        copy() {
            let text = this.text;
            if (typeof text === 'function') {
                text = text()
            }

            if (typeof text === 'object') {
                text = JSON.stringify(text)
            }
            let then = () => {
                setTimeout(() => {
                    this.onClipboard = false
                }, 500)
            }

            window.navigator.clipboard.writeText(text).then(then)

            window.toastr.info(this.message)
        },
        onClipboardClick() {
            this.onClipboard = true
            this.copy();
        },
    }
}
