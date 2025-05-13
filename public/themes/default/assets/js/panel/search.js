(() => {
	'use strict';

	document.querySelectorAll('.header-search').forEach(headerSearch => {

		const searchInput = headerSearch.querySelector('.header-search-input');
		const searchShortcutKey = headerSearch.querySelector('.search-shortcut-key');

		if (!searchInput) return;

		let inputFocused = false;
		var timer = null;

		if (searchShortcutKey) {
			if (navigator.userAgent.indexOf('Mac OS X') != -1) {
				searchShortcutKey.innerText = 'cmd';
			} else {
				searchShortcutKey.innerText = 'ctrl';
			}
			searchShortcutKey.parentElement.classList.remove('opacity-0');
		}

		searchInput.addEventListener('focus', function () {
			if (!onlySpaces(searchInput.value)) {
				headerSearch.classList.add('done-searching');
			}
		});

		searchInput.addEventListener('keyup', function () {
			if (onlySpaces(searchInput.value)) {
				clearTimeout(timer);
				headerSearch.classList.remove('is-searching');
				headerSearch.classList.remove('done-searching');
			} else {
				headerSearch.classList.add('is-searching');
				clearTimeout(timer);
				timer = setTimeout(() => searchFunction(headerSearch), 1000);
			}
		});

		window.addEventListener('keydown', function (e) {
			if ((e.ctrlKey || e.shiftKey || e.altKey || e.metaKey) && e.key === 'k') {
				e.preventDefault();
				e.stopPropagation();
				if (inputFocused) return;
				searchInput.focus();
				inputFocused = true;
				if (!onlySpaces(searchInput.value)) {
					headerSearch.classList.add('done-searching');
				}
			}
			if (e.key === 'Escape') {
				if (!inputFocused) return;
				searchInput.blur();
				inputFocused = false;
				headerSearch.classList.remove('done-searching');
			}
		});

		searchInput.addEventListener('blur', () => {
			inputFocused = false;
		});

		document.addEventListener('click', ev => {
			const { target } = ev;
			const clickedOutside = !headerSearch?.contains(target);
			if (clickedOutside) {
				headerSearch.classList.remove('is-searching');
				headerSearch.classList.remove('done-searching');
			}
		});
	});

	function onlySpaces(str) {
		'use strict';

		return str.trim().length === 0 || str === '';
	}

	function searchFunction(headerSearch) {
		'use strict';

		const formData = new FormData();
		const searchInput = headerSearch.querySelector('.header-search-input');

		formData.append('_token', document.querySelector('input[name=_token]')?.value);
		formData.append('search', searchInput.value);

		$.ajax({
			type: 'POST',
			url: '/dashboard/api/search',
			data: formData,
			contentType: false,
			processData: false,
			success: function (result) {
				headerSearch.querySelector('.search-results-container').innerHTML = result.html;
				headerSearch.classList.add('done-searching');
				headerSearch.classList.remove('is-searching');
			}
		});
	}
})();


