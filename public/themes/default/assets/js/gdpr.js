( () => {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
		const gdpr = getCookie( 'magicai_gdpr' );
		const gdpr_selector = document.querySelector('#gdpr');

		if ( gdpr ) {
			gdpr_selector.classList.add('hidden');
		}

		document.querySelector('#gdpr button').addEventListener('click', function(){
			gdpr_selector.classList.add('hidden');
			setCookie('magicai_gdpr', 'accepted', 365);
		});

	});


} )();

function setCookie( cookieName, cookieValue, expirationDays ) {
	'use strict';
	const date = new Date();
	date.setTime( date.getTime() + ( expirationDays * 24 * 60 * 60 * 1000 ) ); // Convert days to milliseconds
	const expires = 'expires=' + date.toUTCString();
	document.cookie = cookieName + '=' + cookieValue + ';' + expires + ';path=/';
}

function getCookie( cookieName ) {
	'use strict';
	const name = cookieName + '=';
	const decodedCookie = decodeURIComponent( document.cookie );
	const cookieArray = decodedCookie.split( ';' );

	for ( let i = 0; i < cookieArray.length; i++ ) {
		let cookie = cookieArray[ i ];
		while ( cookie.charAt( 0 ) === ' ' ) {
			cookie = cookie.substring( 1 );
		}
		if ( cookie.indexOf( name ) === 0 ) {
			return cookie.substring( name.length, cookie.length );
		}
	}
	return '';
}
