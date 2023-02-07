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

			$result = new WP_Error( 'missing_shortcode', 'The form shortcode is missing' );

		}

		return $result;
	}

}
