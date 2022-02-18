<?php
/**
 * FTP module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      2.30.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_FTP', false) ) {

	/**
	 * FTP class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_FTP extends FG_Drupal_to_WordPress_Download_FTP {
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			parent::__construct($plugin);
			
			// Default values
			$this->plugin->ftp_options = array(
				'hostname'			=> '',
				'port'				=> 21,
				'username'			=> '',
				'password'			=> '',
				'connection_type'	=> 'ftp',
				'basedir'			=> '',
			);
			$options = get_option('fgd2wp_ftp_options');
			if ( is_array($options) ) {
				$this->plugin->ftp_options = array_merge($this->plugin->ftp_options, $options);
			}
		}
		
		/**
		 * Get the WP options name
		 * 
		 * @param array $option_names Option names
		 * @return array Option names
		 */
		public function get_option_names($option_names) {
			$option_names[] = 'fgd2wp_ftp_options';
			return $option_names;
		}

		/**
		 * Display the FTP settings
		 * 
		 */
		public function display_ftp_settings() {
			$data = array();
			$data['ftp_host'] = $this->plugin->ftp_options['hostname'];
			$data['ftp_port'] = $this->plugin->ftp_options['port'];
			$data['ftp_login'] = $this->plugin->ftp_options['username'];
			$data['ftp_password'] = $this->plugin->ftp_options['password'];
			$data['ftp_connection_type'] = $this->plugin->ftp_options['connection_type'];
			$data['ftp_dir'] = $this->plugin->ftp_options['basedir'];
			require('partials/ftp-settings.php');
		}

		/**
		 * Save the FTP settings
		 * 
		 */
		public function save_ftp_settings() {
			$this->plugin->ftp_options = array_merge($this->plugin->ftp_options, $this->validate_form_info());
			update_option('fgd2wp_ftp_options', $this->plugin->ftp_options);
		}
		
		/**
		 * Validate POST info
		 *
		 * @return array Form parameters
		 */
		private function validate_form_info() {
			$ftp_host = filter_input(INPUT_POST, 'ftp_host', FILTER_SANITIZE_STRING);
			$ftp_port = filter_input(INPUT_POST, 'ftp_port', FILTER_SANITIZE_STRING);
			$ftp_login = filter_input(INPUT_POST, 'ftp_login', FILTER_SANITIZE_STRING);
			$ftp_password = filter_input(INPUT_POST, 'ftp_password', FILTER_SANITIZE_STRING);
			$ftp_connection_type = filter_input(INPUT_POST, 'ftp_connection_type', FILTER_SANITIZE_STRING);
			$ftp_dir = filter_input(INPUT_POST, 'ftp_dir', FILTER_SANITIZE_STRING);
			return array(
				'hostname'			=> isset($ftp_host)? $ftp_host : '',
				'port'				=> isset($ftp_port)? $ftp_port : '',
				'username'			=> isset($ftp_login)? $ftp_login : '',
				'password'			=> isset($ftp_password)? $ftp_password : '',
				'connection_type'	=> isset($ftp_connection_type)? $ftp_connection_type : '',
				'basedir'			=> isset($ftp_dir)? $ftp_dir : '',
			);
		}
		
		/**
		 * Test FTP connection
		 *
		 */
		public function test_ftp_connection($action) {
			if ( $action == 'test_ftp' ) {

				// Save database options
				$this->plugin->save_plugin_options();

				// Test the database connection
				if ( check_admin_referer( 'parameters_form', 'fgd2wp_nonce' ) ) { // Security check
					if ( $this->test_connection() ) {
						$this->plugin->display_admin_notice(__('FTP connection successful', 'fg-drupal-to-wp'));
						$result = array('status' => 'OK', 'message' => __('FTP connection successful', 'fg-drupal-to-wp'));
					} else {
						$result = array('status' => 'Error', 'message' => __('FTP connection failed', 'fg-drupal-to-wp'));
					}
					echo json_encode($result);
				}
			}
		}
		
	}
}
