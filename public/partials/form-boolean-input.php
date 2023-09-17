<?php

/**
 * The HTML markup for the boolean inputs in the form.
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

		<div class="workable-form--field-container workable-form--field-container--radio <?php echo $required ? 'workable-form--required-field' : ''; ?> <?php echo $is_question ? 'workable-form--question' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<p><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></p>
			<label for="<?php echo esc_attr( $name . '-' . $form_id . '-yes' ); ?>" class="workable-form--label workable-form--label-radio">
				Yes
				<input type="radio" class="workable-form--input workable-form--input-radio" id="<?php echo esc_attr( $name . '-' . $form_id . '-yes' ); ?>" name="<?php echo esc_attr( $name ); ?>" value="true" />
			</label>
			<label for="<?php echo esc_attr( $name . '-' . $form_id . '-no' ); ?>" class="workable-form--label workable-form--label-radio">
				No
				<input type="radio" class="workable-form--input workable-form--input-radio" id="<?php echo esc_attr( $name . '-' . $form_id . '-no' ); ?>" name="<?php echo esc_attr( $name ); ?>" value="false" />
			</label>
		</div>
		</br>
