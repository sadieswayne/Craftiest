( () => {
	"use strict";

	const body = document.body;
	const sections = document.querySelectorAll( '.site-section' );

	async function onPageLoad() {
		await document.fonts.ready;

		body.classList.add( 'page-loaded' );

		sectionsInview();
	}

	function sectionsInview() {
		sections.forEach( section => {
			new IntersectionObserver( ( [ entry ], observer ) => {
				if ( entry.isIntersecting ) {
					observer.disconnect();
					entry.target.classList.add( 'lqd-is-in-view' );
				}
			} ).observe( section );
		} );
	};

	onPageLoad();

} )();