function clientSave( client_id ) {
	"use strict";
    document.getElementById( "item_edit_button" ).disabled = true;
	document.getElementById( "item_edit_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	if ( client_id != 'undefined' ) {
		formData.append( 'client_id', client_id );
	} else {
		formData.append( 'client_id', null );
	}

    if ( $( '#avatar' ).val() != 'undefined' ) {
		formData.append( 'avatar', $( '#avatar' ).prop( 'files' )[ 0 ] );
	}

	formData.append( 'alt', $( "#client_alt" ).val() );
	formData.append( 'title', $( "#client_title" ).val() );

	$.ajax( {
		type: "post",
		url: "/dashboard/admin/clients/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.client_saved ||'Client Saved Succesfully. Redirecting...')
			setTimeout( function () {
				location.href = '/dashboard/admin/clients'
			}, 1000 );
            // console.info("Client Saved Succesfully");
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
                console.error(value);
			} );
			document.getElementById( "item_edit_button" ).disabled = false;
			document.getElementById( "item_edit_button" ).innerHTML = "Save";
		}
	} );
	return false;
}



