/**
*
* @param {string} string
*/
function lqdFormatString( string ) {
	if ( !('markdownit' in window) ) {
		return string;
	}

	string = string
		.replace(
			/(?<=\[START_REASONING\])(?:.*?\n\n.*?)(?=\[END_REASONING\]|$)/gs,
			match => match.replace(/\n\n/g, '\n'),
		)
		.replace('[START_REASONING]', '>')
		.replace('[END_REASONING]', '\n');

	const renderer = window.markdownit({
		breaks: true,
		highlight: (str, lang) => {
			const language = lang && lang !== '' ? lang : 'md';
			const codeString = str;

			if (Prism.languages[language]) {
				const highlighted = Prism.highlight(codeString, Prism.languages[language], language);
				return `<pre class="animated-word !whitespace-pre-wrap rounded [direction:ltr] max-w-full !w-full language-${language}"><code data-lang="${language}" class="language-${language}">${highlighted}</code></pre>`;
			}

			return codeString;
		}
	});

	renderer.use(function (md) {
		md.core.ruler.after('inline', 'convert_links', function (state) {
			state.tokens.forEach(function (blockToken) {
				if (blockToken.type !== 'inline') return;
				blockToken.children.forEach(function (token, idx) {
					if (token.content.includes('<a ')) {
						const linkRegex = /(.*)(<a\s+href="([^"]+)"[^>]*>([^<]+)<\/a>)(.*)/;
						const linkMatch = token.content.match(linkRegex);

						if (linkMatch) {
							const [ , before, , href, text, after ] = linkMatch;

							const beforeToken = new state.Token('text', '', 0);
							beforeToken.content = before;

							const newToken = new state.Token('link_open', 'a', 1);
							newToken.attrs = [ [ 'href', href ] ];
							const textToken = new state.Token('text', '', 0);
							textToken.content = text;
							const closingToken = new state.Token('link_close', 'a', -1);

							const afterToken = new state.Token('text', '', 0);
							afterToken.content = after;

							blockToken.children.splice(idx, 1, beforeToken, newToken, textToken, closingToken, afterToken);
						}
					}
				});
			});
		});
	});

	let renderedString = renderer.render(renderer.utils.unescapeAll(string));

	return renderedString;
}
