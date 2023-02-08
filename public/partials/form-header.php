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
	<form id="<?php echo esc_attr( 'workable-form-' . $shortcode ); ?>" class="workable-form" action="#">
		<label for="<?php echo esc_attr( 'firstname-' . $shortcode ); ?>">First name</label>
		<input type="text" id="<?php echo esc_attr( 'firstname-' . $shortcode ); ?>" name="firstname" required />
		<br>
		<label for="<?php echo esc_attr( 'lastname-' . $shortcode ); ?>">Last name</label>
		<input type="text" id="<?php echo esc_attr( 'lastname-' . $shortcode ); ?>" name="lastname" required />
		<br>
		<label for="<?php echo esc_attr( 'email-' . $shortcode ); ?>">Email</label>
		<input type="text" id="<?php echo esc_attr( 'email-' . $shortcode ); ?>" name="email" required />
