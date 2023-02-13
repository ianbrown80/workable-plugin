<?php

/**
 * The HTML markup for the header of the form.
 *
 * @since      1.0.0
 *
 * @package    Workable
 * @subpackage Workable/public/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

?>

<div class="workable-form-container">
	<form id="<?php echo esc_attr( 'workable-form-' . $shortcode ); ?>" class="workable-form" action="#" <?php echo $success_url ? 'data-success="' . esc_attr( $success_url ) . '"' : ''; ?> >
		<div class="workable-form--field-container workable-form--field-container--text workable-form--required-field" >
			<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>
			<label for="<?php echo esc_attr( 'firstname-' . $shortcode ); ?>" class="workable-form--label workable-form--label-text"><span class="workable-form--required">*</span>First name</label>
			<input type="text" id="<?php echo esc_attr( 'firstname-' . $shortcode ); ?>" name="firstname" class="workable-form--input workable-form--input-text" required />
		</div>
		<br>
		<div class="workable-form--field-container workable-form--field-container--text workable-form--required-field" >
			<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>	
			<label for="<?php echo esc_attr( 'lastname-' . $shortcode ); ?>" class="workable-form--label workable-form--label-text"><span class="workable-form--required">*</span>Last name</label>
			<input type="text" id="<?php echo esc_attr( 'lastname-' . $shortcode ); ?>" name="lastname" class="workable-form--input workable-form--input-text" required />
		</div>
		<br>
		<div class="workable-form--field-container workable-form--field-container--text workable-form--required-field" >
			<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>	
			<p class="workable-form--validation workable-form--validation-email">Please enter a valid email address</p>
			<label for="<?php echo esc_attr( 'email-' . $shortcode ); ?>" class="workable-form--label workable-form--label-text"><span class="workable-form--required">*</span>Email</label>
			<input type="email" id="<?php echo esc_attr( 'email-' . $shortcode ); ?>" name="email" class="workable-form--input workable-form--input-text" required />
		</div>
		<br>
