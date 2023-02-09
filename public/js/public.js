(function( $ ) {
	'use strict';

	$( document ).ready(
		function() {

			$( '.workable-form--button-add' ).on(
				'click',
				function(event) {
					event.preventDefault();

					$( this ).siblings( '.workable-form--complex-field-row' ).show();
					$( this ).hide();

				}
			);

			$( '.workable-form--button-save' ).on(
				'click',
				function(event) {
					event.preventDefault();

					var fields        = {};
					var currentValues = $( this ).parents( 'fieldset' ).find( '.workable-form--complex-input-hidden' ).val() ? JSON.parse( $( this ).parents( 'fieldset' ).find( '.workable-form--complex-input-hidden' ).val() ) : [];

					$( this ).siblings( '.workable-form--field-container' ).find( 'input' ).each(
						function() {
							if ($( this ).val()) {
								fields[$( this ).attr( 'name' )] = $( this ).val();
								$( this ).val( '' );
							}
						}
					);

					currentValues.push( fields );

					$( this ).parents( 'fieldset' ).find( '.workable-form--complex-input-hidden' ).val( JSON.stringify( currentValues ) );
					$( this ).parents( '.workable-form--complex-field-row' ).hide();
					$( this ).parents( 'fieldset' ).find( '.workable-form--button-add' ).show();

				}
			);

			$( '.workable-form--button-submit' ).on(
				'click',
				function(event) {
					event.preventDefault();

					var valid      = true;
					var emailRegex = / ^ [a - zA - Z0 - 9. ! #$ % & ' * + /= ? ^ _`{ | }~ - ] + @[a - zA - Z0 - 9 - ] + ( ?: \.[a - zA - Z0 - 9 - ] + ) * $ / ;

					let form = $( this ).parents( 'form.workable-form' )[0];

					$( form ).find( '.workable-form--required-field' ).each(
						function(){

							$( this ).find( '[required]' ).each(
								function() {
									if ( ! $( this ).val() ) {
										valid = false;
										$( this ).parents( '.workable-form--required-field' ).find( '.workable-form--validation-empty' ).show();
									} else {
										$( this ).parents( '.workable-form--required-field' ).find( '.workable-form--validation-empty' ).hide();
									}
								}
							);

							var checkValid = false;
							var checkCount = 0;

							$( this ).find( 'input[type=radio], input[type=checkbox]' ).each(
								function() {
									checkCount++;
									if ( $( this ).prop( 'checked' ) == true ) {
										checkValid = true;
									}
								}
							);

							if ( checkCount > 0 ) {
								if ( ! checkValid ) {
									valid = false;
									$( this ).find( '.workable-form--validation-empty' ).show();
								} else {
									$( this ).find( '.workable-form--validation-empty' ).hide();
								}
							}
						}
					);

					$( form ).find( 'input[type=email]' ).each(
						function(){
							if ( ! $( this ).val().match( emailRegex ) ) {
								valid = false;
								$( this ).parents( '.workable-form--required-field' ).find( '.workable-form--validation-email' ).show();
							} else {
								$( this ).parents( '.workable-form--required-field' ).find( '.workable-form--validation-email' ).hide();
							}
						}
					);

					if (valid) {

						let formData = new FormData( form );

						formData.append( 'action', 'send_application_form' );
						$.ajax(
							{
								type : "post",
								url : workable_scripts.ajaxurl,
								data : formData,
								cache: false,
								processData: false,
								contentType: false,
								success: function( response ) {
									var result = JSON.parse( response );
									if ( result.success ) {
										$( '.workable-form--validation-submit-success' ).html( result.success );
										$( '.workable-form--validation-submit-success' ).show();
										$( '.workable-form--validation-submit-fail' ).hide();
									} else {
										$( '.workable-form--validation-submit-fail' ).html( result.error ? result.error : 'Unfortunatly we were unable to submit your application. Please try again later' );
										$( '.workable-form--validation-submit-success' ).hide();
										$( '.workable-form--validation-submit-fail' ).show();
									}
								},
								error: function( error ){
									$( '.workable-form--validation-submit-fail' ).html( 'Unfortunatly we were unable to submit your application. Please try again later' );
									$( '.workable-form--validation-submit-success' ).hide();
									$( '.workable-form--validation-submit-fail' ).show();
								}
							}
						);
					}

				}
			)
		}
	)

})( jQuery );
