<?php
/**
 * Download File System module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      2.30.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Download_FS', false) ) {

	/**
	 * Download File System class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Download_FS {
		
		private $plugin;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param object $plugin Admin plugin
		 */
		public function __construct($plugin) {

			$this->plugin = $plugin;
		}
		
		/**
		 * Test connection
		 *
		 * @return bool Connection successful or not
		 */
		public function test_connection() {
			return is_dir($this->plugin->plugin_options['base_dir']);
		}

		/**
		 * List the files in a directory
		 *
		 * @param string $directory Directory
		 * @return array List of files
		 */
		public function list_directory($directory) {
			$files = array();
			$full_dir = trailingslashit($this->plugin->plugin_options['base_dir']) . $directory;
			if ( is_dir($full_dir) ) {
				$files = scandir($full_dir);
			}
			return $files;
		}

		/**
		 * Is the path a directory?
		 * 
		 * @param string $path Path
		 * @return boolean
		 */
		public function is_dir($path) {
			$path = preg_replace('#^'. preg_quote(trailingslashit($this->plugin->plugin_options['url'])) . '#', '', $path); // Remove the http and the domain
			$full_path = trailingslashit($this->plugin->plugin_options['base_dir']) . $path;
			return is_dir($full_path);
		}

		/**
		 * Get the content of a file
		 *
		 * @param string $source Original filename
		 * @return string File content
		 */
		public function get_content($source) {
			$filename = preg_replace('#^'. preg_quote(trailingslashit($this->plugin->plugin_options['url'])) . '#', '', $source); // Remove the http and the domain
			$filename = trailingslashit($this->plugin->plugin_options['base_dir']) . $filename; // Add the base directory
			return file_get_contents($filename);
		}
		
	}
}
