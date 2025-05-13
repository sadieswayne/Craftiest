<link
	rel="stylesheet"
	href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
>
<script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>
<script src="{{ custom_theme_url('/assets/js/format-string.js') }}"></script>

<script>
	document.addEventListener('DOMContentLoaded', (event) => {
		"use strict";

		const codeLang = document.querySelector('#code_lang');
		const codePre = document.querySelector('#code-pre');
		const codeOutput = codePre?.querySelector('#code-output');

		if (!codeOutput) return;

		// saving for copy
		window.codeRaw = codeOutput.innerText;

		codeOutput.innerHTML = lqdFormatString(codeOutput.textContent);
	});
</script>
