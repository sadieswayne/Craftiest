@php
    $dashboard_scss_path = 'resources/views/' . get_theme() . '/scss/dashboard.scss';
@endphp

<html>

<head>
    <title></title>

    @vite($dashboard_scss_path)
</head>

<body>
    <div class="prose max-w-full">
        @foreach ($messages as $message)
            @if ($message->input != null)
                <div
                    class="chat-bubble"
                    style='font-family: sans-serif; display: flex;padding: 10px 14px;justify-content: center;align-items: center;gap: 10px;flex-shrink: 0;border-radius: 12px;background: #F3E2FD; font-size: 15px;font-style: normal;line-height: 23px;'
                >
                    You: {{ $message->input }}</div>
            @endif
            @if ($message->output != null)
                @php
                    $output = str_replace(['<br>', '<br/>', '<br >', '<br />'], "\n", $message->output);
                @endphp
                <div
                    class="chat-bubble"
                    style='font-family: sans-serif; display: flex;padding: 10px 14px;justify-content: center;align-items: center;gap: 10px;flex-shrink: 0;border-radius: 12px;background: #F4F4F4;color: #474D59;font-size: 15px;font-style: normal;line-height: 23px;'
                >
                    Chatbot: {{ $output }}</div>
            @endif
        @endforeach
    </div>

    <script src="{{ custom_theme_url('/assets/libs/markdown-it.min.js') }}"></script>
    <script>
        const chatsMD = window.markdownit({
            highlight: function(str, lang) {
                const language = lang && lang !== '' ? lang : 'md';
                try {
                    return `<pre class="line-numbers max-w-full rounded [direction:ltr]"><code class="language-${language}" data-lang="${language}">${str.replace(/&/g, '&amp;').replace(/</g, '&lt;')}</code></pre>`;
                } catch (__) {}
                return '';
            }
        });

        function highlightCode(wrapperEl) {
            if (!wrapperEl) return;

            wrapperEl.innerHTML = chatsMD.render(
                chatsMD.utils.unescapeAll(
                    wrapperEl.innerHTML
                )
            );

            wrapperEl.innerHTML = wrapperEl.innerHTML
                .replace(/>(\s*\r?\n\s*)</g, '><')
                .replace(/\n(?!.*\n)/, '');

            wrapperEl.querySelectorAll('code[class*=language-]').forEach(el => {
                Prism.highlightElement(el);
            });
        }

        (() => {
            document.querySelectorAll('.chat-bubble').forEach(highlightCode);
        })();
    </script>
</body>

</html>
