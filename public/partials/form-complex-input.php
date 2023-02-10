<?php

/**
 * The HTML markup for the complex inputs in the form.
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

		<fieldset id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--field-container workable-form--field-container--fieldset <?php echo $required ? 'workable-form--required-field' : ''; ?> <?php echo $is_question ? 'workable-form--question' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<legend class="workable-form--label workable-form--label-fieldset"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?> </legend>
			<button type="button" class="workable-form--button workable-form--button-add">Add</button>
			<div class="workable-form--complex-field-row">

			<?php
			// Go througheach field and render it.
			foreach ( $fields as $field ) {
				switch ( $field->type ) {
					case 'string':
						$this->render_text( $form_id, $field->key, $field->label, $field->required, false, isset( $field->max_length ) ? $field->max_length : '' );
						break;
					case 'free_text':
						$this->render_free_text( $form_id, $field->key, $field->label, $field->required, false );
						break;
					case 'file':
						$this->render_file( $form_id, $field->key, $field->label, $field->required, $field->supported_file_types, $field->max_file_size, false );
						break;
					case 'boolean':
						$this->render_boolean( $form_id, $field->key, $field->label, $field->required, false );
						break;
					case 'date':
						$this->render_date( $form_id, $field->key, $field->label, $field->required, false );
						break;
				}
			}
			?>

			<button type="button" class="workable-form--button workable-form--button-save">Save</button>
			</div>
			<input type="hidden" class="workable-form--complex-input-hidden" id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> value="" />
		</fieldset>
		</br>
