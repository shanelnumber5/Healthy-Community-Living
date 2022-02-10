<?php

namespace Endurance\WP\Module\Data;

use Endurance\WP\Module\Data\Helpers\Encryption;
use Endurance\WP\Module\Data\Helpers\Multibrand;
use Endurance\WP\Module\Data\Helpers\Plugin as PluginHelper;
use Endurance\WP\Module\Data\Helpers\Transient;

/**
 * Manages a Hub connection instance and interactions with it
 */
class HubConnection implements SubscriberInterface {

	/**
	 * Hub API url
	 *
	 * @var string
	 */
	private $api;

	/**
	 * Authentication token for data api
	 *
	 * @var string
	 */
	private $token;


	/**
	 * Whether connection attempts are currently throttled
	 *
	 * @var boolean
	 */
	private $throttled;

	/**
	 * Construct
	 */
	public function __construct() {

		if ( ! defined( 'BH_HUB_URL' ) ) {
			define( 'BH_HUB_URL', 'https://hiive.cloud/api' );
		}

		$this->api = BH_HUB_URL;

	}

	/**
	 * Register the hooks required for site verification
	 *
	 * @return void
	 */
	public function register_verification_hooks() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		add_action( 'wp_ajax_nopriv_bh-hub-verify', array( $this, 'ajax_verify' ) );

	}

	/**
	 * Set up REST API routes
	 *
	 * @return void
	 */
	public function rest_api_init() {
		$controller = new API\Verify( $this );
		$controller->register_routes();
	}

	/**
	 * Process the admin-ajax request
	 *
	 * @return void
	 */
	public function ajax_verify() {
		$valid  = $this->verify_token( $_REQUEST['token'] );
		$status = ( $valid ) ? 200 : 400;

		$data = array(
			'token' => $_REQUEST['token'],
			'valid' => $valid,
		);
		wp_send_json( $data, $status );
	}

	/**
	 * Confirm whether verification token is valid
	 *
	 * @param string $token Token to verify
	 *
	 * @return boolean
	 */
	public function verify_token( $token ) {
		$saved_token = Transient::get( 'bh_data_verify_token' );

		if ( $saved_token && $saved_token === $token ) {
			Transient::delete( 'bh_data_verify_token' );

			return true;
		}

		return false;
	}

	/**
	 * Check whether site has established connection to hub
	 *
	 * @return boolean
	 */
	public static function is_connected() {
		return (bool) ( self::get_auth_token() );
	}

	/**
	 * Attempt to connect to hub
	 *
	 * @return void
	 */
	public function connect() {

		if ( $this->is_throttled() ) {
			return;
		}

		$this->throttle();

		$token = md5( wp_generate_password() );
		Transient::set( 'bh_data_verify_token', $token, 5 * MINUTE_IN_SECONDS );

		$data                 = $this->get_core_data();
		$data['verify_token'] = $token;
		$data['plugins']      = PluginHelper::collect_installed();

		$args = array(
			'body'     => wp_json_encode( $data ),
			'headers'  => array(
				'Content-Type' => 'applicaton/json',
				'Accept'       => 'applicaton/json',
			),
			'blocking' => true,
			'timeout'  => 30,
		);

		$attempts = intval( get_option( 'bh_data_connection_attempts', 0 ) );
		update_option( 'bh_data_connection_attempts', $attempts + 1 );

		$response = wp_remote_post( $this->api . '/sites/v1/connect', $args );
		$status   = wp_remote_retrieve_response_code( $response );

		// Created = 201; Updated = 200
		if ( 201 === $status || 200 === $status ) {
			$body = json_decode( wp_remote_retrieve_body( $response ) );
			if ( ! empty( $body->token ) ) {
				$encryption      = new Encryption();
				$encrypted_token = $encryption->encrypt( $body->token );
				update_option( 'bh_data_token', $encrypted_token );
			}
		}

	}

	/**
	 * Set the connection throttle
	 *
	 * @return void
	 */
	public function throttle() {
		$interval = $this->get_throttle_interval();

		$this->throttle = Transient::set( 'bh_data_connection_throttle', true, $interval );
	}

	/**
	 * Determine the throttle interval based off number of connection attempts
	 *
	 * @return integer Time to wait until next connection attempt
	 */
	public function get_throttle_interval() {

		$attempts = intval( get_option( 'bh_data_connection_attempts', 0 ) );

		// Throttle intervals step-up:
		// Hourly for 4 hours
		// Twice a day for 3 days
		// Once a day for 3 days
		// Every 3 days for 3 times
		// Once a week
		if ( $attempts <= 4 ) {
			return HOUR_IN_SECONDS;
		} elseif ( $attempts <= 10 ) {
			return 12 * HOUR_IN_SECONDS;
		} elseif ( $attempts <= 13 ) {
			return DAY_IN_SECONDS;
		} elseif ( $attempts <= 16 ) {
			return 3 * DAY_IN_SECONDS;
		} else {
			return WEEK_IN_SECONDS;
		}

	}

	/**
	 * Check whether connection is throttled
	 *
	 * @return boolean
	 */
	public function is_throttled() {
		$this->throttled = Transient::get( 'bh_data_connection_throttle' );

		return $this->throttled;
	}

	/**
	 * Post event data payload to the hub
	 *
	 * @param Event[] $events Array of Event objects representing the actions that occurred
	 * @param bool    $is_blocking
	 *
	 * @return array|\WP_Error
	 */
	public function notify( $events, $is_blocking = false ) {

		// If for some reason we are not connected, bail out now.
		if ( ! self::is_connected() ) {
			return new \WP_Error( 'hub_connection', __( 'This site is not connected to the hub.' ) );
		}

		$payload = array(
			'environment' => $this->get_core_data(),
			'events'      => $events,
		);

		$args = array(
			'body'     => wp_json_encode( $payload ),
			'headers'  => array(
				'Content-Type'  => 'applicaton/json',
				'Accept'        => 'applicaton/json',
				'Authorization' => 'Bearer ' . self::get_auth_token(),
			),
			'blocking' => $is_blocking,
			'timeout'  => $is_blocking ? 10 : .5,
		);

		return wp_remote_post( $this->api . '/sites/v1/events', $args );
	}

	/**
	 * Try to return the auth token
	 *
	 * @return string|null The decrypted token if it's set
	 */
	public static function get_auth_token() {
		$encrypted_token = get_option( 'bh_data_token' );
		if ( false !== $encrypted_token ) {
			$encryption = new Encryption();

			return $encryption->decrypt( $encrypted_token );
		}
	}


	/**
	 * Get core site data for initial connection
	 *
	 * @return array
	 */
	public function get_core_data() {
		global $wpdb, $wp_version;

		return array(
			'brand'       => sanitize_title( get_option( 'mm_brand', 'false' ) ),
			'cache_level' => intval( get_option( 'endurance_cache_level', 2 ) ),
			'cloudflare'  => get_option( 'endurance_cloudflare_enabled', false ),
			'data'        => NFD_DATA_MODULE_VERSION,
			'email'       => get_option( 'admin_email' ),
			'hostname'    => gethostname(),
			'mysql'       => $wpdb->db_version(),
			'origin'      => Multibrand::get_origin_plugin_id(),
			'php'         => phpversion(),
			'plugin'      => Multibrand::get_origin_plugin_version(),
			'url'         => get_site_url(),
			'wp'          => $wp_version,
		);

	}
}
