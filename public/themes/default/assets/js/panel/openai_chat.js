const aiResponseTextArray = [];
let conversationAreaLastScrollTop = 0;
let conversationAreaScrollLocked = true;
let conversationAreaScrollDir = 'down';
let conversationAreaScrollDelta = 0;
let lastAiChatBubble = null;
let selectedPrompt = -1;
let promptsData = [];
let favData = [];
let searchString = '';
let pdf = undefined;
let pdfName = '';
let pdfPath = '';
let filterType = 'all';
let imagePath = [];
let prompt_images = [];
let streamed_text = '';
let streamed_message_id = 0;
let navigatingInChatsHistory = false;
let selectedHistoryPrompt = -1;
let animatedWordIndex = 0;
let lastFinishedAnimatedWordIndex = -1;
let aiResponseStreaming = false; // indicator of server streaming
let aiResponseAnimating = false; // indicator of client animation

/**
 * Credits: Joydeep Bhowmik https://dev.to/joydeep23/adding-keys-our-dom-diffing-algorithm-4d7g
 */
class LiquidVDOM {
	getnodeType(node) {
		if (node.nodeType == 1) return node.tagName.toLowerCase();
		else return node.nodeType;
	}

	clean(node) {
		for (let n = 0; n < node.childNodes.length; n++) {
			let child = node.childNodes[n];
			if (child.nodeType === 8) {
				// Only remove comment nodes
				node.removeChild(child);
				n--;
			} else if (child.nodeType === 1) {
				// Element node
				if (child.hasAttribute('key')) {
					let key = child.getAttribute('key');
					child.key = key;
					child.removeAttribute('key');
				}
				this.clean(child);
			}
		}
	}

	parseHTML(str) {
		let parser = new DOMParser();
		let doc = parser.parseFromString(str, 'text/html');
		this.clean(doc.body);
		return doc.body;
	}

	attrbutesIndex(el) {
		var attributes = {};
		if (el.attributes == undefined) return attributes;
		for (var i = 0, atts = el.attributes, n = atts.length; i < n; i++) {
			attributes[atts[i].name] = atts[i].value;
		}
		return attributes;
	}

	patchAttributes(vdom, dom) {
		let vdomAttributes = this.attrbutesIndex(vdom);
		let domAttributes = this.attrbutesIndex(dom);
		if (vdomAttributes == domAttributes) return;
		Object.keys(vdomAttributes).forEach((key, i) => {
			//if the attribute is not present in dom then add it
			if (!dom.getAttribute(key)) {
				dom.setAttribute(key, vdomAttributes[key]);
			} //if the atrtribute is present than compare it
			else if (dom.getAttribute(key)) {
				if (vdomAttributes[key] != domAttributes[key]) {
					dom.setAttribute(key, vdomAttributes[key]);
				}
			}
		});
		Object.keys(domAttributes).forEach((key, i) => {
			//if the attribute is not present in vdom than remove it
			if (!vdom.getAttribute(key)) {
				dom.removeAttribute(key);
			}
		});
	}

	hasTheKey(dom, key) {
		let keymatched = false;
		for (let i = 0; i < dom.children.length; i++) {
			if (key == dom.children[i].key) {
				keymatched = true;
				break;
			}
		}
		return keymatched;
	}

	patchKeys(vdom, dom) {
		//remove unmatched keys from dom
		for (let i = 0; i < dom.children.length; i++) {
			let dnode = dom.children[i];
			let key = dnode.key;
			if (key) {
				if (!this.hasTheKey(vdom, key)) {
					dnode.remove();
				}
			}
		}
		//adding keys to dom
		for (let i = 0; i < vdom.children.length; i++) {
			let vnode = vdom.children[i];
			let key = vnode.key;
			if (key) {
				if (!this.hasTheKey(dom, key)) {
					//if key is not present in dom then add it
					let nthIndex = [].indexOf.call(
						vnode.parentNode.children,
						vnode,
					);
					if (dom.children[nthIndex]) {
						dom.children[nthIndex].before(vnode.cloneNode(true));
					} else {
						dom.append(vnode.cloneNode(true));
					}
				}
			}
		}
	}

	diff(vdom, dom) {
		//if dom has no childs then append the childs from vdom
		if (dom.hasChildNodes() == false && vdom.hasChildNodes() == true) {
			for (let i = 0; i < vdom.childNodes.length; i++) {
				//appending
				dom.append(vdom.childNodes[i].cloneNode(true));
			}
		} else {
			this.patchKeys(vdom, dom);
			//if dom has extra child
			if (dom.childNodes.length > vdom.childNodes.length) {
				let count = dom.childNodes.length - vdom.childNodes.length;
				if (count > 0) {
					for (; count > 0; count--) {
						dom.childNodes[dom.childNodes.length - count].remove();
					}
				}
			}
			//now comparing all childs
			for (let i = 0; i < vdom.childNodes.length; i++) {
				//if the node is not present in dom append it
				if (dom.childNodes[i] == undefined) {
					dom.append(vdom.childNodes[i].cloneNode(true));
					// console.log("appenidng",vdom.childNodes[i])
				} else if (
					this.getnodeType(vdom.childNodes[i]) ==
					this.getnodeType(dom.childNodes[i])
				) {
					//if same node type
					//if the nodeType is text
					if (vdom.childNodes[i].nodeType == 3) {
						//we check if the text content is not same
						if (
							vdom.childNodes[i].textContent !=
							dom.childNodes[i].textContent
						) {
							//replace the text content
							dom.childNodes[i].textContent =
								vdom.childNodes[i].textContent;
						}
					} else {
						this.patchAttributes(
							vdom.childNodes[i],
							dom.childNodes[i],
						);
					}
				} else {
					//replace
					dom.childNodes[i].replaceWith(
						vdom.childNodes[i].cloneNode(true),
					);
				}
				if (vdom.childNodes[i].nodeType != 3) {
					this.diff(vdom.childNodes[i], dom.childNodes[i]);
				}
			}
		}
	}
}

const liquidVDOM = new LiquidVDOM();

function conversationAreaScrollHandler() {
	const conversationArea = document.querySelector('.conversation-area');

	if (!conversationArea) return;

	conversationArea.removeEventListener('wheel', onConversationAreaScroll);
	conversationArea.addEventListener('wheel', onConversationAreaScroll);
}

function onConversationAreaScroll() {
	const isTouchScreen =
		'ontouchstart' in window || navigator.maxTouchPoints > 0;

	conversationAreaScrollLocked =
		!isTouchScreen && // Disable scroll lock on touch devices
		this.scrollTop + this.offsetHeight + 10 >= this.scrollHeight;

	conversationAreaScrollDir =
		this.scrollTop > conversationAreaLastScrollTop ? 'down' : 'up';
	conversationAreaScrollDelta = Math.abs(
		this.scrollTop - conversationAreaLastScrollTop,
	);
	conversationAreaLastScrollTop = this.scrollTop <= 0 ? 0 : this.scrollTop;
}

conversationAreaScrollHandler();

/**
 *
 * @param {Node} node
 * @param {object} options
 * @param {string} options.className
 */
