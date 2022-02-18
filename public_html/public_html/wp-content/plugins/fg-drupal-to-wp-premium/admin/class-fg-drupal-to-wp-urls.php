<?php

/**
 * URLs module
 *
 * @link       https://www.fredericgilles.net/drupal-to-wordpress/
 * @since      1.4.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Urls', false) ) {

	/**
	 * URLs class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Urls {

		private $plugin;
		private $url_table = '';
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
		}
		
		/**
		 * Reset the Drupal last imported URL ID
		 *
		 */
		public function reset_urls() {
			update_option('fgd2wp_last_drupal_url_id', 0);
		}
		
		/**
		 * Import the URLs
		 * 
		 */
		public function import_urls() {
			if ( isset($this->plugin->premium_options['skip_redirects']) && $this->plugin->premium_options['skip_redirects'] ) {
				return;
			}
			
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			
			$message = __('Importing redirects...', $this->plugin->get_plugin_name());
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_urls_count());
			} else {
				$this->plugin->log($message);
			}
			$imported_redirect_count = 0;
			$matches = array();
			
			$this->url_table = $this->get_url_table();
			$imported_nodes = $this->plugin->get_imported_drupal_posts_with_post_type();
			$imported_taxonomies = $this->plugin->get_imported_drupal_taxonomies();
			
			do {
				if ( $this->plugin->import_stopped() ) {
					return;
				}
				$urls = $this->get_urls($this->plugin->chunks_size);
				$urls_count = count($urls);

				foreach ( $urls as $url ) {
					// Increment the Drupal last imported URL ID
					update_option('fgd2wp_last_drupal_url_id', $url['id']);
					
					if ( preg_match('#^/?(.*)/(\d+)$#', $url['source'], $matches) ) {
						$drupal_object_type = $matches[1];
						$drupal_object_id = $matches[2];
						$object_id = 0;
						$object_type = '';
						switch ( $drupal_object_type ) {
							case 'node':
								if ( isset($imported_nodes[$drupal_object_id]) ) {
									$object_id = $imported_nodes[$drupal_object_id]['post_id'];
									$object_type = $imported_nodes[$drupal_object_id]['post_type'];
								}
								break;
								
							case 'taxonomy/term':
								if ( isset($imported_taxonomies[$drupal_object_id]) ) {
									$object_id = $imported_taxonomies[$drupal_object_id]['term_id'];
									$object_type = $imported_taxonomies[$drupal_object_id]['taxonomy'];
								}
								break;
						}
						if ( !empty($object_id) && !empty($object_type) ) {
							FG_Drupal_to_WordPress_Redirect::add_redirect($url['alias'], $object_id, $object_type);
							$imported_redirect_count++;
						}
					}
					if ( defined('WP_CLI') ) {
						$progress_cli->tick();
					}
				}
				$this->plugin->progressbar->increment_current_count($urls_count);
				
			} while ( !is_null($urls) && ($urls_count > 0) );
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			$this->plugin->display_admin_notice(sprintf(_n('%d redirect imported', '%d redirects imported', $imported_redirect_count, $this->plugin->get_plugin_name()), $imported_redirect_count));
		}
		
		/**
		 * Get the URL table name
		 * 
		 * @since 2.14.0
		 * 
		 * @return string Table name
		 */
		private function get_url_table() {
			$url_table = '';
			$potential_tables = array('url_alias', 'path_alias');
			foreach ( $potential_tables as $potential_table ) {
				if ( $this->plugin->table_exists($potential_table) ) {
					$url_table = $potential_table;
					break;
				}
			}
			return $url_table;
		}
		
		/**
		 * Get the URLs
		 * 
		 * @param int $limit Number of urls max
		 * @return array of urls
		 */
		private function get_urls($limit=1000) {
			$urls = array();
			if ( !empty($this->url_table) ) {
				$prefix = $this->plugin->plugin_options['prefix'];
				$last_drupal_url_id = (int)get_option('fgd2wp_last_drupal_url_id'); // to restore the import where it left
				
				if ( $this->url_table == 'url_alias' ) {
					$id_field = 'pid';
				} else {
					$id_field = 'id';
				}

				if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
					// Version 6
					$source_field = 'src AS source';
					$alias_field = 'dst AS alias';
				} else {
					// Version 7+
					if ( $this->url_table == 'url_alias' ) {
						$source_field = 'source';
					} else {
						$source_field = 'path AS source';
					}
					$alias_field = 'alias';
				}
				$sql = "
					SELECT u.{$id_field} AS id, u.${source_field}, u.${alias_field}
					FROM ${prefix}{$this->url_table} u
					WHERE u.$id_field > '$last_drupal_url_id'
					ORDER BY u.$id_field
					LIMIT $limit
				";

				$sql = apply_filters('fgd2wp_get_urls_sql', $sql);
				$urls = $this->plugin->drupal_query($sql);
			}
			return $urls;
		}
		
		/**
		 * Update the number of total elements found in Drupal
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_redirects']) || !$this->plugin->premium_options['skip_redirects'] ) {
				$count += $this->get_urls_count();
			}
			return $count;
		}
		
		/**
		 * Get the number of URLs
		 * 
		 * @return int Number of URLs
		 */
		private function get_urls_count() {
			$count = 0;
			$this->url_table = $this->get_url_table();
			if ( !empty($this->url_table) ) {
				$prefix = $this->plugin->plugin_options['prefix'];

				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}{$this->url_table}
				";

				$result = $this->plugin->drupal_query($sql);
				if ( isset($result[0]['nb']) ) {
					$count = $result[0]['nb'];
				}
			}
			return $count;
		}
		
	}
}
