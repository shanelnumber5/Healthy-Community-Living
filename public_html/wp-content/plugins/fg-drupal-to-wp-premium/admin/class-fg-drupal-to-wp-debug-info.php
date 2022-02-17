<?php
/**
 * Debug Info class
 *
 * @since      2.0.0
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/includes
 * @author     Frédéric GILLES
 */

if ( !class_exists('FG_Drupal_to_WordPress_DebugInfo', false) ) {
	class FG_Drupal_to_WordPress_DebugInfo {
		
		private $option_names_filter = 'fgd2wp_get_option_names';
		
		/**
		 * Display the Debug Info
		 * 
		 * @global object $wpdb
		 */
		public function display() {
			global $wpdb;
			$matches = array();
			
			$protocol = is_ssl()? 'https' : 'http';
			$plugin_url = $protocol . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			$plugin_url = preg_replace('/&tab=debuginfo/', '', $plugin_url);
			
			$theme = wp_get_theme();
			
			// Plugins
			$plugins = get_plugins();
			$current_plugin_path = preg_match('#' . preg_quote(wp_normalize_path(WP_PLUGIN_DIR)) . '/(.*?)/#', wp_normalize_path(__DIR__), $matches)? $matches[1] : '';
			$active_plugins_paths = get_option('active_plugins');
			$active_plugins = array();
			$current_plugin = array('Name' => '', 'Version' => '');
			$addons = array();
			foreach ( $plugins as $plugin_path => $plugin ) {
				if ( in_array($plugin_path, $active_plugins_paths) ) {
					$active_plugins[] = $plugin;
					// Current plugin
					if ( preg_match('#^' . $current_plugin_path . '/#', $plugin_path) ) {
						$current_plugin = $plugin;
					}
					// Add-ons
					if ( preg_match('#^' . $current_plugin_path . '-#', $plugin_path) ) {
						$addons[] = $plugin;
					}
				}
			}
			
			// Plugin options
			$plugin_options = $this->get_plugin_options();
			
			$pdo_drivers = extension_loaded('PDO')? implode(', ', PDO::getAvailableDrivers()) : 'not loaded';
			
			echo "### BEGIN DEBUG INFO ###\n\n";
			echo "WordPress info:\n";
			echo  '  Plugin URL: '. esc_html($plugin_url) . "\n";
			echo  '  Site URL: '. site_url() . "\n";
			echo  '  Home URL: '. home_url() . "\n";
			echo  '  WP version: '. get_bloginfo('version') . "\n";
			echo  '  WP Memory limit: '. esc_html(WP_MEMORY_LIMIT) . "\n";
			echo  '  Multisite: '. (is_multisite()? 'yes' : 'no') . "\n";
			echo  '  Permalink structure: '. esc_html(get_option('permalink_structure')) . "\n";
			echo  '  Media in year/month folders: '. (get_option('uploads_use_yearmonth_folders')? 'yes' : 'no') . "\n";
			echo  '  Active theme: '. esc_html($theme->Name) . ' ' . esc_html($theme->Version) . "\n";
			echo  "  Active plugins: \n";
			foreach ( $active_plugins as $active_plugin ) {
				echo '    ' . esc_html($active_plugin['Name']) . ' ' . esc_html($active_plugin['Version']) . "\n";
			}
			
			echo "\nPHP info:\n";
			echo  '  PHP version: '. esc_html(PHP_VERSION) . "\n";
			echo  '  Web server info: '. esc_html($_SERVER['SERVER_SOFTWARE']) . "\n";
			echo  '  memory_limit: '. esc_html(ini_get('memory_limit')) . "\n";
			echo  '  max_execution_time: '. esc_html(ini_get('max_execution_time')) . "\n";
			echo  '  max_input_time: '. esc_html(ini_get('max_input_time')) . "\n";
			echo  '  post_max_size: '. esc_html(ini_get('post_max_size')) . "\n";
			echo  '  upload_max_filesize: '. esc_html(ini_get('upload_max_filesize')) . "\n";
			echo  '  allow_url_fopen: '. esc_html(ini_get('allow_url_fopen')) . "\n";
			echo  '  PDO: '. esc_html($pdo_drivers) . "\n";
			
			echo "\nMySQL info:\n";
			echo  '  MySQL version: '. esc_html($wpdb->db_version()) . "\n";
			echo  '  max_allowed_packet: '. esc_html($this->bytes_format($wpdb->get_var("SHOW VARIABLES LIKE 'max_allowed_packet';", 1))) . "\n";
			echo  '  wait_timeout: '. esc_html($wpdb->get_var("SHOW VARIABLES LIKE 'wait_timeout';", 1)) . "\n";
			
			echo "\nPlugin info:\n";
			echo '  ' . esc_html($current_plugin['Name']) . ' ' . esc_html($current_plugin['Version']) . "\n";
			echo "  Add-ons:\n";
			foreach ( $addons as $addon ) {
				echo '    ' . esc_html($addon['Name']) . ' ' . esc_html($addon['Version']) . "\n";
			}
			echo "  Options:\n";
			foreach ( $plugin_options as $option ) {
				$option_value = $option['value'];
				if ( preg_match('/password/', $option['key']) ) {
					$option_value = '***'; // Don't show the passwords
				}
				if ( is_array($option_value) ) {
					$option_value = print_r($option_value, true);
				}
				echo '    ' . esc_html($option['key']) . ': ' . esc_html($option_value) . "\n";
			}
			
			echo "\n### END DEBUG INFO ###\n";
		}
		
		/**
		 * Get the plugin options
		 * 
		 * @return array Plugin options
		 */
		private function get_plugin_options() {
			$plugin_options = array();
			$option_names = apply_filters($this->option_names_filter, array());
			foreach ( $option_names as $option_name ) {
				$options = get_option($option_name, array());
				foreach ( $options as $key => $value ) {
					$plugin_options[] = array('key' => $key, 'value' => $value);
				}
			}
			return $plugin_options;
		}
		
		/**
		 * Convert a number to a human readable number
		 * 
		 * @since 2.21.0
		 * 
		 * @param int $n Number
		 * @return string Human readable number
		 */
		private function bytes_format($n) {
			$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
			$u = 0;
			while ( $n >= 1024 ) {
				$u++;
				$n = $n / 1024;
			}
			return number_format($n, ($u ? 2 : 0), '.', ',') . ' ' . $units[$u];
		}
		
	}
}
