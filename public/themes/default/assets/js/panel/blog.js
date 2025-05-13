function blogSave( post_id ) {
	"use strict";

	document.getElementById( "post_button" ).disabled = true;
	document.getElementById( "post_button" ).innerHTML = "Please Wait...";


	var formData = new FormData();

    if ( post_id != 'undefined' ) {
        formData.append( 'post_id', post_id );
	} else {
		formData.append( 'post_id', null );
	}
	formData.append( 'title', $( "#title" ).val() );
	formData.append( 'content', tinymce.activeEditor.getContent() );
	if ( $( '#feature_image' ).val() != 'undefined' ) {
		formData.append( 'feature_image', $( '#feature_image' ).prop( 'files' )[ 0 ] );
	}
	formData.append( 'slug', $( "#slug" ).val() ? $( "#slug" ).val() : $( "#title" ).val() );
	formData.append( 'seo_title', $( "#seo_title" ).val() );
	formData.append( 'seo_description', $( "#seo_description" ).val() );
	formData.append( 'category', $( "#category" ).val() );
	formData.append( 'tag', $( "#tag" ).val() );
    formData.append( 'status', $( "#status" ).val() );

	$.ajax( {
		type: "post",
		url: "/dashboard/blog/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.page_saved ||'Page Saved Succesfully')
			document.getElementById( "post_button" ).disabled = false;
			document.getElementById( "post_button" ).innerHTML = "Save";
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "post_button" ).disabled = false;
			document.getElementById( "post_button" ).innerHTML = "Save";
		}
	} );
	return false;
}

$(document).ready(function() {
	"use strict";
	var fileInput = $('#feature_image');
	var previewImage = $('.preview');

	fileInput.on('change', function() {
	  var file = this.files[0];
	  var reader = new FileReader();

	  if (file) {
		reader.readAsDataURL(file);
		reader.onload = function() {
		  previewImage.attr('src', reader.result);
		  previewImage.show();
		};
	  } else {
		previewImage.hide();
	  }
	});
});