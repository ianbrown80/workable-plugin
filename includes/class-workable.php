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

		add_shortcode( 'workable_form', array( $this, 'get_workable_form' ) );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

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
	 * @param array $attr the attributes passed into the shortcode.
	 * @since     1.0.0
	 */
	public function get_workable_form( $attr ) {

		$error   = false;
		$default = array(
			'shortcode' => '',
		);

		$attributes = shortcode_atts( $default, $attr );
		$shortcode  = $attributes['shortcode'];

		if ( isset( $shortcode ) && ! empty( $shortcode ) ) {

			$form = $this->api->get_application_form( $shortcode );

			if ( ! is_wp_error( $form ) ) {

				ob_start();

				$this->render_form_header( $shortcode );

				if ( isset( $form->form_fields ) && ! empty( $form->form_fields ) ) {

					$this->render_form_fields( $shortcode, $form->form_fields );

				}

				if ( isset( $form->questions ) && ! empty( $form->questions ) ) {

					$this->render_form_questions( $shortcode, $form->questions );

				}

				$this->render_form_footer( $shortcode );

				ob_end_flush();

			} else {
				$error = $form;
			}
		} else {
			$error = new WP_Error( 'missing_shortcode', 'The form shortcode is missing' );
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
	 * @param string $id Unique id for the form.
	 * @param array $fields The fields in the form.
	 * @since     1.0.0
	 */
	private function render_form_fields( $id, $fields ) {

		foreach ( $fields as $field ) {

			switch ( $field->type ) {
				case 'string':
					$this->render_text( $id, $field->key, $field->label, $field->required, isset( $field->max_length ) ? $field->max_length : '' );
					break;
				case 'free_text':
					$this->render_free_text( $id, $field->key, $field->label, $field->required );
					break;
				case 'file':
					$this->render_file( $id, $field->key, $field->label, $field->required, $field->supported_file_types, $field->max_file_size );
					break;
				case 'boolean':
					$this->render_boolean( $id, $field->key, $field->label, $field->required );
					break;
				case 'date':
					$this->render_date( $id, $field->key, $field->label, $field->required );
					break;
				case 'complex':
					$this->render_complex( $id, $field->key, $field->label, $field->required, $field->multiple, $field->fields );
					break;
			}
		}
	}

	/**
	 * Display the form questions HTML.
	 * @param string $id Unique id for the form.
	 * @param array $questions The questions in the form.
	 * @since     1.0.0
	 */
	private function render_form_questions( $id, $questions ) {

		foreach ( $questions as $question ) {

			switch ( $question->type ) {
				case 'multiple_choice':
					$this->render_multiple_choice( $id, $question->id, $question->body, $question->required, $question->single_answer, $question->choices );
					break;
				case 'free_text':
					$this->render_free_text( $id, $question->id, $question->body, $question->required );
					break;
				case 'file':
					$this->render_file( $id, $question->id, $question->body, $question->required, $question->supported_file_types, $question->max_file_size );
					break;
				case 'boolean':
					$this->render_boolean( $id, $question->id, $question->body, $question->required );
					break;
				case 'date':
					$this->render_date( $id, $question->id, $question->body, $question->required );
					break;
				case 'dropdown':
					$this->render_dropdown( $id, $question->id, $question->body, $question->required, $question->single_answer, $question->choices );
					break;
				case 'numeric':
					$this->render_numeric( $id, $question->id, $question->body, $question->required );
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
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_text( $id, $key, $label, $required, $maxlength = '' ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> <?php echo $maxlength ? 'maxlength="' . esc_attr( $maxlength ) . '"' : ''; ?>/>
		</br>
		<?php
	}

	/**
	 * Display the textarea HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_free_text( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<textarea id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>></textarea>
		</br>
		<?php
	}

	/**
	 * Display the file input HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @param array $file_types The supported file types.
	 * @param int $required The maximum file size.
	 * @since     1.0.0
	 */
	private function render_file( $id, $key, $label, $required, $file_types, $max_file ) {

		if ( isset( $file_types ) && ! empty( $file_types ) ) {
			foreach ( $file_types as $type_key => $file_type ) {
				$file_types[ $type_key ] = '.' . $file_type;
			}
			$file_types_string = implode( ' ', $file_types );
		}

		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="file" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> <?php echo $file_types_string ? 'accept="' . esc_attr( $file_types_string ) . '"' : ''; ?> <?php echo $max_file ? 'data-max-size="' . esc_attr( $max_file ) . '"' : ''; ?>/>
		</br>
		<?php
	}

	/**
	 * Display the boolean input HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_boolean( $id, $key, $label, $required ) {
		?>
		<p><?php echo esc_html( $label ); ?></p>
		<label for="<?php echo esc_attr( $key . '-' . $id . '-yes' ); ?>">
			Yes
			<input type="radio" id="<?php echo esc_attr( $key . '-' . $id . '-yes' ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> value="true"/>
		</label>
		<label for="<?php echo esc_attr( $key . '-' . $id . '-no' ); ?>">
			No
			<input type="radio" id="<?php echo esc_attr( $key . '-' . $id . '-no' ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> value="false"/>
		</label>
		</br>
		<?php
	}

	/**
	 * Display the date input HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_date( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="date" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
		</br>
		<?php
	}

	/**
	 * Display the complex inputs HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_complex( $id, $key, $label, $required, $multiple, $fields ) {

		if ( $multiple ) :
			?>
			<fieldset id="<?php echo esc_attr( $key . '-' . $id ); ?>">
				<legend><?php echo esc_html( $label ); ?> </legend>
					<div class="field-row">
					<?php
					foreach ( $fields as $field ) {
						switch ( $field->type ) {
							case 'string':
								$this->render_text( $id, $field->key, $field->label, $field->required, isset( $field->max_length ) ? $field->max_length : '' );
								break;
							case 'free_text':
								$this->render_free_text( $id, $field->key, $field->label, $field->required );
								break;
							case 'file':
								$this->render_file( $id, $field->key, $field->label, $field->required, $field->supported_file_types, $field->max_file_size );
								break;
							case 'boolean':
								$this->render_boolean( $id, $field->key, $field->label, $field->required );
								break;
							case 'date':
								$this->render_date( $id, $field->key, $field->label, $field->required );
								break;
						}
					}
					?>
					<button type="button">Add Row</button>
					</div>
				</fieldset>
			</br>
			<?php
			endif;
	}

	/**
	 * Display the multiple choice HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_multiple_choice( $id, $key, $label, $required, $single_answer, $choices ) {
		if ( $single_answer && isset( $choices ) && ! empty( $choices ) ) {
			?>

			<p><?php echo esc_html( $label ); ?></p>
			<?php foreach ( $choices as $choice ) : ?>
				<label for="<?php echo esc_attr( $key . '-' . $id . $choice->id ); ?>">
					<?php echo esc_html( $choice->body ); ?>
					<input type="radio" id="<?php echo esc_attr( $key . '-' . $id . $choice->id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> value="<?php echo esc_attr( $key ); ?>"/>
				</label>
			<?php endforeach; ?>
			</br>

		<?php } elseif ( isset( $choices ) && ! empty( $choices ) ) { ?>
			<p><?php echo esc_html( $label ); ?></p>
			<?php foreach ( $choices as $choice ) : ?>
				<label for="<?php echo esc_attr( $key . '-' . $id . $choice->id ); ?>">
					<?php echo esc_html( $choice->body ); ?>
					<input type="checkbox" id="<?php echo esc_attr( $key . '-' . $id . $choice->id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> value="<?php echo esc_attr( $key ); ?>"/>
				</label>
			<?php endforeach; ?>
			</br>

			<?php
		}

	}


	/**
	 * Display the dropdown HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_dropdown( $id, $key, $label, $required, $single_answer, $choices ) {

		if ( isset( $choices ) && ! empty( $choices ) ) {
			?>
			<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<select id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> <?php echo $single_answer ? '' : 'multiple'; ?>>
				<option value="" disabled selected>Select an option...</option>
			<?php foreach ( $choices as $choice ) : ?>
				<option value="<?php echo esc_attr( $choice->id ); ?>"><?php echo esc_html( $choice->body ); ?></option>
			<?php endforeach; ?>

			</select>
			</br>
			<?php
		}

	}

	/**
	 * Display the numeric input HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_numeric( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="number" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
		</br>
		<?php
	}

}
