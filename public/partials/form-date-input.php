<?php

/**
 * The HTML markup for the date inputs in the form.
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

		<div class="workable-form--field-container workable-form--field-container--text <?php echo $required ? 'workable-form--required-field' : ''; ?> <?php echo $is_question ? 'workable-form--question' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--label workable-form--label-text"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<input type="date" id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--input workable-form--input-text" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> />
		</div>
		</br>
