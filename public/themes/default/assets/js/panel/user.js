//Admin
document.addEventListener("DOMContentLoaded", function() {
    "use strict";
    const list = new List('table-default', {
        sortClass: 'table-sort',
        listClass: 'table-tbody',
        valueNames: [
            'sort-name',
            'sort-group',
            'sort-remaining-words',
            'sort-remaining-images',
            'sort-country',
            'sort-status',
            'sort-quantity',
            'sort-score',
            { attr: 'data-date', name: 'sort-date' },
        ],
        // page: 25,
        // pagination: {
        //     innerWindow: 1,
        //     left: 0,
        //     right: 0,
        //     paginationClass: "pagination",
        // },
    });
})
function userSave( user_id ) {
	"use strict";

	document.getElementById( "user_edit_button" ).disabled = true;
	document.getElementById( "user_edit_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	formData.append( 'user_id', user_id );
	formData.append( 'name', $( "#name" ).val() );
	formData.append( 'surname', $( "#surname" ).val() );
	formData.append( 'phone', $( "#phone" ).val() );
	formData.append( 'email', $( "#email" ).val() );
	formData.append( 'country', $( "#country" ).val() );
	formData.append( 'type', $( "#type" ).val() );
	formData.append( 'status', $( "#status" ).val() );

    collectCreditsToFormData(formData);

	$.ajax( {
		type: "post",
		url: "/dashboard/admin/users/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.user_saved ||'User saved succesfully')
			document.getElementById( "user_edit_button" ).disabled = false;
			document.getElementById( "user_edit_button" ).innerHTML = "Save";
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "user_edit_button" ).disabled = false;
			document.getElementById( "user_edit_button" ).innerHTML = "Save";
		}
	} );
	return false;
}

//User
function userProfileSave() {
	"use strict";

	document.getElementById( "user_edit_button" ).disabled = true;
	document.getElementById( "user_edit_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	formData.append( 'name', $( "#name" ).val() );
	formData.append( 'surname', $( "#surname" ).val() );
	formData.append( 'phone', $( "#phone" ).val() );
	formData.append( 'country', $( "#country" ).val() );
	formData.append( 'city', $( "#city" ).val() );
	formData.append( 'state', $( "#state" ).val() );
	formData.append( 'postal', $( "#postal" ).val() );
	formData.append( 'address', $( "#address" ).val() );

    if ( $( '#old_password' ).val() != null ) {
        formData.append( 'old_password', $( "#old_password" ).val() );
        formData.append( 'new_password', $( "#new_password" ).val() );
        formData.append( 'new_password_confirmation', $( "#new_password_confirmation" ).val() );
    }

	if ( $( '#avatar' ).val() != 'undefined' ) {
		formData.append( 'avatar', $( '#avatar' ).prop( 'files' )[ 0 ] );
	}

	$.ajax( {
		type: "post",
		url: "/dashboard/user/settings/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.user_saved ||'User saved succesfully')
			document.getElementById( "user_edit_button" ).disabled = false;
			document.getElementById( "user_edit_button" ).innerHTML = "Save";
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "user_edit_button" ).disabled = false;
			document.getElementById( "user_edit_button" ).innerHTML = "Save";
		}
	} );
	return false;
}
