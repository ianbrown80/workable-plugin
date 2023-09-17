<?php

/**
 * The HTML markup for the select inputs in the form.
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

		<div class="workable-form--field-container workable-form--field-container--select <?php echo $required ? 'workable-form--required-field' : ''; ?> <?php echo $is_question ? 'workable-form--question' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--label workable-form--label-select"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<select id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> <?php echo $single_answer ? '' : 'multiple'; ?> >
				<option value="" class="workable-form--input workable-form--input-select" disabled selected>Select an option...</option>
				<?php foreach ( $choices as $choice ) : ?>
					<option value="<?php echo esc_attr( $choice->id ); ?>" class="workable-form--input workable-form--input-select"><?php echo esc_html( $choice->body ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		</br>
