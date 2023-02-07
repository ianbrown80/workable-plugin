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

				$this->render_form_header();

				if ( isset( $form->form_fields ) && ! empty( $form->form_fields ) ) {

					$this->render_form_fields( $shortcode, $form->form_fields );

				}

				if ( isset( $form->questions ) && ! empty( $form->questions ) ) {

					$this->render_form_questions( $shortcode, $form->questions );

				}

				$this->render_form_footer();

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
	private function render_form_header() {

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
					$this->render_text_field( $id, $field->key, $field->label, $field->required );
					break;
				case 'free_text':
					$this->render_free_text_field( $id, $field->key, $field->label, $field->required );
					break;
				case 'file':
					$this->render_file_field( $id, $field->key, $field->label, $field->required, $field->supported_file_types, $field->max_file_size );
					break;
				case 'boolean':
					$this->render_boolean_field( $id, $field->key, $field->label, $field->required );
					break;
				case 'date':
					$this->render_date_field( $id, $field->key, $field->label, $field->required );
					break;
				case 'complex':
					$this->render_complex_field( $id, $field->key, $field->label, $field->required );
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
					$this->render_multiple_choice_question( $id, $question->id, $question->body, $question->required );
					break;
				case 'free_text':
					$this->render_free_text_question( $id, $question->id, $question->body, $question->required );
					break;
				case 'file':
					$this->render_file_question( $id, $question->id, $question->body, $question->required, $question->supported_file_types, $question->max_file_size );
					break;
				case 'boolean':
					$this->render_boolean_question( $id, $question->id, $question->body, $question->required );
					break;
				case 'date':
					$this->render_date_question( $id, $question->id, $question->body, $question->required );
					break;
				case 'dropdown':
					$this->render_dropdown_question( $id, $question->id, $question->body, $question->required );
					break;
				case 'numeric':
					$this->render_numeric_question( $id, $question->id, $question->body, $question->required );
					break;
			}
		}
	}

	/**
	 * Display the form footer HTML.
	 * @since     1.0.0
	 */
	private function render_form_footer() {

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
	private function render_text_field( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
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
	private function render_free_text_field( $id, $key, $label, $required ) {
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
	private function render_file_field( $id, $key, $label, $required, $file_types, $max_file ) {

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
	private function render_boolean_field( $id, $key, $label, $required ) {
		?>
		<p><?php echo esc_html( $label ); ?></p>
		<label>
			Yes
			<input type="radio" id="<?php echo esc_attr( $key . '-' . $id . '-yes' ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> value="true"/>
		</label>
		<label>
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
	private function render_date_field( $id, $key, $label, $required ) {
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
	private function render_complex_field( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
		</br>
		<?php
	}

	/**
	 * Display the multiple choice HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_multiple_choice_question( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
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
	private function render_free_text_question( $id, $key, $label, $required ) {
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
	private function render_file_question( $id, $key, $label, $required, $file_types, $max_file ) {

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
	private function render_boolean_question( $id, $key, $label, $required ) {
		?>
		<p><?php echo esc_html( $label ); ?></p>
		<label>
			Yes
			<input type="radio" id="<?php echo esc_attr( $key . '-' . $id . '-yes' ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?> value="true"/>
		</label>
		<label>
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
	private function render_date_question( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="date" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
		</br>
		<?php
	}

	/**
	 * Display the dropdown HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_dropdown_question( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
		</br>
		<?php
	}

	/**
	 * Display the numeric input HTML.
	 * @param string $id Unique id for the form.
	 * @param string $key The field name.
	 * @param string $label The field label.
	 * @param string $required Should the field be required.
	 * @since     1.0.0
	 */
	private function render_numeric_question( $id, $key, $label, $required ) {
		?>
		<label for="<?php echo esc_attr( $key . '-' . $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="number" id="<?php echo esc_attr( $key . '-' . $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>/>
		</br>
		<?php
	}

}
