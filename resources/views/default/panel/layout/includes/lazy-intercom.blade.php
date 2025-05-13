@if ($app_is_not_demo)
<script>
    document.addEventListener('DOMContentLoaded', function() {
		fetch('/vip-intercom-partial')
			.then(response => response.text())
			.then(html => {
				// Create a temporary container
				const temp = document.createElement('div');
				temp.innerHTML = html;

				// Extract and execute scripts
				const scripts = temp.getElementsByTagName('script');
				for (let script of scripts) {
					const newScript = document.createElement('script');
					if (script.src) {
						newScript.src = script.src;
					} else {
						newScript.textContent = script.textContent;
					}
					document.body.appendChild(newScript);
				}
			});
    });
</script>

<div id="vip-intercom-container"></div>
@endif
