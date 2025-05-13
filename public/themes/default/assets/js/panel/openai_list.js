( () => {

	const filterButtons = [ ...document.querySelectorAll( '[data-filter-trigger]' ) ];
	const items = [ ...document.querySelectorAll( '[data-filter]' ) ];

	if ( !filterButtons.length || !items.length ) return;

	filterButtons.forEach( btn => {
		btn.addEventListener( 'click', event => {
			event.preventDefault();
			const filterVal = btn.getAttribute( 'data-filter-trigger' );
			let itemsToShow = [];
			let itemsToHide = [];
			if ( filterVal === 'all' ) {
				itemsToShow = items;
			} else {
				itemsToShow = items.filter( item => {
					const itemFilters = item.getAttribute( 'data-filter' ).split( ',' ).map( f => f.trim() );
					return itemFilters.includes( filterVal )
				} );
				itemsToHide = items.filter( item => {
					const itemFilters = item.getAttribute( 'data-filter' ).split( ',' ).map( f => f.trim() );
					return !itemFilters.includes( filterVal )
				} );
			}
			itemsToShow.forEach( item => item.style.display = 'block' );
			itemsToHide.forEach( item => item.style.display = 'none' );
			filterButtons.forEach( filterBtn => filterBtn.classList.remove( 'active' ) );
			btn.classList.add( 'active' );
		} );
	} )

} )();

function favoriteTemplate( id ) {
	"use strict";

	var formData = new FormData();
	formData.append( 'id', id );

	$.ajax( {
		type: "post",
		url: "/dashboard/user/openai/favorite",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			$( "#favorite_area_" + id ).html( data.html );
		},
	} );
	return false;
}