function wrapWords(node, options = {}) {
	const wrapper = (function () {
		let wordIndex = 0;

		function wrapWordsInner(node, options = {}) {
			if (
				node.nodeName === 'PRE' ||
				node.nodeName === 'CODE' ||
				node.nodeName === 'A'
			) {
				if (options.className) {
					node.classList.add(...options.className.split(' '));
				}
				node.setAttribute('data-index', wordIndex++);
				if (wordIndex <= lastFinishedAnimatedWordIndex) {
					node.classList.add('animated');
				}
				return;
			}

			if (node.nodeType === 3) {
				const words = node.textContent.trim().split(/[\s\n]+/);

				if (words.length <= 1 && words[0].length > 0) {
					const span = document.createElement('span');
					span.textContent = words[0];
					if (options.className) {
						span.classList.add(...options.className.split(' '));
					}
					span.setAttribute('data-index', wordIndex++);
					if (wordIndex <= lastFinishedAnimatedWordIndex) {
						span.classList.add('animated');
					}
					if (words[0] === '[DONE]') {
						span.classList.add('done-signal');
					}
					node.parentNode.replaceChild(span, node);

					return;
				}

				const fragment = document.createDocumentFragment();

				words.forEach((word, i) => {
					if (word.length > 0) {
						const span = document.createElement('span');
						span.textContent = word;
						if (options.className) {
							span.classList.add(...options.className.split(' '));
						}
						span.setAttribute('data-index', wordIndex++);
						if (wordIndex <= lastFinishedAnimatedWordIndex) {
							span.classList.add('animated');
						}
						if (word === '[DONE]') {
							span.classList.add('done-signal');
						}
						fragment.appendChild(span);
					}
					if (i < words.length - 1) {
						fragment.appendChild(document.createTextNode(' '));
					}
				});

				node.parentNode.replaceChild(fragment, node);

				return;
			}

			if (node.classList?.contains(options.className?.split(' ')[0])) return;

			const childNodes = [ ...node.childNodes ];
			childNodes.forEach(child => wrapWordsInner(child, options));
		}

		return wrapWordsInner;
	})();

	wrapper(node, options);
}

function getAiResponseString(withoutDone = true) {
	const string = aiResponseTextArray
		.join('')
		.trim()
		.replace(/<br\s*\/?>/g, '\n');

	if (withoutDone) {
		return string.replace('[DONE]', '');
	}

	return string;
}

function fixUnclosedMarkdownSyntax(string) {
	let text = string;

	let unclosedLinkTextMatch = text.match(/\[([^\]]*$)/);
	if (unclosedLinkTextMatch) {
		text = text + '](#)';
	}

	let unclosedLinkUrlMatch = text.match(/\[([^\]]+)\]\(([^)]*$)/);
	if (unclosedLinkUrlMatch) {
		text = text + ')';
	}

	let boldMatch = text.match(/\*\*(?:(?!\*\*).)*$/);
	if (boldMatch) {
		text = text + '**';
	}

	let italicMatch = text.match(/\*(?:(?!\*).)*$/);
	if (italicMatch) {
		text = text + '*';
	}

	let codeBlockMatch = text.match(/```(?:(?!```).)*$/);
	if (codeBlockMatch) {
		text = text + '```';
	}

	let inlineCodeMatch = text.match(/`(?:(?!`).)*$/);
	if (inlineCodeMatch) {
		text = text + '`';
	}

	let strikeMatch = text.match(/~~(?:(?!~~).)*$/);
	if (strikeMatch) {
		text = text + '~~';
	}

	return text;
}

/**
 *
 * @param {string} string
 * @param {object} options
 * @param {boolean} options.readyForAnimation
 */
function formatString(string, options = {}) {
	if (!('markdownit' in window)) return;

	string = fixUnclosedMarkdownSyntax(string);

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
				const highlighted = Prism.highlight(
					codeString,
					Prism.languages[language],
					language,
				);
				return `<pre class="${options.readyForAnimation ? 'animated-word' : ''} !whitespace-pre-wrap rounded [direction:ltr] max-w-full !w-full language-${language}"><code data-lang="${language}" class="language-${language}">${highlighted}</code></pre>`;
			}

			return codeString;
		},
	});

	renderer.use(function (md) {
		md.core.ruler.after('inline', 'convert_elements', function (state) {
			state.tokens.forEach(function (blockToken) {
				if (blockToken.type !== 'inline') return;

				let fullContent = '';

				blockToken.children.forEach(token => {
					let { content, type } = token;

					switch (type) {
						case 'link_open':
							content = `<a ${token.attrs.map(([ key, value ]) => `${key}="${value}"`).join(' ')}>`;
							break;
						case 'link_close':
							content = '</a>';
							break;
					}

					fullContent += content;
				});

				if (
					fullContent.includes('<ol>') ||
					fullContent.includes('<ul>')
				) {
					const listToken = new state.Token('html_inline', '', 0);
					listToken.content = fullContent.trim();
					listToken.markup = 'html';
					listToken.type = 'html_inline';

					blockToken.children = [ listToken ];
				}
			});
		});

		md.core.ruler.after('inline', 'convert_links', function (state) {
			state.tokens.forEach(function (blockToken) {
				if (blockToken.type !== 'inline') return;
				blockToken.children.forEach(function (token, idx) {
					const { content } = token;
					if (content.includes('<a ')) {
						const linkRegex = /(.*)(<a\s+[^>]*\s+href="([^"]+)"[^>]*>([^<]*)<\/a>?)(.*)/;
						const linkMatch = content.match(linkRegex);

						if (linkMatch) {
							const [ , before, , href, text, after ] = linkMatch;

							const beforeToken = new state.Token('text', '', 0);
							beforeToken.content = before;

							const newToken = new state.Token( 'link_open', 'a', 1, );
							newToken.attrs = [
								[ 'href', href ],
								[ 'target', '_blank' ],
							];
							const textToken = new state.Token('text', '', 0);
							textToken.content = text;
							const closingToken = new state.Token( 'link_close', 'a', -1, );

							const afterToken = new state.Token('text', '', 0);
							afterToken.content = after;

							blockToken.children.splice( idx, 1, beforeToken, newToken, textToken, closingToken, afterToken, );
						}
					}
				});
			});
		});
	});

	let renderedString = renderer.render(renderer.utils.unescapeAll(string));

	if (options.readyForAnimation) {
		const html = liquidVDOM.parseHTML(renderedString);

		wrapWords(html, { className: 'animated-word inline-block' });

		renderedString = html.innerHTML;
	}

	return renderedString;
}

function switchGenerateButtonsStatus(generating) {
	const generateBtn = document.querySelector('#send_message_button');
	const stopBtn = document.querySelector('#stop_button');

	generateBtn.disabled = generating;
	generateBtn.classList.toggle('hidden', generating);
	generateBtn.classList.toggle('submitting', generating);

	stopBtn.classList.toggle('active', generating);
	stopBtn.disabled = !generating;
}

const handleConversationsAreaScroll = _.throttle(
	word => {
		if (!word) return;

		const conversationArea = document.querySelector('.conversation-area');
		const wordOffsetTop = word.offsetTop;
		const aiBubbleHeight = lastAiChatBubble.offsetHeight;
		const aiBubbleOffsetTop = lastAiChatBubble.offsetTop;
		const wordRect = word.getBoundingClientRect();
		const conversationRect = conversationArea.getBoundingClientRect();
		const wordTop = wordRect.top - conversationRect.top;
		const wordHeight = wordRect.height;
		const conversationHeight = conversationRect.height;

		lastAiChatBubble.style.setProperty( '--animating-word-y', `${wordOffsetTop}px`, );

		if (
			( conversationAreaScrollDir === 'down' || ( conversationAreaScrollDir === 'up' && conversationAreaScrollDelta < 3 ) ) &&
        ( conversationAreaScrollLocked || ( wordTop < ( conversationHeight * 4/5 )))
		) {
			let scrollPosition;
			if (word.nodeName === 'PRE') {
				scrollPosition = aiBubbleOffsetTop + wordOffsetTop + wordHeight;
			} else {
				scrollPosition = aiBubbleOffsetTop + wordOffsetTop - (conversationArea.clientHeight / 2) + (wordHeight / 2);
			}

			conversationArea.scroll({
				top: scrollPosition,
				behavior: 'smooth',
			});
		}
	}, 175, { leading: false },
);

