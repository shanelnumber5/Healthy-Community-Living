<?php
/**
 * Download FTP module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      2.30.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Download_FTP', false) ) {

	/**
	 * Download FTP class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Download_FTP {
		
		protected $plugin;
		protected $ftp;
		
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
			return $this->login();
		}

		/**
		 * List the files in a directory
		 *
		 * @param string $directory Directory
		 * @return array List of files
		 */
		public function list_directory($directory) {
			$files = array();
			
			if ( $this->is_connected() ) {
				$full_directory = trailingslashit($this->plugin->ftp_options['basedir']) . $directory;
				$ftp_dir_list = $this->ftp->dirlist($full_directory, false);
				if ( is_array($ftp_dir_list) ) {
					return array_keys($ftp_dir_list);
				}
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
			$full_path = trailingslashit($this->plugin->ftp_options['basedir']) . $path;
			return $this->ftp->is_dir($full_path);
		}

		/**
		 * Get the content of a file
		 *
		 * @param string $source Original filename
		 * @return string File content
		 */
		public function get_content($source) {
			$content = false;
			if ( $this->is_connected() ) {
				$filename = preg_replace('#^'. preg_quote(trailingslashit($this->plugin->plugin_options['url'])) . '#', '', $source); // Remove the http and the domain
				$filename = trailingslashit($this->plugin->ftp_options['basedir']) . $filename; // Add the FTP base directory
				$content = $this->ftp->get_contents($filename);
				
				// Display error
				if ( isset($this->ftp->errors->errors) && !empty($this->ftp->errors->errors) ) {
					$error_message = '';
					foreach ( $this->ftp->errors->errors as $key => $value ) {
						$error_message = "$key => " . implode("\n", $value) . "\n";
					}
					trigger_error($error_message, E_USER_WARNING);
				}
			}
			return $content;
		}
		
		/**
		 * FTP login
		 *
		 * @return bool Login successful or not
		 */
		public function login() {
			$result = false;

			if ( !empty($this->plugin->ftp_options['hostname']) && !empty($this->plugin->ftp_options['username']) && !empty($this->plugin->ftp_options['password']) ) {
				if ( !defined('FS_CONNECT_TIMEOUT') ) {
					define('FS_CONNECT_TIMEOUT', 3);
				}
				if ( $this->plugin->ftp_options['connection_type'] == 'sftp' ) {
					// SFTP
					if ( class_exists('WP_Filesystem_SSH2') ) {
						$this->ftp = new WP_Filesystem_SSH2($this->plugin->ftp_options);
					} else {
						$this->plugin->display_admin_error(__('FTP connection failed:', 'fg-drupal-to-wp') . ' ' . sprintf(__('(SFTP requires the <a href="%s" target="_blank">WP Filesystem SSH2</a> plugin)', 'fg-drupal-to-wp'), 'https://www.fredericgilles.net/wp-filesystem-ssh2/'));
						return false;
					}
				} else {
					// FTP and FTPS
					$this->ftp = new WP_Filesystem_FTPext($this->plugin->ftp_options);
				}
				if ( $this->ftp->connect() ) {
					if ( $this->ftp->is_dir($this->plugin->ftp_options['basedir']) ) {
						// Connection successful
						$result = true;
					} else {
						$this->plugin->display_admin_error(__('FTP connection failed:', 'fg-drupal-to-wp') . ' ' . "Can't changedir to " . $this->plugin->ftp_options['basedir']);
					}
				} else {
					// Connection error
					$error_message = '';
					if ( isset($this->ftp->errors->errors) ) {
						$errors = $this->ftp->errors->errors;
						foreach ( $errors as $key => $value ) {
							$error_message = "$key => " . implode("\n", $value) . "\n";
						}
					}
					$this->plugin->display_admin_error(__('FTP connection failed:', 'fg-drupal-to-wp') . ' ' . $error_message);
				}
			}
			return $result;
		}
		
		/**
		 * Check if the FTP connection is active - Try to reconnect if this is not the case
		 * 
		 * @return bool FTP connection is active
		 */
		protected function is_connected() {
			$result = isset($this->ftp->link) && ($this->ftp->link !== false);
			if ( !$result ) {
				$result = $this->login(); // Try to reconnect
			}
			return $result;
		}
		
	}
}
