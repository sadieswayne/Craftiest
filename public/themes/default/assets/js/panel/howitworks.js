function howitWorksSave( howitWorks_id ) {
	"use strict";
    document.getElementById( "item_edit_button" ).disabled = true;
	document.getElementById( "item_edit_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	if ( howitWorks_id != 'undefined' ) {
		formData.append( 'howitWorks_id', howitWorks_id );
	} else {
		formData.append( 'howitWorks_id', null );
	}

	formData.append( 'order', $( "#order" ).val() );
	formData.append( 'title', $( "#title" ).val() );
	formData.append( 'description', $( "#description" ).val() );
	formData.append( 'bg_color', $( "#bg_color" ).val() );
	formData.append( 'bg_image', $( "#bg_image" )[0].files[0] );
	formData.append( 'text_color', $( "#text_color" ).val() );
	formData.append( 'image', $( "#image" )[0].files[0] );

	$.ajax( {
		type: "post",
		url: "/dashboard/admin/howitWorks/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.how_it_works_step_saved ||'How it Works Step Saved Succesfully. Redirecting...')
			setTimeout( function () {
				location.href = '/dashboard/admin/howitWorks'
			}, 1000 );
            // console.info("How it Works Step Saved Succesfully");
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


function howitWorksUpdate(from) {
	"use strict";

	var formData = new FormData();
	if(from == 0){
		if(document.getElementById("bottomlinecheck").getAttribute("checked") != null){
			formData.append( 'option', 0 );
		}else{
			formData.append( 'option', 1 );
		}
	}
	if(from == 1){
		formData.append( 'text', $( "#bottomlinetext" ).val() );
	}
	

	$.ajax( {
		type: "post",
		url: "/dashboard/admin/howitWorks/bottom-line",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.how_it_works_bottom_line_saved ||'How it Works Bottom Line updated successfully. Redirecting...')
			setTimeout( function () {
				location.href = '/dashboard/admin/howitWorks';
			}, 1000 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
                console.error(value);
			} );
		}
	} );
	return false;

}