function onAiResponse() {
	const aiBubbleChatContent =
		lastAiChatBubble?.querySelector('.chat-content');

	if (!aiBubbleChatContent) return;

	lastAiChatBubble.classList.remove('loading');

	const responseString = getAiResponseString(false);
	const animationKeyframes = [
		{ opacity: 0, transform: 'translateX(3px)', filter: 'blur(2px)' },
		{ opacity: 1, transform: 'translateX(0)', filter: 'blur(0)' },
	];

	const formattedResponse = formatString(responseString, {
		readyForAnimation: true,
	});

	if (!formattedResponse.trim()) return;

	const vdom = liquidVDOM.parseHTML(formattedResponse);
	const dom = aiBubbleChatContent;

	liquidVDOM.clean(dom);
	liquidVDOM.diff(vdom, dom);

	const animatedWords = [ ...aiBubbleChatContent.querySelectorAll('.animated-word') ]
		.filter(word => !word.classList.contains('animated') && parseInt(word.getAttribute('data-index'), 10) >= animatedWordIndex);

	aiResponseAnimating = true;

	animatedWords.forEach(word => {
		const dataIndex = parseInt(word.getAttribute('data-index'), 10);
		const delay = (dataIndex - lastFinishedAnimatedWordIndex) * 0.02 * 1000;

		animatedWordIndex = dataIndex;

		if ( dataIndex <= lastFinishedAnimatedWordIndex ){
			word.classList.add('animated');
			handleConversationsAreaScroll(word);
			return;
		}

		word.animate(animationKeyframes, {
			duration: 125,
			easing: 'ease',
			fill: 'both',
			delay: delay,
		}).onfinish = () => {
			const isDoneSignal = word.classList.contains('done-signal');

			word.classList.add('animated');

			if ( !isDoneSignal ) {
				lastFinishedAnimatedWordIndex = Math.max(lastFinishedAnimatedWordIndex, dataIndex);
				handleConversationsAreaScroll(word);
			}

			if (!aiResponseStreaming && isDoneSignal) {
				aiResponseAnimating = false;
				lastAiChatBubble.classList.remove('animating-words');
				switchGenerateButtonsStatus(false);
			}
		};
	});
}

_.observe(aiResponseTextArray, 'create', _.throttle(onAiResponse, 100));

function updateFav(id) {
	$.ajax({
		type: 'post',
		url: '/dashboard/user/openai/chat/update-prompt',
		data: {
			id: id,
		},
		success: function (data) {
			favData = data;
			updatePrompts(promptsData);
		},
		error: function () {},
	});
}

function updatePrompts(data) {
	const $prompts = $('#prompts');

	$prompts.empty();

	if (data.length == 0) {
		$('#no_prompt').removeClass('hidden');
	} else {
		$('#no_prompt').addClass('hidden');
	}

	for (let i = 0; i < data.length; i++) {
		let isFav = favData.filter(item => item.item_id == data[i].id).length;

		let title = data[i].title.toLowerCase();
		let prompt = data[i].prompt.toLowerCase();
		let searchStr = searchString.toLowerCase();

		if (data[i].id == selectedPrompt) {
			if (title.includes(searchStr) || prompt.includes(searchStr)) {
				if ( (filterType == 'fav' && isFav != 0) || filterType != 'fav' ) {
					let prompt = document.querySelector('#selected_prompt').content.cloneNode(true);
					const favbtn = prompt.querySelector('.favbtn');
					prompt.querySelector('.prompt_title').innerHTML = data[i].title;
					prompt.querySelector('.prompt_text').innerHTML = data[i].prompt;
					favbtn.setAttribute('id', data[i].id);

					if (isFav != 0) {
						favbtn.classList.add('active');
					} else {
						favbtn.classList.remove('active');
					}

					$prompts.append(prompt);
				} else {
					selectedPrompt = -1;
				}
			} else {
				selectedPrompt = -1;
			}
		} else {
			if (title.includes(searchStr) || prompt.includes(searchStr)) {
				if (
					(filterType == 'fav' && isFav != 0) ||
					filterType != 'fav'
				) {
					let prompt = document
						.querySelector('#unselected_prompt')
						.content.cloneNode(true);
					const favbtn = prompt.querySelector('.favbtn');
					prompt.querySelector('.prompt_title').innerHTML =
						data[i].title;
					prompt.querySelector('.prompt_text').innerHTML =
						data[i].prompt;
					favbtn.setAttribute('id', data[i].id);

					if (isFav != 0) {
						favbtn.classList.add('active');
					} else {
						favbtn.classList.remove('active');
					}

					$prompts.append(prompt);
				}
			}
		}
	}
	let favCnt = favData.length;
	let perCnt = data.length;

	if (favCnt == 0) {
		$('#fav_count')[0].innerHTML = '';
	} else {
		$('#fav_count')[0].innerHTML = favCnt;
	}

	if (perCnt == 0 || perCnt == undefined) {
		$('#per_count')[0].innerHTML = '';
	} else {
		$('#per_count')[0].innerHTML = perCnt;
	}
}

function searchStringChange(e) {
	searchString = $('#search_str').val();
	updatePrompts(promptsData);
}

function openNewImageDlg(e) {
	$('#selectImageInput').click();
}

function updatePromptImages() {
	$('#chat_images').empty();
	if (prompt_images.length == 0) {
		$('#chat_images').removeClass('active');
		$('.split_line').addClass('hidden');
		return;
	}
	$('#chat_images').addClass('active');
	$('.split_line').removeClass('hidden');
	for (let i = 0; i < prompt_images.length; i++) {
		let new_image = document
			.querySelector('#prompt_image')
			.content.cloneNode(true);
		$(new_image.querySelector('img')).attr('src', prompt_images[i]);
		$(new_image.querySelector('.prompt_image_close')).attr('index', i);
		$(document.querySelector('#chat_images')).append(new_image);
	}
	let new_image_btn = document
		.querySelector('#prompt_image_add_btn')
		.content.cloneNode(true);
	document.querySelector('#chat_images').append(new_image_btn);
	$('.promt_image_btn').on('click', function (e) {
		e.preventDefault();
		$('#chat_add_image').click();
	});
	$('.prompt_image_close').on('click', function () {
		prompt_images.splice($(this).attr('index'), 1);
		updatePromptImages();
	});
}

function addImagetoChat(data) {
	if (prompt_images.filter(item => item == data).length == 0) {
		prompt_images.push(data);
		updatePromptImages();
	}
}

