function sendSupportForm() {
	'use strict';

	const submitBtn = document.getElementById( 'support_button' );
	submitBtn.disabled = true;
	submitBtn.classList.add( 'submitting' );

	var formData = new FormData();
	formData.append( 'category', $( '#category' ).val() );
	formData.append( 'priority', $( '#priority' ).val() );
	formData.append( 'subject', $( '#subject' ).val() );
	formData.append( 'message', $( '#message' ).val() );

	$.ajax( {
		type: 'post',
		url: '/dashboard/support/new-support-request/send',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.support_ticket_created ||'Support Ticket Created Succesfully. Redirecting...');
			setTimeout( function () {
				location.href = '/dashboard/support/my-requests';
			}, 1500 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			submitBtn.disabled = false;
			submitBtn.classList.remove( 'submitting' );
		}
	} );
	return false;
}

(() => {
	'use strict';

	const form = document.querySelector('#support_form');
	const messageField = form?.querySelector('#message');
	const chatsContainer = document.querySelector('.chats-container');

	// send message when user hits enter
	messageField && messageField.addEventListener('keydown', function (ev) {
		if (ev.key === 'Enter' && !ev.shiftKey) {
			ev.preventDefault();
			ev.target.form.requestSubmit();
		}
	});

	// scroll chats container to bottom
	chatsContainer && (chatsContainer.scrollTop = chatsContainer.scrollHeight);
})();

function sendMessage( ticket_id ) {
	'use strict';

	const submitBtn = document.getElementById( 'send_message_button' );
	submitBtn.disabled = true;
	submitBtn.classList.add( 'submitting' );

	var formData = new FormData();
	formData.append( 'message', $( '#message' ).val() );
	formData.append( 'ticket_id', ticket_id );

	$.ajax( {
		type: 'post',
		url: '/dashboard/support/requests-action/send-message',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success(magicai_localize?.message_sent ||'Message sent succesfully. Please Wait');
			setTimeout( function () {
				location.reload();
			}, 1500 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			submitBtn.disabled = false;
			submitBtn.classList.remove( 'submitting' );
		}
	} );
	return false;
}
