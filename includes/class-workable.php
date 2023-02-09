<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Workable
 * @subpackage Workable/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Workable
 * @subpackage Workable/includes
 * @author     Ian Brown <brown.i18@sky.com>
 */
class Workable {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Workable_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The API manager that is responsible for making and recieving requests
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Workable_API    $api    Manages the API requests.
	 */
	protected $api;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'VERSION' ) ) {
			$this->version = VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'workable';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Create the shortcode to display the form.
		add_shortcode( 'workable_form', array( $this, 'get_workable_form' ) );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Workable_Loader. Orchestrates the hooks of the plugin.
	 * - Workable_I18n. Defines internationalization functionality.
	 * - Workable_Admin. Defines all hooks for the admin area.
	 * - Workable_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-workable-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-workable-i18n.php';

		/**
		 * The class responsible for managing the codeable API connection
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-workable-api.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-workable-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-workable-public.php';

		// Initialise the classes needed.
		$this->loader = new Workable_Loader();
		$this->api    = new Workable_API();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Workable_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Workable_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Workable_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Workable_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// These are the Ajax hooks needed to send the form to Workable.
		$this->loader->add_action( 'wp_ajax_send_application_form', $this->api, 'send_application_form' );
		$this->loader->add_action( 'wp_ajax_nopriv_send_application_form', $this->api, 'send_application_form' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Workable_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Display the form from Workable.
	 *
	 * This is the function that is run when the form shortcode is used
	 *
	 * @param array $attr the attributes passed into the shortcode.
	 * @since     1.0.0
	 */
	public function get_workable_form( $attr ) {

		$error = false;

		// Set a default form shortcode value.
		$default = array(
			'shortcode' => '',
		);
		// Set the attributes passed in.
		$attributes = shortcode_atts( $default, $attr );
		$shortcode  = $attributes['shortcode'];

		if ( isset( $shortcode ) && ! empty( $shortcode ) ) {

			// Get the application form from the API.
			$form = $this->api->get_application_form( $shortcode );

			// If the form doesn't generate an error, display it.
			if ( ! is_wp_error( $form ) ) {

				ob_start();

				// Show the top of the form.
				$this->render_form_header( $shortcode );

				// Show the form fields.
				if ( isset( $form->form_fields ) && ! empty( $form->form_fields ) ) {

					$this->render_form_fields( $shortcode, $form->form_fields );

				}

				// Show the form questions.
				if ( isset( $form->questions ) && ! empty( $form->questions ) ) {

					$this->render_form_questions( $shortcode, $form->questions );

				}

				// Show the bottom of the form.
				$this->render_form_footer( $shortcode );

				ob_end_flush();

			} else {
				// If the API has returned an error, save it to display later.
				$error = $form;
			}
		} else {
			// Set an error if the form shortcode is empty.
			$error = new WP_Error( 'missing_shortcode', 'The form shortcode is missing' );
		}

		// If there has been an error, display it.
		if ( $error ) {
			echo '<p>' . esc_html( $error->get_error_message() ) . '</p>';
		}
	}

	/**
	 * Display the form header HTML.
	 * @since     1.0.0
	 */
	private function render_form_header( $shortcode ) {

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/form-header.php';
	}

	/**
	 * Display the form fields HTML.
	 * @param string $form_id Unique id for the form.
	 * @param array $fields The fields in the form.
	 * @since     1.0.0
	 */
	private function render_form_fields( $form_id, $fields ) {

		// Go though each of the "field" properties and display the HTML for them.
		foreach ( $fields as $field ) {

			switch ( $field->type ) {
				case 'string':
					$this->render_text( $form_id, $field->key, $field->label, $field->required, isset( $field->max_length ) ? $field->max_length : '' );
					break;
				case 'free_text':
					$this->render_free_text( $form_id, $field->key, $field->label, $field->required );
					break;
				case 'file':
					$this->render_file( $form_id, $field->key, $field->label, $field->required, $field->supported_file_types, $field->max_file_size );
					break;
				case 'boolean':
					$this->render_boolean( $form_id, $field->key, $field->label, $field->required );
					break;
				case 'date':
					$this->render_date( $form_id, $field->key, $field->label, $field->required );
					break;
				case 'complex':
					$this->render_complex( $form_id, $field->key, $field->label, $field->required, $field->multiple, $field->fields );
					break;
			}
		}
	}

	/**
	 * Display the form questions HTML.
	 * @param string $form_id Unique id for the form.
	 * @param array $questions The questions in the form.
	 * @since     1.0.0
	 */
	private function render_form_questions( $form_id, $questions ) {

		// Go though each of the "question" properties and display the HTML for them.
		foreach ( $questions as $question ) {

			switch ( $question->type ) {
				case 'multiple_choice':
					$this->render_multiple_choice( $form_id, $question->id, $question->body, $question->required, $question->single_answer, $question->choices );
					break;
				case 'free_text':
					$this->render_free_text( $form_id, $question->id, $question->body, $question->required );
					break;
				case 'file':
					$this->render_file( $form_id, $question->id, $question->body, $question->required, $question->supported_file_types, $question->max_file_size );
					break;
				case 'boolean':
					$this->render_boolean( $form_id, $question->id, $question->body, $question->required );
					break;
				case 'date':
					$this->render_date( $form_id, $question->id, $question->body, $question->required );
					break;
				case 'dropdown':
					$this->render_dropdown( $form_id, $question->id, $question->body, $question->required, $question->single_answer, $question->choices );
					break;
				case 'numeric':
					$this->render_numeric( $form_id, $question->id, $question->body, $question->required );
					break;
			}
		}
	}

	/**
	 * Display the form footer HTML.
	 * @since     1.0.0
	 */
	private function render_form_footer( $shortcode ) {

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/form-footer.php';
	}

	/**
	 * Display the text input HTML.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 * @param int $maxlength The maximum input size.
	 *
	 * @since     1.0.0
	 */
	private function render_text( $form_id, $name, $label, $required, $maxlength = '' ) {
		?>
		<div class="workable-form--field-container workable-form--field-container--text <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--label workable-form--label-text"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<input type="text" class="workable-form--input workable-form--input-text" id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> <?php echo $maxlength ? 'maxlength="' . esc_attr( $maxlength ) . '"' : ''; ?> />
		</div>
		</br>
		<?php
	}

	/**
	 * Display the textarea HTML.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 *
	 * @since     1.0.0
	 */
	private function render_free_text( $form_id, $name, $label, $required ) {
		?>
		<div class="workable-form--field-container workable-form--field-container--textarea <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--label workable-form--label-textarea"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<textarea id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--input workable-form--input-textarea" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> ></textarea>
		</div>
		</br>
		<?php
	}

	/**
	 * Display the file input HTML.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 * @param array $file_types The supported file types.
	 * @param int $max_file The maximum file size.
	 *
	 * @since     1.0.0
	 */
	private function render_file( $form_id, $name, $label, $required, $file_types, $max_file ) {

		// Get a string of the supported file types 
		if ( isset( $file_types ) && ! empty( $file_types ) ) {
			foreach ( $file_types as $type_key => $file_type ) {
				$file_types[ $type_key ] = '.' . $file_type;
			}
			$file_types_string = implode( ', ', $file_types );
		}

		?>
		<div class="workable-form--field-container workable-form--field-container--file <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>"class="workable-form--label workable-form--label-file"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<input type="file" class="workable-form--input workable-form--input-file" id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> <?php echo $file_types_string ? 'accept="' . esc_attr( $file_types_string ) . '"' : ''; ?> <?php echo $max_file ? 'data-max-size="' . esc_attr( $max_file ) . '"' : ''; ?> />
		</div>
		</br>
		<?php
	}

	/**
	 * Display the boolean input HTML.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 *
	 * @since     1.0.0
	 */
	private function render_boolean( $form_id, $name, $label, $required ) {
		?>
		<div class="workable-form--field-container workable-form--field-container--radio <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
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
		<?php
	}

	/**
	 * Display the date input HTML.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 *
	 * @since     1.0.0
	 */
	private function render_date( $form_id, $name, $label, $required ) {
		?>
		<div class="workable-form--field-container workable-form--field-container--text <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--label workable-form--label-text"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<input type="date" id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--input workable-form--input-text" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> />
		</div>
		</br>
		<?php
	}

	/**
	 * Display the complex inputs HTML.
	 *
	 * A group of fields added together. At the moment, I can't see an example of how if should behave if multiple is set to false.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 * @param boolean $multiple Determins the type of complex fieldset.
	 * @param array $fields an array of fields in the fieldset.
	 *
	 * @since     1.0.0
	 */
	private function render_complex( $form_id, $name, $label, $required, $multiple, $fields ) {

		// I suspect the API may have a different way of displaying single "complex fields" but I can't find any documentaion on them.
		if ( $multiple ) :
			?>
			<fieldset id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--field-container workable-form--field-container--fieldset <?php echo $required ? 'workable-form--required-field' : ''; ?>">
				<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
				<legend class="workable-form--label workable-form--label-fieldset"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?> </legend>
				<button type="button" class="workable-form--button workable-form--button-add">Add</button>
				<div class="workable-form--complex-field-row">
				<?php

				// Go througheach field and render it.
				foreach ( $fields as $field ) {
					switch ( $field->type ) {
						case 'string':
							$this->render_text( $form_id, $field->key, $field->label, $field->required, isset( $field->max_length ) ? $field->max_length : '' );
							break;
						case 'free_text':
							$this->render_free_text( $form_id, $field->key, $field->label, $field->required );
							break;
						case 'file':
							$this->render_file( $form_id, $field->key, $field->label, $field->required, $field->supported_file_types, $field->max_file_size );
							break;
						case 'boolean':
							$this->render_boolean( $form_id, $field->key, $field->label, $field->required );
							break;
						case 'date':
							$this->render_date( $form_id, $field->key, $field->label, $field->required );
							break;
					}
				}
				?>
				<button type="button" class="workable-form--button workable-form--button-save">Save</button>
				</div>
				<input type="hidden" class="workable-form--complex-input-hidden" id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> value="" />
			</fieldset>
		</br>
			<?php
		endif;
	}

	/**
	 * Display the multiple choice HTML.
	 *
	 * If the single answer is true, radio inputs are used, otherwse, checkbox inputs are used.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 * @param boolean $single_answer Determins if one or more answers are allowed.
	 * @param array $choices an array of choices for the dropdown.
	 *
	 * @since     1.0.0
	 */
	private function render_multiple_choice( $form_id, $name, $label, $required, $single_answer, $choices ) {

		// For single answer fields, use radio inputs.
		if ( $single_answer && isset( $choices ) && ! empty( $choices ) ) {
			?>

			<div class="workable-form--field-container workable-form--field-container--radio <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
				<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
				<p><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></p>
				<?php foreach ( $choices as $choice ) : ?>
					<label for="<?php echo esc_attr( $name . '-' . $form_id . $choice->id ); ?>" class="workable-form--label workable-form--label-radio">
						<?php echo esc_html( $choice->body ); ?>
						<input type="radio" class="workable-form--input workable-form--input-radio" id="<?php echo esc_attr( $name . '-' . $form_id . '-' . $choice->id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $choice->id ); ?>"/>
					</label>
				<?php endforeach; ?>
			</div>
			</br>

		<?php } elseif ( isset( $choices ) && ! empty( $choices ) ) { ?>
			// For multi answer, use checkboxes.
			
			<div class="workable-form--field-container workable-form--field-container--checkbox <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
				<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>	
				<p><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></p>
				<?php foreach ( $choices as $choice ) : ?>
					<label for="<?php echo esc_attr( $name . '-' . $form_id . $choice->id ); ?>" class="workable-form--label workable-form--label-checkbox">
						<?php echo esc_html( $choice->body ); ?>
						<input type="checkbox" class="workable-form--input workable-form--input-checkbox" id="<?php echo esc_attr( $name . '-' . $form_id . '-' . $choice->id ); ?>" name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo esc_attr( $choice->id ); ?>"/>
					</label>
				<?php endforeach; ?>
			</div>
			</br>

			<?php
		}

	}

	/**
	 * Display the dropdown HTML.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 * @param boolean $single_answer Determins if one or more answers are allowed.
	 * @param array $choices an array of choices for the dropdown.
	 *
	 * @since     1.0.0
	 */
	private function render_dropdown( $form_id, $name, $label, $required, $single_answer, $choices ) {

		if ( isset( $choices ) && ! empty( $choices ) ) {
			?>
			<div class="workable-form--field-container workable-form--field-container--select <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
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
			<?php
		}

	}

	/**
	 * Display the numeric input HTML.
	 *
	 * @param string $form_id Unique id for the form.
	 * @param string $name The field name.
	 * @param string $label The field label.
	 * @param boolean $required Should the field be required.
	 *
	 * @since     1.0.0
	 */
	private function render_numeric( $form_id, $name, $label, $required ) {
		?>
		<div class="workable-form--field-container workable-form--field-container--text <?php echo $required ? 'workable-form--required-field' : ''; ?>" >
			<?php echo $required ? '<p class="workable-form--validation workable-form--validation-empty">Please fill in the required field</p>' : ''; ?>
			<label for="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--label workable-form--label-text"><?php echo $required ? '<span class="workable-form--required">*</span>' : ''; ?><?php echo esc_html( $label ); ?></label>
			<input type="number" id="<?php echo esc_attr( $name . '-' . $form_id ); ?>" class="workable-form--input workable-form--input-text" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?> />
		</div>
		</br>
		<?php
	}

}
