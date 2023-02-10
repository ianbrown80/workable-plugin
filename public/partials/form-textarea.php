<?php

/**
 * The HTML markup for the textareas in the form.
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

		<div class="workable-form--field-container workable-form--field-container--textarea <?php echo $required ? 'workable-form--required-field' : ''; ?> <?php echo $is_question ? 'workable-form--question' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--label workable-form--label-textarea"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<textarea id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--input workable-form--input-textarea" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> ></textarea>
		</div>
		</br>
