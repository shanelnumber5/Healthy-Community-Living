<?php
/**
 * WP CLI module
 *
 * Run commands by WP CLI
 * 
 * @link       https://www.fredericgilles.net/drupal-to-wordpress/
 * @since      2.26.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_WPCLI', false) ) {

	/**
	 * Import Drupal to WordPress using WP CLI
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_WPCLI {

		private $plugin;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param object $plugin Admin plugin
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
			ini_set('display_errors', true); // Display the errors that may happen (ex: Allowed memory size exhausted)
			if ( !defined('WP_ADMIN') ) {
				define('WP_ADMIN', true); // To execute the actions done when is_admin() (ex: Register Types post types)
			}
			$this->plugin->set_local_timezone();
		}
		
		/**
		 * Test the database connection
		 */
		public function test_database() {
			$this->dispatch('test_database');
		}
		
		/**
		 * Test the media connection
		 */
		public function test_media() {
			$this->dispatch('test_download');
		}
		
		/**
		 * Empty the imported data | empty all : Empty all the WordPress data
		 * 
		 * [<all>]
		 * : Empty all the WordPress content
		 * 
		 * @subcommand empty
		 */
		public function empty_wp_content($args) {
			$_POST['empty_action'] = isset($args[0]) && ($args[0] == 'all')? 'all' : '';
			$this->plugin->empty_log_file();
			$this->dispatch('empty');
		}
		
		/**
		 * Import the data
		 */
		public function import() {
			$this->dispatch('import');
		}
		
		/**
		 * Modify the internal links
		 */
		public function modify_links() {
			$this->dispatch('modify_links');
		}
		
		/**
		 * Dispatch an action
		 * 
		 * @param string $action Action to run
		 */
		private function dispatch($action) {
			$this->plugin->set_plugin_options();
			$this->set_current_user();
			
			$result = $this->plugin->dispatch($action);
			if ( isset($result['status']) && ($result['status'] == 'Error') ) {
				WP_CLI::error($result['message']);
			} else {
				WP_CLI::success($result['message']);
			}
		}
		
		/**
		 * Set the current user if not set
		 */
		private function set_current_user() {
			$user_id = get_current_user_id();
			if ( $user_id == 0 ) {
				// Get the first admin user
				$admin_users = get_users(array(
					'role__in' => 'administrator',
					'orderby' => 'ID',
				));
				if ( !empty($admin_users) ) {
					wp_set_current_user($admin_users[0]->ID);
				}
			}
		}

	}
}
