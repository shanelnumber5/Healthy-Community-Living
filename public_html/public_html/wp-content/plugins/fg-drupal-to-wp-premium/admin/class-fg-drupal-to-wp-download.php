<?php
/**
 * Download module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      2.30.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Download', false) ) {

	/**
	 * Download class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Download {
		
		private $plugin;
		private $protocol;
		private $main_download_manager;
		private $http_download_manager;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param object $plugin Admin plugin
		 * @param string $protocol Download protocol (file_system|http|ftp)
		 */
		public function __construct($plugin, $protocol) {

			$this->plugin = $plugin;
			$this->protocol = $protocol;
			
			switch ( $protocol ) {
				case 'file_system':
					$this->main_download_manager = new FG_Drupal_to_WordPress_Download_FS($this->plugin);
					break;
				
				case 'ftp':
					$this->main_download_manager = new FG_Drupal_to_WordPress_Download_FTP($this->plugin);
					$this->main_download_manager->login();
					break;
				
				case 'http':
				default:
					$this->main_download_manager = new FG_Drupal_to_WordPress_Download_HTTP($this->plugin);
					break;
			}
			
			$this->http_download_manager = new FG_Drupal_to_WordPress_Download_HTTP($this->plugin); // For the external files
		}
		
		/**
		 * Test connection
		 *
		 * @return bool Connection successful or not
		 */
		public function test_connection() {
			$result = $this->main_download_manager->test_connection();
			$protocol_upcase = strtoupper(str_replace('_', ' ', $this->protocol));
			if ( $result ) {
				$this->plugin->display_admin_notice(sprintf(__('%s connection successful', 'fg-drupal-to-wp'), $protocol_upcase));
			} else {
				$this->plugin->display_admin_error(sprintf(__('%s connection failed', 'fg-drupal-to-wp'), $protocol_upcase));
			}
			return $result;
		}

		/**
		 * List the files in a directory
		 *
		 * @param string $directory Directory
		 * @return array List of files
		 */
		public function list_directory($directory='') {
			return $this->main_download_manager->list_directory($directory);
		}
		
		/**
		 * Copy a file or a directory
		 * 
		 * @param string $source Original file or directory name
		 * @param string $destination Destination file or directory name
		 * @param bool $recursive Recursive copy?
		 * @return bool File copied or not
		 */
		public function copy($source, $destination, $recursive=true) {
			if ( $this->is_dir($source) ) {
				// Directory
				return $this->copy_dir($source, $destination, $recursive);
			} else {
				// File
				return $this->copy_file($source, $destination);
			}
		}
		
		/**
		 * Is the path a directory?
		 * 
		 * @param string $path Path
		 * @return boolean
		 */
		public function is_dir($path) {
			return $this->main_download_manager->is_dir($path);
		}

		/**
		 * Copy a directory
		 * 
		 * @param string $source Original directory name
		 * @param string $destination Destination directory name
		 * @param bool $recursive Recursive copy?
		 * @return bool Directory copied or not
		 */
		public function copy_dir($source, $destination, $recursive=true) {
			$result = true;
			if ( !is_dir($destination) ) {
				mkdir($destination, 0755, true); // Create the directory if not existing
			}
			foreach ( $this->list_directory($source) as $file ) {
				if ( preg_match('/^\.+$/', $file) ) { // Skip . and ..
					continue;
				}
				$source_filename = trailingslashit($source) . $file;
				$dest_filename = trailingslashit($destination) . $file;
				if ( $recursive || !$this->is_dir($source_filename) ) {
					$result |= $this->copy($source_filename, $dest_filename, $recursive);
				}
			}
			return $result;
		}
			
		/**
		 * Copy a file
		 *
		 * @param string $source Original filename
		 * @param string $destination Destination filename
		 * @return bool File copied or not
		 */
		public function copy_file($source, $destination) {
			$result = false;
			
			if ( !$this->plugin->plugin_options['force_media_import'] && file_exists($destination) && (filesize($destination) > 0) ) {
				// Don't download the file if already downloaded
				return true;
			}
			
//			db("Copy $source => $destination");
			if ( $this->is_external_file($source) ) {
				$file_content = @$this->http_download_manager->get_content($source); // External file: Use HTTP
			} else {
				$file_content = @$this->main_download_manager->get_content($source);
			}
			if ( $file_content !== false ) {
				$result = (file_put_contents($destination, $file_content) !== false);
			}
			return $result;
		}
		
		/**
		 * Check if the file is external
		 * 
		 * @param string $source Original filename
		 * @return bool External file?
		 */
		private function is_external_file($source) {
			$source = preg_replace('#^' . preg_quote($this->plugin->plugin_options['url']) . '#', '', $source); // Remove the http and the default domain
			return preg_match('/^http/', $source);
		}
		
	}
}
