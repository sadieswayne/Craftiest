( () => {
	"use strict";

	const copyBtn = document.querySelector( '.copy-aff-link' );
	copyBtn?.addEventListener( 'click', ev => {
		const codeInput = document.querySelector( '#ref-code' );
		navigator.clipboard.writeText( codeInput.value );
		toastr.success( 'Affiliate link coppied to clipboard.' );
	} )
} )();

function filterAffiliates() {
    var input = document.getElementById('searchInput').value.toLowerCase();
    var startDate = document.getElementById('startDate').value;
    var endDate = document.getElementById('endDate').value;

    $.ajax({
        type: 'GET',
        url: '/dashboard/user/affiliates/users',
        data: {
            search: input,
            startDate: startDate,
            endDate: endDate
        },
        success: function(data) {
            $('#userTable tbody').html($(data).find('tbody').html());
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

function sendRequestForm() {
	"use strict";

	document.getElementById( "send_request_button" ).disabled = true;
	document.getElementById( "send_request_button" ).innerHTML = magicai_localize.please_wait;


	var formData = new FormData();
	formData.append( 'affiliate_bank_account', $( "#affiliate_bank_account" ).val() );
	formData.append( 'amount', $( "#amount" ).val() );

	$.ajax( {
		type: "post",
		url: "/dashboard/user/affiliates/send-request",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.request_sent ||'Request Sent Succesfully');
			setTimeout( function () {
				location.reload();
			}, 900 );
		},
		error: function ( data ) {
			toastr.error(magicai_localize?.you_cannot_withdrawal ||'You cannot withdrawal with this amount. Please check')
			document.getElementById( "send_request_button" ).disabled = false;
			document.getElementById( "send_request_button" ).innerHTML = "Send Request";
		}
	} );
	return false;
};

function sendInvitationForm() {
    "use strict";

    document.getElementById( "send_invitation_button" ).disabled = true;
    document.getElementById( "send_invitation_button" ).innerHTML = magicai_localize.please_wait;


    var formData = new FormData();
    formData.append( 'to_mail', $( "#to_mail" ).val() );

    $.ajax( {
        type: "post",
        url: "/dashboard/user/affiliates/send-invitation",
        data: formData,
        contentType: false,
        processData: false,
        success: function ( data ) {
            toastr.success(magicai_localize?.invitation_sent ||'Invitation Sent Succesfully!');
            document.getElementById( "send_invitation_button" ).disabled = false;
            document.getElementById( "send_invitation_button" ).innerHTML = "Send Invitation";
        },
        error: function ( data ) {
            toastr.error(magicai_localize?.error_while_sending ||'Error while sending information. Please contact us.')
            document.getElementById( "send_invitation_button" ).disabled = false;
            document.getElementById( "send_invitation_button" ).innerHTML = "Send Invitation";
        }
    } );
    return false;
};


