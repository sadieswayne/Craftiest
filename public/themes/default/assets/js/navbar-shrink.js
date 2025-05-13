( () => {
	const navbarIsShrinked = localStorage.getItem( 'lqd-navbar-shrinked' );
	if ( navbarIsShrinked === 'true' ) {
		document.body.classList.add( 'navbar-shrinked' );
	}
} )();