function initChat() {
	var mediaRecorder;
	var chunks = [];
	var stream_;

	prompt_images = [];

	$('#scrollable_content').animate({ scrollTop: 1000 }, 200);

	// Start recording when the button is pressed
	$('#voice_record_button').click(function () {
		chunks = [];
		navigator.mediaDevices
			.getUserMedia({ audio: true })
			.then(function (stream) {
				stream_ = stream;
				mediaRecorder = new MediaRecorder(stream);
				$('#voice_record_button').addClass('inactive');
				$('#voice_record_stop_button').addClass('active');
				mediaRecorder.ondataavailable = function (e) {
					chunks.push(e.data);
				};
				mediaRecorder.start();
			})
			.catch(function (err) {
				console.log('The following error occurred: ' + err);
				toastr.warning('Audio is not allowed');
			});

		$('#voice_record_stop_button').click(function (e) {
			e.preventDefault();
			$('#voice_record_button').removeClass('inactive');
			$('#voice_record_stop_button').removeClass('active');
			mediaRecorder.onstop = function () {
				var blob = new Blob(chunks, { type: 'audio/mp3' });

				var formData = new FormData();
				var fileOfBlob = new File([ blob ], 'audio.mp3');
				formData.append('file', fileOfBlob);

				chunks = [];

				$.ajax({
					url: '/dashboard/user/openai/chat/transaudio',
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function (response) {
						if (response.length >= 4) {
							$('#prompt').val(response);
						}
					},
					error: function () {
						// Handle the error response
					},
				});
			};
			mediaRecorder.stop();
			stream_
				.getTracks() // get all tracks from the MediaStream
				.forEach(track => track.stop()); // stop each of them
		});
	});

	$('#btn_add_new_prompt').on('click', function (e) {
		prompt_title = $('#new_prompt_title').val();
		prompt = $('#new_prompt').val();

		if (prompt_title.trim() == '') {
			toastr.warning('Please input title');
			return;
		}

		if (prompt.trim() == '') {
			toastr.warning('Please input prompt');
			return;
		}

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/add-prompt',
			data: {
				title: prompt_title,
				prompt: prompt,
			},
			success: function (data) {
				promptsData = data;
				updatePrompts(data);
				$('.custom__popover__back').addClass('hidden');
				$('#custom__popover').removeClass('custom__popover__wrapper');
			},
			error: function () {},
		});
	});

	$('#add_btn').on('click', function (e) {
		$('#custom__popover').addClass('custom__popover__wrapper');
		$('.custom__popover__back').removeClass('hidden');
		e.stopPropagation();
	});

	$('.custom__popover__back').on('click', function () {
		$(this).addClass('hidden');
		$('#custom__popover').removeClass('custom__popover__wrapper');
	});

	$('#prompt_library').on('click', function (e) {
		e.preventDefault();

		$('#prompts').empty();

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/prompts',
			success: function (data) {
				filterType = 'all';
				promptsData = data.promptData;
				favData = data.favData;
				updatePrompts(data.promptData);
				$('#modal').addClass('lqd-is-active');
				$('.modal__back').removeClass('hidden');
			},
			error: function () {},
		});
		e.stopPropagation();
	});

	$('.modal__back').on('click', function () {
		$(this).addClass('hidden');
		$('#modal').removeClass('lqd-is-active');
	});

	$(document).on('click', '.prompt', function () {
		const $promptInput = $('#prompt');
		selectedPrompt = Number($(this.querySelector('.favbtn')).attr('id'));
		$promptInput.val(
			promptsData.filter(item => item.id == selectedPrompt)[0].prompt,
		);
		$('.modal__back').addClass('hidden');
		$('#modal').removeClass('lqd-is-active');
		selectedPrompt = -1;
		$promptInput.css('height', '5px');
		$promptInput.css('height', $promptInput[0].scrollHeight + 'px');
	});

	$(document).on('click', '.filter_btn', function () {
		$('.filter_btn').removeClass('active');
		$(this).addClass('active');
		filterType = $(this).attr('filter');
		updatePrompts(promptsData);
	});

	$(document).on('click', '.favbtn', function (e) {
		updateFav(Number($(this).attr('id')));
		e.stopPropagation();
	});

	$('#chat_add_image').click(function () {
		$('#selectImageInput').click();
	});

	$('#selectImageInput').change(function () {
		if (this.files && this.files[0]) {
			for (let i = 0; i < this.files.length; i++) {
				let reader = new FileReader();
				// Existing image handling code
				reader.onload = function (e) {
					var img = new Image();
					img.src = e.target.result;
					img.onload = function () {
						var canvas = document.createElement('canvas');
						var ctx = canvas.getContext('2d');
						canvas.height = (img.height * 200) / img.width;
						canvas.width = 200;
						ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
						var base64 = canvas.toDataURL('image/png');
						addImagetoChat(base64);
					};
				};
				reader.readAsDataURL(this.files[i]);
			}
		}
		document.getElementById('mainupscale_src') &&
			(document.getElementById('mainupscale_src').style.display = 'none');
	});

	$('#upscale_src').change(function () {
		if (this.files && this.files[0]) {
			for (let i = 0; i < this.files.length; i++) {
				let reader = new FileReader();

				reader.onload = function (e) {
					addImagetoChat(e.target.result);
					// $( "#selectImageInput" ).val( '' );
				};
				reader.readAsDataURL(this.files[i]);
			}
		}
		document.getElementById('mainupscale_src') &&
			(document.getElementById('mainupscale_src').style.display = 'none');
	});

	document
		.querySelectorAll('.lqd-chat-ai-bubble .chat-content')
		?.forEach(el => {
			el.classList.remove('!whitespace-pre-wrap', 'whitespace-pre-wrap');
			el.style.whiteSpace = 'normal';
			el.innerHTML = formatString(el.innerHTML);
		});
}

async function saveResponseAsync(
	input,
	response,
	chat_id,
	imagePath,
	pdfName,
	pdfPath,
	outputImage = '',
	model = '',
) {
	var formData = new FormData();

	if (!response) {
		response = '';
	}

	formData.append('chat_id', chat_id);
	formData.append('input', input);
	formData.append('response', response);
	formData.append('images', imagePath);
	formData.append('pdfName', pdfName);
	formData.append('pdfPath', pdfPath);
	formData.append('outputImage', outputImage);
	formData.append('model', model);

	await jQuery.ajax({
		url: '/dashboard/user/openai/chat/low/chat_save',
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
		},
		data: formData,
		contentType: false,
		processData: false,
	});

	return false;
}

/*

DO NOT FORGET TO ADD THE CHANGES TO BOTH FUNCTION makeDocumentReadyAgain and the document ready function on the top!!!!

*/
function makeDocumentReadyAgain() {
	_.defer(() => {
		updateChatButtons();
		conversationAreaScrollHandler();
	});

	$(document).ready(function () {
		'use strict';

		const chat_id = $('#chat_id').val();
		$(`#chat_${chat_id}`)
			.addClass('active')
			.siblings()
			.removeClass('active');

		scrollConversationArea();

		handlePromptHistoryNavigate();
	});
}

function handlePromptHistory(prompt) {
	const promptHistory = localStorage.getItem('promptHistory');

	if (!promptHistory) {
		return localStorage.setItem('promptHistory', JSON.stringify([ prompt ]));
	}

	const promptHistoryArray = JSON.parse(promptHistory);

	if (promptHistoryArray.at(-1) !== prompt) {
		if (promptHistoryArray.length >= 20) {
			promptHistoryArray.shift();
		}

		promptHistoryArray.push(prompt);
	}

	localStorage.setItem('promptHistory', JSON.stringify(promptHistoryArray));
}

function removePromptHistoryHandler() {
	const promptInput = document.querySelector('.lqd-chat-form #prompt');

	promptInput?.removeEventListener('keydown', onPromptInputKeyUpDown);
}

function handlePromptHistoryNavigate() {
	const promptInput = document.querySelector('.lqd-chat-form #prompt');

	if (!promptInput) return;

	promptInput.addEventListener('keydown', onPromptInputKeyUpDown);
}

function onPromptInputKeyUpDown(e) {
	const promptInput = e.target;
	const promptHistory = localStorage.getItem('promptHistory') || '[]';
	const promptHistoryArray = JSON.parse(promptHistory);

	if (promptHistoryArray.length === 0) return;

	if (promptInput.value !== '' && !navigatingInChatsHistory) {
		return;
	}

	const arrowsPressed = e.key === 'ArrowUp' || e.key === 'ArrowDown';

	if (e.key === 'ArrowUp') {
		navigatingInChatsHistory = true;

		if (selectedHistoryPrompt === -1) {
			selectedHistoryPrompt = promptHistoryArray.length - 1;
		} else {
			selectedHistoryPrompt = Math.max(0, selectedHistoryPrompt - 1);
		}

		promptInput.value = promptHistoryArray[selectedHistoryPrompt];
	}

	if (e.key === 'ArrowDown') {
		navigatingInChatsHistory = true;

		if (selectedHistoryPrompt === -1) {
			selectedHistoryPrompt = 0;
		} else {
			selectedHistoryPrompt = Math.min(
				promptHistoryArray.length - 1,
				selectedHistoryPrompt + 1,
			);
		}

		promptInput.value = promptHistoryArray[selectedHistoryPrompt];
	}

	if (!arrowsPressed) {
		navigatingInChatsHistory = false;
		selectedHistoryPrompt = -1;
	}
}

handlePromptHistoryNavigate();

