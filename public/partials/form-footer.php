<?php

/**
 * The HTML markup for the footer of the form.
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
		<?php wp_nonce_field( 'submit_workable_form', 'workable_form_nonce', true, true ); ?>
		<input type="hidden" name="shortcode" value="<?php echo esc_attr( $shortcode ); ?>" />
		<button type="submit" id="<?php echo esc_attr( 'submit-' . $shortcode ); ?>" class="workable-form-submit">Submit</button>
	</form>
</div>

