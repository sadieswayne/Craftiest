const stripe = Stripe( '{{$setting->stripe_key}}' );

const elements = stripe.elements();

const cardElement = elements.create( 'card' );

cardElement.mount( '#card-element' );

const form = document.getElementById( 'payment-form' );
const cardBtn = document.getElementById( 'card-button' );
const cardHolderName = document.getElementById( 'card-holder-name' );

form.addEventListener( 'submit', async ( e ) => {
	"use strict";

	e.preventDefault()
	cardBtn.disabled = true
	cardBtn.innerHTML = 'Please Wait, Processing...'

	const { setupIntent, error } = await stripe.confirmCardSetup(
		cardBtn.dataset.secret, {
		payment_method: {
			card: cardElement,
			billing_details: {
				name: cardHolderName.value
			}
		}
	}
	)

	if ( error ) {
		cardBtn.disabled = false
		cardBtn.innerHTML = 'Pay with Stripe'

		toastr.error( error.message );
	} else {
		let token = document.createElement( 'input' )
		token.setAttribute( 'type', 'hidden' )
		token.setAttribute( 'name', 'token' )
		token.setAttribute( 'value', setupIntent.payment_method )
		paymentMethod = setupIntent.payment_method
		$( '.payment-method' ).val( paymentMethod )
		form.appendChild( token )
		form.submit();
	}
} )
