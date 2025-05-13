(async function () {
	const scriptTag = document.currentScript;
	const url = new URL(scriptTag.getAttribute('src'));
	const iframeWidth = scriptTag.getAttribute('data-iframe-width');
	const iframeHeight = scriptTag.getAttribute('data-iframe-height');
	let language = scriptTag.getAttribute('data-language') ?? 'en';
	const chatbotHostOrigin = `${url.origin}`;
	const chatBotUuid = scriptTag.getAttribute('data-chatbot-uuid');
	const iFrameUrl = `${chatbotHostOrigin}/chatbot/${chatBotUuid}/frame`;
	const jsonUrl = `${chatbotHostOrigin}/api/v2/chatbot/${chatBotUuid}`;

	if (document.querySelector("html")?.getAttribute("lang")) {

		const htmlLang = document.querySelector("html").getAttribute("lang");

		if (htmlLang) {
			language = htmlLang;
		}
	}

	let config = {
		active: false,
		color: '#763ed1',
		trigger_avatar_size: '60px',
		avatar: null,
		trigger_background: ''
	};

	const getChatbotDetails = async () => {
		if (chatBotUuid) {
			try {
				const response = await fetch(`${jsonUrl}?language=${language}`);
				const data = await response.json();
				const chatbot = data?.data || {};

				config = {
					...config,
					...chatbot
				};
			} catch (error) {
				console.error('Failed to fetch chatbot details:', error);
				return null;
			}
		}
		return null;
	};

	await getChatbotDetails();

	if (!iFrameUrl) {
		console.error('Iframe source is not set');
		return;
	}

	const widgetMarkup = `
<div id="lqd-ext-chatbot-wrap" data-ready="false" data-window-open="false">
    <style>
        #lqd-ext-chatbot-wrap {
            --lqd-ext-chat-trigger-background: ${config.trigger_background && config.trigger_background !== '' ? config.trigger_background : 'var(--lqd-ext-chat-primary)'};
            --lqd-ext-chat-trigger-foreground: ${config.trigger_foreground && config.trigger_foreground !== '' ? config.trigger_foreground : 'var(--lqd-ext-chat-primary-foreground)'};
            display: flex;
            flex-direction: column;
            gap: var(--lqd-ext-chat-window-y-offset, 20px);
            position: fixed;
            bottom: var(--lqd-ext-chat-offset-y, 30px);
            left: var(--lqd-ext-chat-offset-y, 30px);
            z-index: 9999;
            transition: transform 0.3s, opacity 0.3s, visibility 0.3s;
            font-family: var(--lqd-ext-chat-font-family, 'inherit');
            pointer-events: none;
        }

        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger {
            display: inline-grid;
            place-items: center;
            place-content: center;
            width: var(--lqd-ext-chat-trigger-w);
            height: var(--lqd-ext-chat-trigger-h);
            padding: 0;
            position: relative;
            background-color: var(--lqd-ext-chat-trigger-background);
            color: var(--lqd-ext-chat-trigger-foreground);
            border-radius: var(--lqd-ext-chat-trigger-w);
            border: none;
            overflow: hidden;
            transition: all 0.15s;
            cursor: pointer;
            backdrop-filter: blur(12px) saturate(120%);
            pointer-events: auto;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
        }
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger:before {
            content: '';
            display: inline-block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background-color: var(--lqd-ext-chat-primary);
            opacity: 0;
            transform: translateY(3px);
            transition: all 0.15s;
        }
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-img,
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-icon {
            grid-row: 1 / 1;
            grid-column: 1 / 1;
            transition: all 0.15s;
        }
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-img {
            width: ${config.trigger_avatar_size ? `${parseInt(config.trigger_avatar_size, 10) }px` : '100%'};
            height: auto;
            max-width: none;
            position: relative;
            z-index: 1;
        }
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-icon {
            opacity: 0;
            transform: translateY(3px);
        }
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger:active {
            transform: scale(0.9);
        }

        #lqd-ext-chatbot-iframe-wrap {
            width: min(var(--lqd-ext-chat-window-w), calc(100vw - (var(--lqd-ext-chat-offset-x) * 2)));
            height: min(var(--lqd-ext-chat-window-h), calc(100vh - (var(--lqd-ext-chat-offset-y) * 2) - var(--lqd-ext-chat-trigger-h) - var(--lqd-ext-chat-window-y-offset)));
            box-shadow: 0 5px 40px hsl(0 0% 0% / 16%);
            border-radius: 12px;
            pointer-events: none;
            transform-origin: bottom left;
            transform: scale(0.975) translateY(6px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.1s;
        }

        #lqd-ext-chatbot-iframe {
            width: 100%;
            height: 100%;
        }

        #lqd-ext-chatbot-welcome-bubble {
            padding: 12px 16px;
            border-radius: 12px;
            position: absolute;
            bottom: calc(var(--lqd-ext-chat-trigger-h) + var(--lqd-ext-chat-window-y-offset));
            left: 0;
            color: var(--lqd-ext-chat-primary);
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2em;
            backdrop-filter: blur(12px) saturate(120%) brightness(1.75);
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: all 0.15s;
        }
         #lqd-ext-chatbot-welcome-bubble:before {
            content: '';
            display: inline-block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background-color: var(--lqd-ext-chat-primary);
            opacity: 0.05;
            border-radius: inherit;
        }
        #lqd-ext-chatbot-welcome-bubble p {
            position: relative;
            z-index: 1;
            margin: 0;
        }

        .lqd-ext-chatbot-not-loaded {
            margin: 0;
            padding: 1rem;
        }

        #lqd-ext-chatbot-wrap[data-ready=true] #lqd-ext-chatbot-trigger,
        #lqd-ext-chatbot-wrap[data-ready=true] #lqd-ext-chatbot-welcome-bubble {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-iframe-wrap {
            transform: translateY(0) scale(1);
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-trigger:before {
            transform: translateY(0);
            opacity: 1;
        }
        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-trigger-icon {
            opacity: 1;
            transform: translateY(0);
        }
        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-trigger-img {
            opacity: 0;
            transform: translateY(-3px);
        }
        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-welcome-bubble {
            transform: scale(0.95);
            opacity: 0;
            visibility: hidden;
        }

        #lqd-ext-chatbot-wrap[data-pos-x=right] {
            left: auto;
            right: var(--lqd-ext-chat-offset-x, 30px);
            align-items: end;
        }

        #lqd-ext-chatbot-wrap[data-pos-x=right] #lqd-ext-chatbot-iframe-wrap {
            transform-origin: bottom right;
        }

        #lqd-ext-chatbot-wrap[data-pos-x=right] #lqd-ext-chatbot-welcome-bubble {
            left: auto;
            right: 0;
        }

        #lqd-ext-chatbot-wrap[data-pos-y=top] {
            bottom: auto;
            top: var(--lqd-ext-chat-offset-y, 30px);
            flex-direction: column-reverse;
        }

        #lqd-ext-chatbot-wrap[data-pos-y=top] #lqd-ext-chatbot-welcome-bubble {
            bottom: auto;
            top: calc(var(--lqd-ext-chat-trigger-h) + var(--lqd-ext-chat-window-y-offset));
        }
    </style>
    <div id="lqd-ext-chatbot-iframe-wrap">
        ${iFrameUrl ? `
            <iframe
                src="${iFrameUrl}"
                title="${config.title}"
                frameborder="0"
                allowfullscreen
                allowtransparency
                id="lqd-ext-chatbot-iframe"
                name="lqd-ext-chatbot-iframe"
                crossOrigin="anonymous"
                onload="
                    const wrapper = document.querySelector('#lqd-ext-chatbot-wrap');
                    window.addEventListener('message', event => {
                        if ( event.origin !== '${chatbotHostOrigin}' || event.data.type !== 'lqd-ext-chatbot-response-styling' || !wrapper ) return;
                        const { styles, attrs } = event.data.data;
                        Object.entries(styles).forEach(([key, value]) => {
                            if ( key === '--lqd-ext-chat-window-w' && ${iframeWidth ? true : false} ) {
                                return wrapper.style.setProperty(key, '${parseInt(iframeWidth, 10)}px');
                            } else if ( key === '--lqd-ext-chat-window-h' && ${iframeHeight ? true : false} ) {
                                return wrapper.style.setProperty(key, '${parseInt(iframeHeight, 10)}px');
                            }
                            wrapper.style.setProperty(key, value);
                        });
                        Object.entries(attrs).forEach(([key, value]) => {
                            wrapper.setAttribute(key, value);
                        });
                        wrapper.setAttribute('data-ready', 'true');
                    });

                    this.contentWindow.postMessage({
                        type: 'lqd-ext-chatbot-request-styling',
                    }, '${chatbotHostOrigin}');
                "
            ></iframe>` : `
        <p class="lqd-ext-chatbot-not-loaded">Could not setup the chatbot</p>
        `}
    </div>
    ${config.bubble_message && config.bubble_message !== '' ?
		`<div id="lqd-ext-chatbot-welcome-bubble">
                <p>
                    ${config.bubble_message}
                </p>
            </div>`
		:
		''
	}
    <button
        id="lqd-ext-chatbot-trigger"
        type="button"
        @click.prevent="toggleWindowState()"
    >
        <img
            id="lqd-ext-chatbot-trigger-img"
            src="${chatbotHostOrigin}${config.avatar}"
            alt="${config.title}"
            width="60"
            height="60"
        />
        <span id="lqd-ext-chatbot-trigger-icon">
            <svg
                width="16"
                height="10"
                viewBox="0 0 16 10"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path d="M8 9.07814L0.75 1.82814L2.44167 0.136475L8 5.69481L13.5583 0.136475L15.25 1.82814L8 9.07814Z" />
            </svg>
        </span>
    </button>
</div>`;

	document.body.insertAdjacentHTML('beforeend', widgetMarkup);

	const chatbotWrap = document.querySelector('#lqd-ext-chatbot-wrap');
	const trigger = document.querySelector('#lqd-ext-chatbot-trigger');
	let open = false;

	trigger.addEventListener('click', ev => {
		ev.preventDefault();
		open = !open;
		chatbotWrap.setAttribute('data-window-state', open ? 'open' : 'close');
	});
})();