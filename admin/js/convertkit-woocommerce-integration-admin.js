(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$( document ).ready(
		function() {

			// To rewrite URL when showing success message for Successful ConverKit Integration.

			var href = window.location.href;

			if ( href.indexOf( '&mwb_success_cwi=true' ) >= 0 ) {

				var newUrl = href.substring( 0, href.indexOf( '&' ) );

				window.history.replaceState( {}, '', newUrl );

			}

			// On click Integrate ConvertKit.
			$( '#convertkit-woocommerce-integration-integrate' ).on(
				'click',
				function(e) {

					e.preventDefault();

					$( '#convertkit-woocommerce-integration-integrate-loading' ).css( 'display', 'inline-block' );

					$.ajax(
						{

							type:'POST',
							dataType: 'json',
							url: ajax_object.ajaxurl,

							data: {
								'action': 'convertkit_woocommerce_integration_integration',
								'convertkit-woocommerce-integration-ajax-nonce': ajax_object.ajax_nonce,
							},

							success:function( data ) {

								if ( 'true' === data.custom_fields_status && 'true' === data.tag_created_status ) {

									window.location = ajax_object.reloadurl + '&mwb_success_cwi=true';

									return;
								}

								$( '#convertkit-woocommerce-integration-integrate-loading' ).css( 'display', 'none' );

								if ( 'true' === data.custom_fields_status ) {

									$( '#convertkit-woocommerce-integration-custom-field' ).addClass( 'notice-success' );
								} else {

									$( '#convertkit-woocommerce-integration-custom-field' ).addClass( 'notice-error' );

								}

								$( '#convertkit-woocommerce-integration-custom-field p' ).html( data.custom_fields_message );

								$( '#convertkit-woocommerce-integration-custom-field' ).css( 'display', 'block' );

								if ( 'true' === data.tag_created_status ) {

									$( '#convertkit-woocommerce-integration-tag' ).addClass( 'notice-success' );
								} else {

									$( '#convertkit-woocommerce-integration-tag' ).addClass( 'notice-error' );
								}

								$( '#convertkit-woocommerce-integration-tag p' ).html( data.tag_created_message );

								$( '#convertkit-woocommerce-integration-tag' ).css( 'display', 'block' );
							}
						}
					);
				}
			);

		}
	);

})( jQuery );
