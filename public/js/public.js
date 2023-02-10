(function( $ ) {
	'use strict';

	$( document ).ready(
		function() {

			// For the complex field, the field groups are hidden until the "Add" button is pressed
			$( '.workable-form--button-add' ).on(
				'click',
				function(event) {
					event.preventDefault();

					$( this ).siblings( '.workable-form--complex-field-row' ).show();
					$( this ).hide();

				}
			);

			// Each batch of complex fields needs to be saved.
			$( '.workable-form--button-save' ).on(
				'click',
				function(event) {
					event.preventDefault();

					// The field values are saved as as a JSON string in a hidden input field
					var fields        = {};
					var currentValues = $( this ).parents( 'fieldset' ).find( '.workable-form--complex-input-hidden' ).val() ? JSON.parse( $( this ).parents( 'fieldset' ).find( '.workable-form--complex-input-hidden' ).val() ) : [];

					// Go through each of the inputs in the fieldset and add the value to an array
					$( this ).siblings( '.workable-form--field-container' ).find( 'input, textarea' ).each(
						function() {
							if ($( this ).val()) {
								fields[$( this ).attr( 'name' )] = $( this ).val();
								$( this ).val( '' );
							}
						}
					);

					// Add the field array to the object that holds the previous entries.
					currentValues.push( fields );

					// Save it in the hidden field, then close the fieldset and empty the values ready to be use for the next set.
					$( this ).parents( 'fieldset' ).find( '.workable-form--complex-input-hidden' ).val( JSON.stringify( currentValues ) );
					$( this ).parents( '.workable-form--complex-field-row' ).hide();
					$( this ).parents( 'fieldset' ).find( '.workable-form--button-add' ).show();

				}
			);

			// When the user submits the form, check the required fields are entered correctly before sending it.
			$( '.workable-form--button-submit' ).on(
				'click',
				function(event) {
					event.preventDefault();

					var valid = true;
					var form  = $( this ).parents( 'form.workable-form' )[0];

					// Go through each element that is set to be required.
					$( form ).find( '.workable-form--required-field' ).each(
						function(){

							// Check the input, select and textarea elements first
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
							// Then check to radio and checkboxes
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
					// Check email inputs to make sure the are formatted correctly.
					var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

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

						// If all validation checks are done, submit the form with ajax.
						let formData = new FormData( form );

						// The questions need to be packaged a lttle differntly.
						// var questions = [];

						// $( '.workable-form--question' ).each(
						// 	function() {

						// 		$( this ).find( 'input, select, textarea' ).each(
						// 			function() {
						// 				var name = $( this ).attr( 'name' );
						// 				if (formData.has( name )) {
						// 					questions.push(
						// 						{
						// 							"question_key" : name,
						// 							"body" : $( this ).val()
						// 						}
						// 					)
						// 					formData.delete( name );
						// 				}
						// 			}
						// 		)
						// 	}
						// )

						formData.append( 'action', 'send_application_form' );
						// formData.append( 'answers', JSON.stringify( questions ) );

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

										// Display any message that is returned.
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
									//If the ajax fails, say why.
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