function updateChatButtons() {
	const generateBtn = document.getElementById('send_message_button');
	const stopBtn = document.getElementById('stop_button');
	const promptInput = document.getElementById('prompt');
	const realtime = document.getElementById('realtime');
	const chat_brand_voice = document.getElementById('chat_brand_voice');
	const brand_voice_prod = document.getElementById('brand_voice_prod');
	const chatbot_front_model = document.getElementById('chatbot_front_model');
	const assistant = document.getElementById('assistant');

	let controller = null; // Store the AbortController instance
	let nIntervId = null;
	let chunk = [];

	function onBeforePageUnload(e) {
		e.preventDefault();
		e.returnValue = '';
	}

	const generate = async ev => {
		'use strict';

		ev?.preventDefault();

		const mainUpscaleSrc = document.querySelector('#mainupscale_src');
		const suggestions = document.querySelector('#sugg');
		const promptInputValue = promptInput.value;
		const realtimePrompt = promptInput.value;

		if (mainUpscaleSrc) {
			mainUpscaleSrc.style.display = 'none';
		}
		if (suggestions) {
			suggestions.style.display = 'none';
		}

		// Alert the user if no prompt value
		if (
			!promptInputValue ||
			promptInputValue.length === 0 ||
			promptInputValue.replace(/\s/g, '') === ''
		) {
			return toastr.error(
				magicai_localize?.please_fill_message ||
					'Please fill the message field',
			);
		}

		const chatsContainer = $('.chats-container');
		const userBubbleTemplate = document
			.querySelector('#chat_user_bubble')
			.content.cloneNode(true);
		const aiBubbleTemplate = document
			.querySelector('#chat_ai_bubble')
			.content.cloneNode(true);

		if (category.slug != 'ai_chat_image') {
			aiBubbleTemplate.querySelector('.lqd-typing-loader')?.remove();
		} else {
			aiBubbleTemplate
				.querySelector('.chat-content-container')
				?.classList?.add('flex', 'items-center');
			aiBubbleTemplate.querySelector('.lqd-typing')?.remove();
			aiBubbleTemplate.querySelector('button')?.remove();
		}

		if (generateBtn.classList.contains('submitting')) return;

		const prompt1 = atob(guest_event_id);
		const prompt2 = atob(guest_look_id);
		const prompt3 = atob(guest_product_id);
		const chat_id = $('#chat_id').val();
		const bearer = prompt1 + prompt2 + prompt3;

		switchGenerateButtonsStatus(true);

		animatedWordIndex = 0;
		lastFinishedAnimatedWordIndex = -1;
		aiResponseStreaming = true;
		conversationAreaScrollDir = 'down';

		userBubbleTemplate.querySelector('.chat-content').innerHTML =
			promptInputValue;

		handlePromptHistory(promptInputValue);

		promptInput.value = '';
		promptInput.style.height = '';

		chatsContainer.append(userBubbleTemplate);

		for (let i = 0; i < prompt_images.length; i++) {
			const chatImageBubbleTemplate = document
				.querySelector('#chat_user_image_bubble')
				.content.cloneNode(true);

			chatImageBubbleTemplate.querySelector('a').href = prompt_images[i];
			chatImageBubbleTemplate.querySelector('.img-content').src =
				prompt_images[i];

			chatsContainer.append(chatImageBubbleTemplate);
		}

		refreshFsLightbox();

		// Create a new AbortController instance
		controller = new AbortController();

		const signal = controller.signal;
		const aiBubbleWrapper = aiBubbleTemplate.firstElementChild;
		const aiBubbleChatContent =
			aiBubbleWrapper.querySelector('.chat-content');
		let responseText = '';

		lastAiChatBubble = aiBubbleWrapper;

		aiBubbleWrapper.classList.add('loading', 'animating-words');
		aiBubbleChatContent.innerHTML = responseText;
		chatsContainer.append(aiBubbleTemplate);

		scrollConversationArea({ smooth: true });

		if (prompt_images.length == 0) {
			messages.push({
				role: 'user',
				content: promptInputValue,
			});
		} else {
			messages.push({
				role: 'user',
				content: promptInputValue,
			});
		}

		if (category.slug == 'ai_chat_image') {
			let image_formData = new FormData();

			image_formData.append('prompt', promptInputValue);
			image_formData.append('chatHistory', JSON.stringify(messages));

			let response = await $.ajax({
				url: '/dashboard/user/openai/image/generate',
				type: 'POST',
				data: image_formData,
				processData: false,
				contentType: false,
			});

			const chatImageBubbleTemplate = document
				.querySelector('#chat_bot_image_bubble')
				.content.cloneNode(true);

			chatImageBubbleTemplate.querySelector('a').href = response.path;
			chatImageBubbleTemplate.querySelector('.img-content').src =
				response.path;

			chatsContainer.append(chatImageBubbleTemplate);

			messages.push({
				role: 'assistant',
				content: '',
			});

			if (messages.length >= 6) {
				messages.splice(1, 2);
			}

			saveResponseAsync(
				promptInputValue,
				'',
				chat_id,
				'',
				'',
				'',
				response.path,
			);

			aiBubbleWrapper.classList.remove('loading');

			switchGenerateButtonsStatus(false);

			controller = null; // Reset the AbortController instance

			window.removeEventListener('beforeunload', onBeforePageUnload);

			refreshFsLightbox();

			scrollConversationArea();

			aiResponseStreaming = false;

			return;
		}

		let guest_id2 = atob(guest_id);
		let guest_search_i = atob(guest_search);
		let guest_search_k = atob(guest_search_id);

		// to prevent from reloading when generating respond
		window.addEventListener('beforeunload', onBeforePageUnload);

		chunk = [];
		aiResponseTextArray.splice(0, aiResponseTextArray.length);

		nIntervId = setInterval(function () {
			let text = chunk.shift();

			if (text) {
				streamed_text =
					streamed_text + text.replace(/<br\s*\/?>/g, '\n');
			}
		}, 20);

		function implementChatBackend(type, images) {
			var formData = new FormData();
			var receivedMessageId = false;

			formData.append('template_type', type);
			formData.append('prompt', promptInputValue);
			formData.append('chat_id', chat_id);
			formData.append('category_id', category.id);
			formData.append('images', images == undefined ? '' : images);
			formData.append('pdfname', pdfName == undefined ? '' : pdfName);
			formData.append('pdfpath', pdfPath == undefined ? '' : pdfPath);
			formData.append('realtime', realtime?.checked ? 1 : 0);
			formData.append('chat_brand_voice', chat_brand_voice?.value || '');
			formData.append('brand_voice_prod', brand_voice_prod?.value || '');
			formData.append(
				'chatbot_front_model',
				chatbot_front_model?.value || '',
			);
			formData.append('assistant', assistant.value || '');

			fetchEventSource('/dashboard/user/generator/generate-stream', {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
						'content',
					),
				},
				body: formData,
				signal: signal,
				onmessage: e => {
					if (!receivedMessageId) {
						const eventData = e.event
							.split('\n')
							.reduce((acc, line) => {
								if (line.startsWith('message')) {
									acc.type = 'message';
									acc.data = e.data;
								}

								return acc;
							}, {});

						if (eventData.type === 'message') {
							streamed_message_id = eventData.data;
							receivedMessageId = true;
						}

						return;
					}

					const txt = e.data;

					if (e.data === '[DONE]') {
						aiResponseStreaming = false;

						aiBubbleWrapper.classList.remove('loading');

						messages.push({
							role: 'assistant',
							content: getAiResponseString(),
						});

						if (messages.length >= 6) {
							messages.splice(1, 2);
						}

						controller = null; // Reset the AbortController instance

						window.removeEventListener(
							'beforeunload',
							onBeforePageUnload,
						);

						clearInterval(nIntervId);

						console.log('sreaming done');

						aiResponseTextArray.push(' [DONE]');

						changeChatTitle(streamed_message_id);
					}

					if (txt !== undefined && e.data !== '[DONE]') {
						chunk.push(txt);
						aiResponseTextArray.push(txt);
					}
				},
				onclose: () => {
					// console.log('Connection closed');
					streamed_message_id = 0;
					streamed_text = '';
				},
				onerror: err => {
					throw err; // stop retrying
				},
			});
		}

		async function implementChatFrontend() {
			// Fetch the response from the OpenAI API with the signal from AbortController
			var temp = [ ...prompt_images ];
			let resmodel = temp.length == 0 ? openai_model : 'gpt-4o';
			let resmessages = [
				...messages.slice(0, messages.length - 1),
				...training,
				messages[messages.length - 1],
			];
			if (resmodel == 'gpt-4o') {
				resmessages = [
					{
						role: 'user',
						content: [
							{
								type: 'text',
								text: promptInputValue,
							},
							...temp.map(item => ({
								type: 'image_url',
								image_url: {
									url: item,
								},
							})),
						],
					},
				];
			}

			var formData = new FormData();
			formData.append('chat_id', chat_id);

			let chatbot = $('#chatbot_id').val();

			if (chatbot) {
				formData.append('chatbot_id', $('#chatbot_id').val());
			}

			formData.append('prompt', promptInputValue);

			$.ajax({
				url: '/pdf/getContent',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: async function (response_) {
					if (!response_.extra_prompt == '') {
						resmessages = [
							{
								role: 'user',
								content:
									'\'this pdf\' means pdf content. Must not reference previous chats if user asking about pdf. Must reference pdf content if only user is asking about pdf. Else just response as an assistant shortly and professionaly without must not referencing pdf content. \n\n\n\nUser question: ' +
									messages[messages.length - 1].content +
									'\n\n\n\n\n Document Content: \n ' +
									response_.extra_prompt,
							},
						];
					}
					if (realtime?.checked && guest_search_k != '') {
						const response1 = await fetch(guest_search_i, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'X-API-KEY': guest_search_k,
							},
							body: JSON.stringify({
								q: realtimePrompt,
							}),
						});
						let jsonContent = await response1.json();
						resmessages = [
							{
								role: 'user',
								content:
									'Prompt: ' +
									realtimePrompt +
									'\n\nWeb search real time results: ' +
									JSON.stringify(jsonContent) +
									'\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.',
							},
						];
					}
					const response = await fetch(guest_id2, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							Authorization: `Bearer ${bearer}`,
						},
						body: JSON.stringify({
							model: resmodel,
							messages: resmessages,
							max_tokens: 2000,
							stream: true, // For streaming responses
						}),
						signal, // Pass the signal to the fetch request
					});

					if (response.status != 200) {
						throw response;
					}
					// Read the response as a stream of data
					const reader = response.body.getReader();
					const decoder = new TextDecoder('utf-8');

					while (true) {
						const { done, value } = await reader.read();

						if (done) {
							const lastAiMessage = getAiResponseString();

							aiResponseStreaming = false;

							messages.push({
								role: 'assistant',
								content: lastAiMessage,
							});

							if (messages.length >= 6) {
								messages.splice(1, 2);
							}

							controller = null; // Reset the AbortController instance

							window.removeEventListener(
								'beforeunload',
								onBeforePageUnload,
							);

							aiResponseTextArray.push(' [DONE]');

							saveResponseAsync(
								promptInputValue,
								lastAiMessage,
								chat_id,
								imagePath,
								pdfName,
								pdfPath,
								'',
							);

							break;
						}

						// Massage and parse the chunk of data
						const chunk1 = decoder.decode(value);
						const lines = chunk1.split('\n');

						const parsedLines = lines
							.map(line => line.replace(/^data: /, '').trim()) // Remove the "data: " prefix
							.filter(line => line !== '' && line !== '[DONE]') // Remove empty lines and "[DONE]"
							.map(line => {
								try {
									return JSON.parse(line);
								} catch (ex) {
									console.log(line);
								}
								return null;
							}); // Parse the JSON string

						for (const parsedLine of parsedLines) {
							if (!parsedLine) continue;
							const { choices } = parsedLine;
							const { delta } = choices[0];
							const { content } = delta;
							// const { finish_reason } = choices[0];

							if (content) {
								chunk.push(content);
								aiResponseTextArray.push(content);
							}
						}
					}
				},
				error: function (xhr, status, error) {
					console.error('Error uploading PDF: ' + error);
				},
			});
		}

		if (stream_type == 'backend') {
			imagePath = [];
			pdfName = '';
			pdfPath = '';

			if (prompt_images.length == 0) {
				implementChatBackend('chatbot');
			} else {
				let temp = [ ...prompt_images ];

				prompt_images = [];

				updatePromptImages();

				$.ajax({
					type: 'POST',
					url: '/images/upload',
					data: {
						images: temp,
					},
					success: function (result) {
						imagePath = result.path;
						implementChatBackend('vision', result.path);
					},
				});
			}
		} else {
			try {
				var temp = [ ...prompt_images ];
				imagePath = [];
				prompt_images = [];

				updatePromptImages();

				$.ajax({
					type: 'POST',
					url: '/images/upload',
					data: {
						images: temp,
					},
					success: function (result) {
						imagePath = result.path;

						implementChatFrontend();
					},
				});
			} catch (error) {
				aiResponseStreaming = false;

				aiBubbleWrapper.classList.remove('loading');

				switchGenerateButtonsStatus(false);

				// Handle fetch request errors
				if (signal.aborted) {
					aiBubbleWrapper.querySelector('.chat-content').innerHTML =
						'Request aborted by user. Not saved.';
				} else {
					switch (error.status) {
						case 429:
							aiBubbleWrapper.querySelector(
								'.chat-content',
							).innerHTML =
								'Api Connection Error. You hit the rate limites of openai requests. Please check your Openai API Key.';
							break;
						default:
							aiBubbleWrapper.querySelector(
								'.chat-content',
							).innerHTML =
								'Api Connection Error. Please contact system administrator via Support Ticket. Error is: API Connection failed due to API keys.';
					}
				}

				clearInterval(nIntervId);

				messages.pop();
			}
		}
	};

	const stop = () => {
		// Abort the fetch request by calling abort() on the AbortController instance
		switchGenerateButtonsStatus(false);

		if (controller) {
			controller.abort();
			controller = null;
			chunk = [];
			reduceOnStop();
		}
	};

	// if promptInput undefined, then refresh the page
	if (promptInput) {
		promptInput.addEventListener('keypress', ev => {
			if (ev.code == 'Enter' && !ev.shiftKey) {
				ev.preventDefault();
				$('.lqd-chat-record-trigger').show();
				return generate();
			}
		});
	}

	generateBtn?.addEventListener('click', generate);
	stopBtn?.addEventListener('click', stop);
}

