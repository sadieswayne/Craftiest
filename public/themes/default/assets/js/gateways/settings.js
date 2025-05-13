

function settingsSave() {
	"use strict";

    document.getElementById( "settings_button" ).disabled = true;
	document.getElementById( "settings_button" ).innerHTML = magicai_localize.please_wait;

    var formData = new FormData(document.getElementById('settings_form'));

	$.ajax( {
		type: "post",
		url: "/dashboard/admin/finance/paymentGateways/settings/"+code+"/save",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.settings_saved ||'Settings saved successfully. Redirecting...' );
			setTimeout( function () {
				location.href = '/dashboard/admin/finance/paymentGateways';
			}, 1000 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
                console.error(value);
			} );
            document.getElementById( "settings_button" ).disabled = false;
            document.getElementById( "settings_button" ).innerHTML = "Save";
		}
	} );
	return false;

}