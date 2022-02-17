<?php

/**
 * Automatic import executed by cron
 *
 * @link              https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since             1.90.0
 * @package           FG_Drupal_to_WordPress
 */

ignore_user_abort(true);

if ( isset($_SERVER['REQUEST_URI']) || !empty($_POST) || defined('DOING_AJAX') || defined('DOING_CRON') ) {
	die();
}

define('DOING_CRON', true); // Tell WordPress we are doing the CRON task

$_SERVER["HTTP_USER_AGENT"] = 'PHP'; // To avoid notices from other plugins

if ( !defined('ABSPATH') ) {
	// Set up WordPress environment
	require_once( __DIR__ . '/../../../wp-load.php' );
	require_once( __DIR__ . '/../../../wp-admin/includes/admin.php' );
	$cron = new FG_Drupal_to_WordPress_Cron();
	$cron->run();
}

/**
 * Cron class
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @author     Frédéric GILLES
 */
class FG_Drupal_to_WordPress_Cron {

	/**
	 * Run the import
	 */
	public function run() {
		global $fgd2wpp;
		
		$fgd2wpp->set_plugin_options();
		$this->set_current_user();
		
		$actions = array('import');
		foreach ( $actions as $action ) {
			$this->do_action($action);
		}
		
		echo "IMPORT COMPLETED\n";
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
	
	/**
	 * Do an action
	 * 
	 * @param string $action Action
	 */
	private function do_action($action) {
		global $fgd2wpp;

		echo esc_html($action) . "...\n";
		$time_start = date('Y-m-d H:i:s');
		$fgd2wpp->display_admin_notice("=== START $action $time_start ===");
		
		echo $fgd2wpp->dispatch($action);
		
		$time_end = date('Y-m-d H:i:s');
		$fgd2wpp->display_admin_notice("=== END $action $time_end ===\n");
	}
	
}
