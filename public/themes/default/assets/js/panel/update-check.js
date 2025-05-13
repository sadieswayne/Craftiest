( () => {
	'use strict';

	$( document ).ready( function () {

		const cache = getCookie( 'magicai_update_check' );

		if ( localStorage.getItem( 'magicai_update_badge' ) ) {
			$( '.nav-link--update .lqd-nav-link-label' ).append( localStorage.getItem( 'magicai_update_badge' ) );
		} else {
			if ( !cache ) {
				$.ajax( {
					type: 'GET',
					url: '/updater/check',
					async: false,
					success: function ( response ) {
						if ( response != '' ) {
							let html = '<span class="text-xs rounded-full flex justify-center items-center bg-[#F3E2FD] text-black size-5 ms-auto">1</span>';
							$( '.nav-link--update .lqd-nav-link-label' ).append( html );
							localStorage.setItem( 'magicai_update_badge', html );
						}
						setCookie( 'magicai_update_check', 'yes', 1 );
					}
				} );
			}
		}

	} );
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