function escapeHtml(html) {
	var text = document.createTextNode(html);
	var div = document.createElement('div');
	div.appendChild(text);
	return div.innerHTML;
}

function openChatAreaContainer(chat_id) {
	'use strict';

	chatid = chat_id;
	$(`#chat_${chat_id}`).addClass('active').siblings().removeClass('active');

	var formData = new FormData();

	formData.append('chat_id', chat_id);

	let openChatAreaContainerUrl = $('#openChatAreaContainerUrl').val();

	$.ajax({
		type: 'post',
		url: openChatAreaContainerUrl,
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			removePromptHistoryHandler();

			$('#load_chat_area_container > .lqd-card-body').html(data.html);

			initChat();

			messages = [
				{
					role: 'assistant',
					content: prompt_prefix,
				},
			];

			data.lastThreeMessage.forEach(message => {
				messages.push({
					role: 'user',
					content: message.input,
				});
				messages.push({
					role: 'assistant',
					content: message.output,
				});
			});

			makeDocumentReadyAgain();
			if (data.lastThreeMessage != '') {
				document.getElementById('mainupscale_src') &&
					(document.getElementById('mainupscale_src').style.display =
						'none');
				document.getElementById('sugg') &&
					(document.getElementById('sugg').style.display = 'none');
			}
			setTimeout(function () {
				scrollConversationArea();
			}, 750);
		},
		error: function (data) {
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});

	return false;
}

function startNewChat(category_id, local, website_url = null) {
	var formData = new FormData();
	formData.append('category_id', category_id);

	// let website_url = $("#website_url")?.val();
	let createChatUrl = $('#createChatUrl')?.val();

	if (website_url != null) {
		formData.append('website_url', website_url);
	}

	let link = '/dashboard/user/openai/chat/start-new-chat';

	if (createChatUrl) {
		link = createChatUrl;
	}

	return $.ajax({
		type: 'post',
		url: link,
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			removePromptHistoryHandler();

			chatid = data.chat.id;

			$('#load_chat_area_container > .lqd-card-body').html(data.html);
			$('#chat_sidebar_container').html(data.html2);

			initChat();

			messages = [
				{
					role: 'assistant',
					content: prompt_prefix,
				},
			];

			makeDocumentReadyAgain();

			setTimeout(function () {
				scrollConversationArea();
			}, 750);
		},
		error: function (data) {
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});
}

