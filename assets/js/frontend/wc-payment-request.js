/*!
 * WooCommerce Payment Request API
 */
jQuery( function( $ ) {
	/* global wc_payment_request_params */
	if ( typeof wc_payment_request_params === 'undefined' ) {
		return false;
	}

	$( document ).on( 'click', '.wc-proceed-to-checkout .checkout-button', function( e ) {
		if ( ! window.PaymentRequest ) {
			return true;
		}

		e.preventDefault();

		var supportedInstruments = [ {
			supportedMethods: [
				'visa', 'mastercard', 'amex', 'discover', 'maestro', 'diners', 'jcb', 'unionpay', 'bitcoin'
			]
		} ];

		var details = {};
		details.displayItems = wc_payment_request_params.display_items;
		details.total = wc_payment_request_params.total;

		var options = {
			requestShipping: true,
			requestPayerEmail: true,
			requestPayerPhone: true
		};

		new PaymentRequest( supportedInstruments, details, options )
			.show()
			.then( function( paymentResponse ) {
				// Process paymentResponse here
				//paymentResponse.complete("success");
			})
			.catch(function(err) {
				//console.error("Uh oh, something bad happened", err.message);
			});
	});
} );
