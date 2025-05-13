function testimonialSave( testimonial_id ) {
	"use strict";
    document.getElementById( "item_edit_button" ).disabled = true;
	document.getElementById( "item_edit_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	if ( testimonial_id != 'undefined' ) {
		formData.append( 'testimonial_id', testimonial_id );
	} else {
		formData.append( 'testimonial_id', null );
	}

    if ( $( '#avatar' ).val() != 'undefined' ) {
		formData.append( 'avatar', $( '#avatar' ).prop( 'files' )[ 0 ] );
	}

	formData.append( 'full_name', $( "#full_name" ).val() );
	formData.append( 'job_title', $( "#job_title" ).val() );
	formData.append( 'words', $( "#words" ).val() );

	$.ajax( {
		type: "post",
		url: "/dashboard/admin/testimonials/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.testimonial_saved ||'Testimonial Saved Succesfully. Redirecting...')
			setTimeout( function () {
				location.href = '/dashboard/admin/testimonials'
			}, 1000 );
            console.info("Testimonial Saved Succesfully");
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



