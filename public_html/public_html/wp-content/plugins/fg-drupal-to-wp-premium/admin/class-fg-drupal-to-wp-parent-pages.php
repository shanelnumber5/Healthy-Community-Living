<?php

/**
 * Parent pages module
 *
 * @link       https://www.fredericgilles.net/drupal-to-wordpress/
 * @since      1.82.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Parent_Pages', false) ) {

	/**
	 * Parent pages class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Parent_Pages {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
		}
		
		/**
		 * Set the parent pages
		 * 
		 */
		public function set_parent_pages() {
			if ( isset($this->plugin->premium_options['skip_redirects']) && $this->plugin->premium_options['skip_redirects'] ) {
				return;
			}
			
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			
			$this->plugin->log(__('Setting parent pages...', $this->plugin->get_plugin_name()));
			$imported_parent_pages = 0;
			$matches = array();
			
			$plugin_redirect = new FG_Drupal_to_WordPress_Redirect();
			$redirects = $plugin_redirect->get_imported_redirects();
			foreach ( $redirects as $url => $redirect ) {
				if ( preg_match('#(.*)/.*?#', $url, $matches) ) {
					$parent_url = $matches[1];
					if ( array_key_exists($parent_url, $redirects) ) {
						$parent = $redirects[$parent_url];
						if ( $parent['type'] == $redirect['type'] ) { // the post type must be the same
							// Set the parent page
							wp_update_post(array(
								'ID' => $redirect['id'],
								'post_parent' => $parent['id'],
							));
							$imported_parent_pages++;
						}
					}
				}
			}
			
			$this->plugin->display_admin_notice(sprintf(_n('%d parent page set', '%d parent pages set', $imported_parent_pages, $this->plugin->get_plugin_name()), $imported_parent_pages));
		}
		
	}
}
