(function( $ ) {
	'use strict';

	$( document ).ready(
		function() {

			$( '.workable-form-submit' ).on(
				'click',
				function(event) {
					event.preventDefault();

					let $form = $( this ).parents( 'form.workable-form' )[0];
					console.log( $form );
					let formData = new FormData( $form );

					formData.append( 'action', 'send_application_form' );
					$.ajax(
						{
							type : "post",
							url : workable_scripts.ajaxurl,
							data : formData,
							cache: false,
							processData: false,
							contentType: false,
							success: function(response) {
								console.log( response );
							},
							error: function(error){
								console.log( error );
							}
						}
					);

				}
			)
		}
	)

})( jQuery );