function deleteAllConv(category_id) {
	if (confirm('Are you sure you want to remove all chats?')) {
		if (category_id == 0) {
			toastr.error('Please select a category');
			return false;
		}

		var formData = new FormData();
		formData.append('category_id', category_id);
		let link = '/dashboard/user/openai/chat/clear-chats';
		$.ajax({
			type: 'post',
			url: link,
			data: formData,
			contentType: false,
			processData: false,
			success: function (data) {
				// refresh page
				location.reload();
			},
			error: function (data) {
				var err = data.responseJSON.errors;
				if (err) {
					$.each(err, function (index, value) {
						toastr.error(value);
					});
				} else {
					toastr.error(data.responseJSON.message);
				}
			},
		});
		return false;
	}
}

function startNewDocChat(file, type) {
	'use strict';

	let category_id = $('#chat_search_word').data('category-id');

	var formData = new FormData();
	formData.append('category_id', category_id);
	formData.append('doc', pdf);
	formData.append('type', type);

	Alpine.store('appLoadingIndicator').show();
	$('.lqd-upload-doc-trigger').attr('disabled', true);

	$.ajax({
		type: 'post',
		url: '/dashboard/user/openai/chat/start-new-doc-chat',
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			removePromptHistoryHandler();
			Alpine.store('appLoadingIndicator').hide();
			$('.lqd-upload-doc-trigger').attr('disabled', false);
			$('#selectDocInput').val('');
			chatid = data.chat.id;
			$('#load_chat_area_container > .lqd-card-body').html(data.html);
			$('#chat_sidebar_container').html(data.html2);

			initChat();
			messages = [
				{
					role: 'assistant',
					content: prompt_prefix,
				},
			];
			makeDocumentReadyAgain();
			setTimeout(function () {
				$('.conversation-area')
					.stop()
					.animate(
						{ scrollTop: $('.conversation-area').outerHeight() },
						200,
					);
			}, 750);

			toastr.success(magicai_localize.analyze_file_finish);
		},
		error: function (data) {
			Alpine.store('appLoadingIndicator').hide();
			$('.lqd-upload-doc-trigger').attr('disabled', false);
			$('#selectDocInput').val('');
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});
	return false;
}

function searchChatFunction() {
	'use strict';

	const categoryId = $('#chat_search_word').data('category-id');
	const formData = new FormData();
	formData.append(
		'_token',
		document.querySelector('input[name=_token]')?.value,
	);
	formData.append(
		'search_word',
		document.getElementById('chat_search_word').value,
	);
	formData.append('category_id', categoryId);

	$.ajax({
		type: 'POST',
		url: '/dashboard/user/openai/chat/search',
		data: formData,
		contentType: false,
		processData: false,
		success: function (result) {
			$('#chat_sidebar_container').html(result.html);
			$(document).trigger('ready');
		},
	});
}

/**
 * @param {object} opts
 * @param {'end' | number} opts.y
 * @param {boolean} opts.smooth
 */
function scrollConversationArea(opts = {}) {
	const options = {
		y: 'end',
		smooth: false,
		...opts,
	};
	const el = document.querySelector('.conversation-area');

	if (!el) return;

	const y = options.y === 'end' ? el.scrollHeight + 200 : options.y;

	el.scrollTo({
		top: Math.round(y),
		left: 0,
		behavior: options.smooth ? 'smooth' : 'auto',
	});
}

function saveResponse(
	input,
	response,
	chat_id,
	imagePath = '',
	pdfName = '',
	pdfPath = '',
	outputImage = '',
) {
	var formData = new FormData();
	formData.append('chat_id', chat_id);
	formData.append('input', input);
	formData.append('response', response);
	formData.append('images', imagePath);
	formData.append('pdfName', pdfName);
	formData.append('pdfPath', pdfPath);
	formData.append('outputImage', outputImage);
	jQuery.ajax({
		url: '/dashboard/user/openai/chat/low/chat_save',
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
		},
		data: formData,
		contentType: false,
		processData: false,
	});
	return false;
}

function addText(text) {
	var promptElement = document.getElementById('prompt');
	var currentText = promptElement.value;
	var newText = currentText + text;
	promptElement.value = newText;
}

function dropHandler(ev, id) {
	// Prevent default behavior (Prevent file from being opened)

	ev.preventDefault();
	const input = document.querySelector(`#${id}`);
	const fileNameEl =
		input?.previousElementSibling?.querySelector('.file-name');

	if (!input) return;

	input.files = ev.dataTransfer.files;

	if (fileNameEl) {
		fileNameEl.innerText = ev.dataTransfer.files[0].name;
	}

	for (let i = 0; i < ev.dataTransfer.files.length; i++) {
		let reader = new FileReader();
		// Existing image handling code
		reader.onload = function (e) {
			var img = new Image();
			img.src = e.target.result;
			img.onload = function () {
				var canvas = document.createElement('canvas');
				var ctx = canvas.getContext('2d');
				canvas.height = (img.height * 200) / img.width;
				canvas.width = 200;
				ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
				var base64 = canvas.toDataURL('image/png');
				addImagetoChat(base64);
			};
		};
		reader.readAsDataURL(input.files[i]);
	}
	document.getElementById('mainupscale_src').style.display = 'none';
}

function dragOverHandler(ev) {
	// Prevent default behavior (Prevent file from being opened)
	ev.preventDefault();
}

function handleFileSelect(id) {
	$('#' + id)
		.prev()
		.find('.file-name')
		.text($('#' + id)[0].files[0].name);
}

function exportAsPdf() {
	var win = window.open(
		`/dashboard/user/openai/chat/generate-pdf?id=${chatid}`,
		'_blank',
	);
	win.focus();
}

function exportAsWord() {
	var win = window.open(
		`/dashboard/user/openai/chat/generate-word?id=${chatid}`,
		'_blank',
	);
	win.focus();
}

function exportAsTxt() {
	var win = window.open(
		`/dashboard/user/openai/chat/generate-txt?id=${chatid}`,
		'_blank',
	);
	win.focus();
}

function reduceOnStop() {
	$.ajax({
		type: 'post',
		url: '/dashboard/user/generator/reduce-tokens/chat',
		data: {
			streamed_text: streamed_text,
			streamed_message_id: streamed_message_id,
		},
		success: function (data) {
			streamed_message_id = 0;
			streamed_text = '';
		},
	});
}

