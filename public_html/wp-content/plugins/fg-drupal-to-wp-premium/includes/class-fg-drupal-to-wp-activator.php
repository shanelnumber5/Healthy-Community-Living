<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wordpress.org/plugins/fg-drupal-to-wp/
 * @since      1.0.0
 *
 * @package    FG_Drupal_to_WordPress
 * @subpackage FG_Drupal_to_WordPress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    FG_Drupal_to_WordPress
 * @subpackage FG_Drupal_to_WordPress/includes
 * @author     Frédéric GILLES
 */
class FG_Drupal_to_WordPress_Activator {

	/**
	 * Activate the plugin
	 *
	 * @since      1.0.0
	 */
	public static function activate() {

		FG_Drupal_to_WordPress_Redirect::install(); // Create the redirect table
	}

}
