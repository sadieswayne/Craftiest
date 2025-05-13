function pageSave( page_id ) {
	"use strict";

	document.getElementById( "page_button" ).disabled = true;
	document.getElementById( "page_button" ).innerHTML = magicai_localize.please_wait;


	var formData = new FormData();

	if ( page_id != 'undefined' ) {
		formData.append( 'page_id', page_id );
	} else {
		formData.append( 'page_id', null );
	}
	formData.append( 'titlebar_status', $( "#titlebar_status" ).is( ":checked" ) ? 1 : 0 );
	formData.append( 'show_on_footer', $( "#show_on_footer" ).is( ":checked" ) ? 1 : 0 );
	formData.append( 'title', $( "#title" ).val() );
	formData.append( 'slug', $( "#slug" ).val() ? $( "#slug" ).val() : $( "#title" ).val() );
	formData.append( 'content', tinymce.activeEditor.getContent() );
	formData.append( 'status', $( "#status" ).is( ":checked" ) ? 1 : 0 );

	console.log( formData );

	$.ajax( {
		type: "post",
		url: "/dashboard/page/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success( 'Page Saved Succesfully' )
			document.getElementById( "page_button" ).disabled = false;
			document.getElementById( "page_button" ).innerHTML = "Save";
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "page_button" ).disabled = false;
			document.getElementById( "page_button" ).innerHTML = "Save";
		}
	} );
	return false;
}
