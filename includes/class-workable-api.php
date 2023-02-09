<?php

/**
 * Creates the API connection with Workable
 *
 * @since      1.0.0
 * @package    Workable
 * @subpackage Workable/includes
 * @author     Ian Brown <brown.i18@sky.com>
 */
class Workable_API {

	/**
	 * The access token needed to get data from Workable.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $token    The Workable access token.
	 */
	private $token;

	/**
	 * The subdomain for the client to specify which forms are availabe.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $subdomain    The client specific subdomain.
	 */
	private $subdomain;

	/**
	 * Set the variables needed for the API access.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->token     = 'nJfJpB6pesu3I0yFqVuN4MB6eJmyYvvv63ywPHKVMB0';
		$this->subdomain = 'fondamexican';

	}

	/**
	 * Generate the header for the API.
	 *
	 * @return stringThe authorisation header.
	 *
	 * @since    1.0.0
	 */
	private function get_header() {

		// Create the athorisation header
		return 'Bearer ' . $this->token;

	}

	/**
	 * Generate the API URL.
	 *
	 * @return string The API base url.
	 *
	 * @since    1.0.0
	 */
	private function get_url() {

		// Create the url for the API.
		return 'https://' . $this->subdomain . '.workable.com/spi/v3/';

	}

	/**
	 * Get the application form data from the API.
	 *
	 * @param string $shortcode The Workable job shortcode of the application form.
	 * @return mixed The response body or an error.
	 *
	 * @since    1.0.0
	 */
	public function get_application_form( $shortcode ) {

		// Pass the application form shortcode to the Workable API.
		if ( $shortcode ) {
			$response = wp_remote_request(
				$this->get_url() . 'jobs/' . $shortcode . '/application_form/',
				array(
					'headers' => array(
						'Authorization' => $this->get_header(),
						'Content-Type'  => 'application/json',
					),
					'method'  => 'GET',
				),
			);

			// Return a response depending on the error code returned.
			switch ( $response['response']['code'] ) {
				case '200':
					$result = json_decode( $response['body'] );
					break;
				case '401':
					$result = new WP_Error( 'not_authorised', $response['response']['message'] );
					break;
				case '404':
					$result = new WP_Error( 'not_found', $response['response']['message'] );
					break;
				default:
					$result = new WP_Error( 'unknown', 'Unknown error' );
			}
		} else {
			// Return a response if the form shorcode is missing from the WordPress shortcode.
			$result = new WP_Error( 'missing_shortcode', 'The form shortcode is missing' );

		}

		return $result;
	}

	/**
	 * Send the application form data to the API.
	 *
	 * @since    1.0.0
	 */
	public function send_application_form() {

		$result = array();

		// Prevent the form from being spammed by bots.
		if ( wp_verify_nonce( $_POST['workable_form_nonce'], 'submit_workable_form' ) ) {

			// Convert the $_POST data into the format needed by Workable.
			$form_data            = $_POST;
			$form_data['sourced'] = false;

			// Remove any POST data that doesn't need to be sent.
			unset( $form_data['_wp_http_referer'] );
			unset( $form_data['workable_form_nonce'] );
			unset( $form_data['action'] );

			// Remove any empty keys.
			foreach ( $form_data as $key => $value ) {
				if ( '' === $value ) {
					unset( $form_data[ $key ] );
				}
			}

			// Add the uploaded files to the form data.
			if ( isset( $_FILES ) && ! empty( $_FILES ) ) {
				foreach ( $_FILES as $field => $file ) {
					if ( $file['name'] && $file['tmp_name'] ) {
						$form_data[ $field ] = array(
							'name' => $file['name'],
							'data' => base64_encode( file_get_contents( $file['tmp_name'] ) ),
						);
					}
				}
			}

			// Send the data to Workable.
			$response = wp_remote_request(
				$this->get_url() . 'jobs/' . $form_data['shortcode'] . '/candidates',
				array(
					'headers' => array(
						'Authorization' => $this->get_header(),
						'Content-Type'  => 'application/json',
						'Accept'        => 'application/json',
					),
					'method'  => 'POST',
					'body'    => wp_json_encode( $form_data ),
				),
			);
		} else {
			// If the nonce has failed, prepare an error.
			$result = new WP_Error( 'error', 'Validation error' );
		}

		// Prepare a response depending on the code returned by the API.
		switch ( $response['response']['code'] ) {
			case '201':
				$result['success'] = 'Thank you for your submission';
				break;
			case '401':
				$result = new WP_Error( 'error', $response['response']['message'] );
				break;
			case '422':
				$result = new WP_Error( 'error', $response['response']['message'] );
				break;
			default:
				$result = new WP_Error( 'error', 'Unfortunatly we were unable to submit your application. Please try again later' );
		}

		// Send the response back to the page to be displayed.
		echo wp_json_encode( $result );

		wp_die();

	}

}
