(() => {
	'use strict';

	const dropdownMenus = document.querySelectorAll('.dropdown-menu');
	const mobileNavTrigger = document.querySelector('.mobile-nav-trigger');
	const siteNavContainer = document.querySelector('.site-nav-container');
	const templatesShowMore = document.querySelector('.templates-show-more');
	const filterTriggers = document.querySelectorAll('button[data-target]');
	const toggleClassnameTriggers = document.querySelectorAll( '[data-lqd-toggle-class]' );
	const frontendLocalNav = document.querySelector('#frontend-local-navbar');
	const textRotators = document.querySelectorAll('.lqd-text-rotator');
	const setAnchors = document.querySelectorAll('[data-set-anchor=true]');
	let lastActiveTrigger = null;
	let lastOpenedAccordion = null;

	textRotators?.forEach(textRotator => {
		const items = textRotator.querySelectorAll('.lqd-text-rotator-item');

		if (!items.length) return;

		const timeout = 2000;
		let activeIndex = 0;

		textRotator.style.width = `${
			items[activeIndex].querySelector('span').clientWidth
		}px`;

		setInterval(() => {
			// current item
			items[activeIndex].classList.remove('lqd-is-active');

			// now next item
			activeIndex =
				activeIndex === items.length - 1 ? 0 : activeIndex + 1;
			textRotator.style.width = `${
				items[activeIndex].querySelector('span').clientWidth
			}px`;
			items[activeIndex].classList.add('lqd-is-active');
		}, timeout);
	});

	dropdownMenus.forEach(dd => {
		if (document.body.classList.contains('navbar-shrinked')) {
			dd.classList.remove('show');
		}
	});

	document.addEventListener('click', ev => {
		const { target } = ev;
		dropdownMenus.forEach(dd => {
			if (
				!document.body.classList.contains('navbar-shrinked') &&
				dd.closest('.primary-nav')
			)
				return;
			const clickedOutside = !dd.parentElement.contains(target);
			if (clickedOutside) {
				dd.classList.remove('show');
			}
		});
	});

	templatesShowMore?.addEventListener('click', ev => {
		ev.preventDefault();
		const list = document.querySelector('.templates-cards');
		const overlay = document.querySelector('.templates-cards-overlay');

		list.style.overflow = 'visible';
		list.style.maxHeight = 'none';

		overlay.animate([ { opacity: 0 } ], {
			duration: 650,
			fill: 'forwards',
			easing: 'ease-out',
		});
		const btnAnima = templatesShowMore.animate([ { opacity: 0 } ], {
			duration: 650,
			fill: 'forwards',
			easing: 'ease-out',
		});
		btnAnima.onfinish = () => {
			overlay.style.visibility = 'hidden';
			templatesShowMore.style.visibility = 'hidden';
		};
	});

	filterTriggers?.forEach(trigger => {
		const targetId = trigger.getAttribute('data-target');
		const targets = document.querySelectorAll(targetId);
		const triggerType =
			trigger.getAttribute('data-trigger-type') || 'toggle';

		if (targets.length <= 0) {
			return trigger.setAttribute('disabled', true);
		}

		trigger.addEventListener('click', ev => {
			ev?.preventDefault();

			trigger.classList.add('lqd-is-active');

			if (triggerType === 'toggle') {
				[ ...trigger.parentElement.children ]
					.filter(c => c.getAttribute('data-target') !== targetId)
					.forEach(c => c.classList.remove('lqd-is-active'));
			} else if (triggerType === 'accordion') {
				if (lastActiveTrigger) {
					lastActiveTrigger.classList.remove('lqd-is-active');
				}
				if (lastActiveTrigger === trigger) {
					lastActiveTrigger = null;
				} else {
					lastActiveTrigger = trigger;
				}
			}

			targets?.forEach(t => {
				t.style.display = 'block';
				t.animate([ { opacity: 0 }, { opacity: 1 } ], {
					duration: 650,
					easing: 'cubic-bezier(.48,.81,.52,.99)',
				});
			});

			if (triggerType === 'toggle') {
				[ ...targets[0]?.parentElement?.children ]
					?.filter(c =>
						targetId.startsWith('.')
							? !c.classList.contains(targetId.replace('.', ''))
							: c.getAttribute('id') !== targetId.replace('#', '')
					)
					?.forEach(c => (c.style.display = 'none'));
			} else if (triggerType === 'accordion') {
				if (lastOpenedAccordion) {
					lastOpenedAccordion.style.display = 'none';
				}
				if (lastOpenedAccordion === targets[0]) {
					lastOpenedAccordion = null;
				} else {
					lastOpenedAccordion = targets[0];
				}
			}
		});
	});

	toggleClassnameTriggers?.forEach(trigger => {
		const target = trigger.getAttribute('data-lqd-toggle-target');
		let targetEls = target ? document.querySelectorAll(target) : [];

		trigger.addEventListener('click', ev => {
			ev?.preventDefault();
			trigger.classList.toggle('lqd-is-active');
			targetEls.forEach(t => t.classList.toggle('lqd-is-active'));
		});
	});

	if (frontendLocalNav?.querySelector('ul')) {
		const scrollspy = VanillaScrollspy({ menu: frontendLocalNav.querySelector('ul') });
		scrollspy.init();
	}

	mobileNavTrigger?.addEventListener('click', ev => {
		ev.preventDefault();
		mobileNavTrigger?.classList.toggle('lqd-is-active');
		siteNavContainer?.classList.toggle('lqd-is-active');
	});

	siteNavContainer?.querySelectorAll('a')?.forEach(link => {
		link.addEventListener('click', ev => {
			mobileNavTrigger?.classList.remove('lqd-is-active');
			siteNavContainer?.classList.remove('lqd-is-active');
		});
	});

	document.addEventListener('click', ev => {
		const clickedEl = ev.target;
		const clipboardCopyButton = clickedEl.closest('.lqd-clipboard-copy');

		// Close mobile nav when clicked outside
		if (
			siteNavContainer?.classList.contains('lqd-is-active') &&
			!siteNavContainer?.contains(clickedEl) &&
			!siteNavContainer !== clickedEl &&
			!mobileNavTrigger?.contains(clickedEl) &&
			!mobileNavTrigger !== clickedEl
		) {
			mobileNavTrigger?.classList.remove('lqd-is-active');
			siteNavContainer?.classList.remove('lqd-is-active');
		}

		// Copy to clipboard
		if (clipboardCopyButton) {
			const settings = JSON.parse(
				clipboardCopyButton.getAttribute('data-copy-options') || '{}'
			);
			let getContentFrom;

			if (settings.contentIn) {
				if (settings.contentIn.startsWith('<') && settings.content) {
					const el = clipboardCopyButton.parentElement.closest(
						settings.contentIn.replace('<', '')
					);
					getContentFrom = el.querySelector(settings.content);
				}
			} else {
				getContentFrom = settings.content
					? document.querySelector(settings.content)
					: clipboardCopyButton.parentElement;
			}

			if (getContentFrom) {
				const copyButtonsTemplate = document.querySelector('#copy-btns-template');
				const wrapper = clipboardCopyButton.closest('.lqd-clipboard-copy-wrap');
				let textContent = extractTextWithLinks(getContentFrom);

				wrapper?.classList?.toggle('active');

				copyToClipboard(textContent, 'text');

				if ( wrapper && copyButtonsTemplate && !clipboardCopyButton.classList.contains('copy-buttons-appended') ) {
					const copyButtons = copyButtonsTemplate.content.cloneNode(true);
					const buttons = copyButtons.querySelectorAll('button');

					wrapper.appendChild(copyButtons);
					clipboardCopyButton.classList.add('copy-buttons-appended');

					buttons.forEach(button => {
						button.addEventListener('click', () => {
							const copyType = button.getAttribute('data-copy-type');

							if ( copyType === 'text' ) {
								copyToClipboard(textContent, 'text');
							} else if ( copyType === 'html' ) {
								const unwrappedContent = getContentFrom.innerHTML
									.replace(/<span[^>]*class="[^"]*animated-word[^"]*"[^>]*>(.*?)<\/span>/g, '$1')
									.replace('[DONE]', '');
								copyToClipboard(unwrappedContent, 'html');
							} else if ( copyType === 'md' ) {
								if ( typeof TurndownService !== 'undefined' ) {
									const turndownService = new TurndownService();
									const content = getContentFrom.innerHTML.replace('[DONE]', '');
									copyToClipboard(turndownService.turndown(getContentFrom.innerHTML), 'md');
								}
							}
						});
					});
				}
			}
		}
	});

	function copyToClipboard(content, contentType = 'text') {
		if (typeof toastr === 'undefined') {
			return;
		}

		navigator.clipboard.writeText(content).then(() => {
			toastr.success(
				magicai_localize && magicai_localize[`${contentType}_content_copied_to_clipboard`] ?
					magicai_localize[`${contentType}_content_copied_to_clipboard`] :
					'Content copied to clipboard' );
		}).catch(err => {
			toastr.error( magicai_localize?.copy_failed || 'Failed to copy content' );
		});
	}

	function extractTextWithLinks(element) {
		let result = '';
		const walker = document.createTreeWalker(
			element,
			NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT,
			null,
			false
		);

		let node;
		while ((node = walker.nextNode())) {
			if (node.nodeType === Node.TEXT_NODE) {
				result += node.nodeValue;
			} else if (node.nodeType === Node.ELEMENT_NODE) {
				const nodeStyles = window.getComputedStyle(node);

				if (node.nodeName === 'A') {
					result += `(${node.href})`;
				} else if (node.nodeName === 'BR') {
					result += '\n\n';
				} else if (
					nodeStyles.display === 'block' ||
					nodeStyles.display === 'list-item'
				) {
					result += '\n';
				}
			}
		}
		return result.trim();
	}

	setAnchors.forEach(el => {
		const elRect = el.getBoundingClientRect();
		const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

		if (
			(!isRtl && elRect.right > window.innerWidth) ||
			(isRtl && elRect.left < 0)
		) {
			el.classList.add('anchor-end');
		}
	});
})();