$(document).ready(function () {
	'use strict';

	initChat();

	scrollConversationArea();

	_.defer(updateChatButtons);

	function saveChatNewTitle(chatId, newTitle) {
		var formData = new FormData();
		formData.append('chat_id', chatId);
		formData.append('title', newTitle);

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/rename-chat',
			data: formData,
			contentType: false,
			processData: false,
		});
		return false;
	}

	function deleteChatItem(chatId, chatTitle) {
		if (confirm(`Are you sure you want to remove ${chatTitle}?`)) {
			var formData = new FormData();
			formData.append('chat_id', chatId);

			const chatTrigger = $(`#${chatId}`);
			const chatIsActive = chatTrigger.hasClass('active');
			let nextChatToActivate = chatTrigger.prevAll(':visible').first();
			const chatsContainer = document.querySelector('.chats-container');

			if (nextChatToActivate.length === 0) {
				nextChatToActivate = chatTrigger.nextAll(':visible').first();
			}

			$.ajax({
				type: 'post',
				url: '/dashboard/user/openai/chat/delete-chat',
				data: formData,
				contentType: false,
				processData: false,
				success: function (data) {
					//Remove chat li
					chatTrigger.hide();
					if (chatIsActive) {
						if (chatsContainer) {
							chatsContainer.innerHTML = '';
						}
						nextChatToActivate
							.children('.chat-list-item-trigger')
							.click();
					}
					toastr.success(
						magicai_localize.conversation_deleted_successfully,
					);
				},
				error: function (data) {
					var err = data.responseJSON.errors;
					if (err) {
						$.each(err, function (index, value) {
							toastr.error(value);
						});
					} else {
						toastr.error(data.responseJSON.message);
					}
				},
			});
			return false;
		}
	}

	$('#chat_sidebar_container').on('click', '.chat-item-delete', ev => {
		const button = ev.currentTarget;
		const parent = button.closest('li');
		const chatId = parent.getAttribute('id');
		const chatTitle = parent.querySelector('.chat-item-title').innerText;
		deleteChatItem(chatId, chatTitle);
	});

	$('#chat_sidebar_container').on(
		'click',
		'.chat-item-update-title',
		ev => {
			const button = ev.currentTarget;
			const parent = button.closest('.chat-list-item');
			const title = parent.querySelector('.chat-item-title');
			const chatId = parent.getAttribute('id');
			const currentText = title.innerText;

			function setEditMode(mode) {
				if (mode === 'editStart') {
					parent.classList.add('edit-mode');

					title.setAttribute('data-current-text', currentText);
					title.setAttribute('contentEditable', true);
					title.focus();
					window.getSelection().selectAllChildren(title);
				} else if (mode === 'editEnd') {
					parent.classList.remove('edit-mode');

					title.removeAttribute('contentEditable');
					title.removeAttribute('data-current-text');
				}
			}

			function keydownHandler(ev) {
				const { key } = ev;
				const escapePressed = key === 'Escape';
				const enterPressed = key === 'Enter';

				if (!escapePressed && !enterPressed) return;

				ev.preventDefault();

				if (escapePressed) {
					title.innerText = currentText;
				}

				if (enterPressed) {
					saveChatNewTitle(chatId, title.innerText);
				}

				setEditMode('editEnd');
				document.removeEventListener('keydown', keydownHandler);
			}

			// if alreay editting then turn the edit button to a save button
			if (title.hasAttribute('contentEditable')) {
				setEditMode('editEnd');
				document.removeEventListener('keydown', keydownHandler);
				return saveChatNewTitle(chatId, title.innerText);
			}

			$('.chat-list-ul .edit-mode').each((i, el) => {
				const title = el.querySelector('.chat-item-title');
				title.innerText = title.getAttribute('data-current-text');
				title.removeAttribute('data-current-text');
				title.removeAttribute('contentEditable');
				el.classList.remove('edit-mode');
			});

			setEditMode('editStart');

			document.addEventListener('keydown', keydownHandler);
		},
	);

	$('#chat_sidebar_container').on(
		'click',
		'.chat-item-pin',
		ev => {
			const button = ev.currentTarget;
			const parent = button.closest('.chat-list-item');
			const chatId = parent.getAttribute('id');
			const isPinned = parent.classList.contains('pin-mode');

			function togglePinMode() {
				parent.classList.toggle('pin-mode');
				const pinIcon = button.querySelector('.tabler-pin');
				const pinnedIcon = button.querySelector('.tabler-pinned');
				if (pinIcon && pinnedIcon) {
					pinIcon.classList.toggle('hidden');
					pinnedIcon.classList.toggle('hidden');
				}
			}
			togglePinMode();

			$.ajax({
				type: 'post',
				url: '/dashboard/user/openai/chat/pin-conversation',
				data: JSON.stringify({pinned: !isPinned, chat_id: chatId}),
				contentType: 'application/json',
				success: function (data) {
					toastr.success(isPinned ? magicai_localize.conversation_unpinned : magicai_localize.conversation_pinned);
				},
				error: (xhr, status, error) => {
					console.error('Error updating pin status:', error);
					togglePinMode();
					toastr.error( magicai_localize.conversation_pin_error );
				},
			});
		},
	);

	$('#chat_search_word').on('keyup', function () {
		return searchChatFunction();
	});

	$('body').on('input', '#prompt', ev => {
		const el = ev.target;
		el.style.height = '5px';
		el.style.height = el.scrollHeight + 'px';
		const recordTrigger = $('.lqd-chat-record-trigger');

		// check if value is not empty and then hide .lqd-chat-record-trigger and .lqd-chat-record-stop-trigger elements
		if (
			el.value &&
			el.value !== '' &&
			!(Array.isArray(el.value) && el.value.length === 0) &&
			!(
				typeof el.value === 'object' &&
				Object.keys(el.value).length === 0
			)
		) {
			recordTrigger.hide();
		} else {
			recordTrigger.show();
		}
	});

	$('#selectDocInput').change(function () {
		if (this.files && this.files[0]) {
			let reader = new FileReader();
			pdf = this.files[0];

			toastr.success(magicai_localize.analyze_file_begin);

			startNewDocChat(pdf, this.files[0].type);

			document.getElementById('mainupscale_src') &&
				(document.getElementById('mainupscale_src').style.display =
					'none');
		}
	});

	if (stream_type == 'backend') {
		window.addEventListener('beforeunload', function (e) {
			reduceOnStop();
		});
	}
});

$('body').on('click', '.chat-download', event => {
	const button = event.currentTarget;
	const docType = button.dataset.docType;
	const docName = button.dataset.docName || 'document';

	const container = document.querySelector('.chats-container');
	let content = container?.parentElement?.innerHTML;
	let html;

	if (!content) return;

	if (docType === 'pdf') {
		return html2pdf()
			.set({
				filename: docName,
			})
			.from(content)
			.toPdf()
			.save();
	}

	if (docType === 'txt') {
		html = container.innerText;
	} else {
		html = `
	<html ${
	this.doctype === 'doc'
		? 'xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40"'
		: ''
}>
	<head>
		<meta charset="utf-8" />
		<title>${docName}</title>
	</head>
	<body>
		${content}
	</body>
	</html>`;
	}

	const url = `${
		docType === 'doc'
			? 'data:application/vnd.ms-word;charset=utf-8'
			: 'data:text/plain;charset=utf-8'
	},${encodeURIComponent(html)}`;

	const downloadLink = document.createElement('a');
	document.body.appendChild(downloadLink);
	downloadLink.href = url;
	downloadLink.download = `${docName}.${docType}`;
	downloadLink.click();

	document.body.removeChild(downloadLink);
});

function changeChatTitle(streamed_message_id) {
	const $lqdChatUserBubblesLength = document.querySelectorAll(
		'.lqd-chat-user-bubble',
	).length;

	if ($lqdChatUserBubblesLength != 1) return;

	$.ajax({
		type: 'post',
		url: '/dashboard/change-chat-title',
		data: {
			streamed_message_id,
		},
		success: function (data) {
			if (data.changed) {
				const chatTitleEl = document.querySelector(
					`#chat_${data.chat_id} .chat-item-title`,
				);

				if (!chatTitleEl) return;

				const newTitle = data.new_title.replaceAll(' ', '\u00a0');
				const newTitleStringArray = newTitle.split('');

				chatTitleEl.innerText = '';

				const interval = setInterval(() => {
					chatTitleEl.innerText += newTitleStringArray.shift();

					if (!newTitleStringArray.length) {
						clearInterval(interval);
					}
				}, 30);
			}
		},
	});
}

function setChatsCssVars() {
	const chatsWrapper = document.querySelector('.chats-wrap');
	const chatsContainer = document.querySelector('.chats-container');
	const chatsHead = document.querySelector('.lqd-chat-head');
	const chatsForm = document.querySelector('.lqd-chat-form');
	const conversationArea = document.querySelector('.conversation-area');

	if (
		chatsWrapper &&
		chatsContainer &&
		chatsHead &&
		chatsForm &&
		conversationArea
	) {
		chatsWrapper.style.setProperty(
			'--chats-container-height',
			`${conversationArea.offsetHeight - chatsHead.offsetHeight - chatsForm.offsetHeight}px`,
		);
	}
}

(() => {
	setChatsCssVars();

	window.addEventListener('resize', _.debounce(setChatsCssVars, 150));
})();
