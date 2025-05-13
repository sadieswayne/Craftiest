function templateSave( template_id ) {
	"use strict";

	document.getElementById( "email_templates_button" ).disabled = true;
	document.getElementById( "email_templates_button" ).innerHTML = "Please Wait...";


	var formData = new FormData();

    if ( template_id != 'undefined' ) {
        formData.append( 'template_id', template_id );
	} else {
		formData.append( 'template_id', null );
	}
	formData.append( 'title', $( "#title" ).val() );
	formData.append( 'subject', $( "#subject" ).val() );

	if ( email_template_content ) {
		formData.append( 'content', email_template_content.getValue() );
	}

	$.ajax( {
		type: "post",
		url: "/dashboard/email-templates/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.template_saved ||'Template Saved Succesfully')
			document.getElementById( "email_templates_button" ).disabled = false;
			document.getElementById( "email_templates_button" ).innerHTML = "Save";
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "email_templates_button" ).disabled = false;
			document.getElementById( "email_templates_button" ).innerHTML = "Save";
		}
	} );
	return false;
}
