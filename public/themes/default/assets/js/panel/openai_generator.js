$( '#file' ).on( 'change', function () {
	"use strict";
	var isNull = false;
	if ( this.files[ 0 ].size > 24900000 ) {
		toastr.error(magicai_localize?.file_size_exceed ||'This file exceed the limit of file upload')
		isNull = true;
	}
	var ext = $( '#file' ).val().split( '.' ).pop().toLowerCase();
	if ( $.inArray( ext, [ 'mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm', 'ogg' ] ) == -1 ) {
		toastr.error(magicai_localize?.invalid_extension ||'Invalid extension. Accepted extensions are ogg, mp3, mp4, mpeg, mpga, m4a, wav, and webm')
		isNull = true;
	}
	if ( isNull ) {
		document.getElementById( "file" ).value = null;
	}
} );
// @formatter:off
document.addEventListener( "DOMContentLoaded", function () {
	"use strict";

	var el = document.getElementById( 'language' );

	if (el) {
		window.TomSelect && ( new TomSelect( el, {
			copyClassesToDropdown: false,
			dropdownClass: 'dropdown-menu ts-dropdown',
			optionClass: 'dropdown-item',
			controlInput: '<input>',
			render: {
				item: function ( data, escape ) {
					if ( data.customProperties ) {
						return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape( data.text ) + '</div>';
					}
					return '<div>' + escape( data.text ) + '</div>';
				},
				option: function ( data, escape ) {
					if ( data.customProperties ) {
						return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape( data.text ) + '</div>';
					}
					return '<div>' + escape( data.text ) + '</div>';
				},
			},
		} ) );
	}

} );
// @formatter:on

function fillAnExample(selector){
    "use strict";

    const prompts = [
        'Cityscape at sunset in retro vector illustration',
        'Painting of a flower vase on a kitchen table with a window in the backdrop.',
        'Memphis style painting of a flower vase on a kitchen table with a window in the backdrop.',
        'Illustration of a cat sitting on a couch in a living room with a coffee mug in its hand.',
        'Delicious pizza with all the toppings.',
        'a super detailed infographic of a working time machine 8k',
        'hedgehog smelling a flower',
        'Freeform ferrofluids, beautiful dark chaos',
        'a home built in a huge Soap bubble, windows',
        'photo of an extremely cute alien fish swimming an alien habitable underwater planet'
    ];

    var item = prompts[Math.floor(Math.random()*prompts.length)];

    $("." + selector).val(item);

    return false;
}
