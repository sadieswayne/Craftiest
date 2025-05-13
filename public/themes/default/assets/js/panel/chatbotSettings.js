function chatbotSettingsSave( template_id ) {
	"use strict";

	document.getElementById( "settings_button" ).disabled = true;
	document.getElementById( "settings_button" ).innerHTML = "Please Wait...";


	var formData = new FormData();

	formData.append( 'status', $( "#status" ).val() );
	formData.append( 'template', $( "#template" ).val() );
	formData.append( 'position', $( "input[name='position']:checked" ).val() );
	formData.append( 'logged_in', $( "#logged_in" ).is(":checked") ? 1 : 0 );
	formData.append( 'rate_limit', $( "#rate_limit" ).val() );

	$.ajax( {
		type: "post",
		url: "/dashboard/chatbot/save-settings",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.saved ||'Saved Succesfully!');
            setTimeout( function () {
				location.href = '/dashboard/chatbot'
			}, 1000 );
			document.getElementById( "settings_button" ).disabled = false;
			document.getElementById( "settings_button" ).innerHTML = "Save";
		},
		error: function ( data ) {
            console.log(data);
            if ( data.status == 403 ) {
                toastr.info( data.responseJSON );
            } else {
                toastr.error( data.responseJSON );
            }
			document.getElementById( "settings_button" ).disabled = false;
			document.getElementById( "settings_button" ).innerHTML = "Save";
		}
	} );
	return false;
}