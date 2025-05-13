function chatbotSave( template_id ) {
	"use strict";

	document.getElementById( "chatbot_button" ).disabled = true;
	document.getElementById( "chatbot_button" ).innerHTML = "Please Wait...";


	var formData = new FormData();

    if ( template_id != 'undefined' ) {
        formData.append( 'template_id', template_id );
	} else {
		formData.append( 'template_id', null );
	}
	formData.append( 'title', $( "#title" ).val() );
	formData.append( 'role', $( "#role" ).val() );
	formData.append( 'model', $( "#model" ).val() );
	formData.append( 'first_message', $( "#first_message" ).val() );
	formData.append( 'instructions', $( "#instructions" ).val() );
	if ( $( '#image' ).val() != 'undefined' ) {
		formData.append( 'image', $( '#image' ).prop( 'files' )[ 0 ] );
	}
	formData.append( 'width', $( "#width" ).val() );
	formData.append( 'height', $( "#height" ).val() );

	$.ajax( {
		type: "post",
		url: "/dashboard/chatbot/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.saved ||'Saved Succesfully');
			document.getElementById( "chatbot_button" ).disabled = false;
			document.getElementById( "chatbot_button" ).innerHTML = "Save";
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "chatbot_button" ).disabled = false;
			document.getElementById( "chatbot_button" ).innerHTML = "Save";
		}
	} );
	return false;
}
