<?php

/**
 * Redirect module
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      1.4.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/public
 */

if ( !class_exists('FG_Drupal_to_WordPress_Redirect', false) ) {

	/**
	 * Redirect class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/public
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Redirect {

		const REDIRECT_TABLE = 'fg_redirect';
		private $canonical_url = '';
		
		/**
		 * Plugin installation
		 */
		static function install() {
			self::create_table_wp();
		}
		
		/**
		 * Create the necessary table
		 */
		private static function create_table_wp() {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$table_name = $wpdb->prefix . self::REDIRECT_TABLE;
			if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
				$sql = "
				CREATE TABLE $table_name (
				  old_url varchar(191) NOT NULL,
				  id bigint(20) unsigned NOT NULL,
				  type varchar(20) NOT NULL,
				  activated tinyint(1) NOT NULL,
				  PRIMARY KEY  (old_url)
				) $charset_collate;
				";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}
		}
		
		/**
		 * Add a redirect in the database
		 *
		 * @param string $old_url
		 * @param int $id
		 * @param string $type
		 */
		public static function add_redirect($old_url, $id, $type) {
			global $wpdb;
			$table_name = $wpdb->prefix . self::REDIRECT_TABLE;
			$wpdb->query($wpdb->prepare("
				INSERT IGNORE INTO $table_name
				(`old_url`, `id`, `type`, `activated`)
				VALUES (%s, %d, '%s', 1)
			", $old_url, $id, $type));
		}

		/**
		 * Empty the redirects table
		 * 
		 */
		public function empty_redirects() {
			global $wpdb;
			$table_name = $wpdb->prefix . self::REDIRECT_TABLE;
			$wpdb->query("TRUNCATE $table_name");
		}
		
		/**
		 * Process the URL
		 *
		 * @global object $wp
		 */
		public function process_url() {
			global $wp;
			
			$premium_options = get_option('fgd2wpp_options');
			$do_redirect = isset($premium_options['url_redirect']) && !empty($premium_options['url_redirect']);
			$do_redirect = apply_filters('fgd2wpp_do_redirect', $do_redirect);
			if ( !$do_redirect ) {
				return;
			}
			if ( !is_404() ) { // A page is found, don't redirect
				return;
			}
			
			$original_url = urldecode($wp->request);
			$urls[] = $original_url;
			
			// Remove the languages prefixes
			$languages = $this->get_active_languages();
			foreach ( $languages as $language ) {
				$url = preg_replace("#^$language/#", '', $original_url);
				if ( !in_array($url, $urls) ) {
					$urls[] = $url;
				}
			}
			foreach ( $urls as $url ) {
				$this->process_one_url($url);
			}
		}
		
		/**
		 * Get the active WPML languages
		 * 
		 * @global object $wpdb
		 * @return array List of language codes
		 */
		private function get_active_languages() {
			global $wpdb;
			$languages = array();
			
			if ( $this->table_exists('icl_languages') ) {
				$prefix = $wpdb->prefix;
				$sql = "
					SELECT l.code
					FROM ${prefix}icl_languages l
					WHERE l.active = 1
				";
				$languages = $wpdb->get_col($sql);
			}
			return $languages;
		}
		
		/**
		 * Test if a table exists in the database
		 * 
		 * @global object $wpdb
		 * @param string $table SQL table
		 * @return bool
		 */
		private function table_exists($table) {
			global $wpdb;
			
			$table_name = $wpdb->prefix . $table;
			return $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
		}
		
		/**
		 * Process one URL
		 *
		 * @param string $url URL
		 */
		private function process_one_url($url) {
			$redirect_object = $this->find_url_in_redirect_table($url);
			$permalink = $this->find_redirect_url($redirect_object);
			if ( !empty($permalink) ) {
				$this->redirect($permalink);
			}
		}
		
		/**
		 * Find the original URL in the redirect table
		 * 
		 * @param string $url
		 * @return object Redirect
		 */
		public function find_url_in_redirect_table($url) {
			$redirect_object = $this->get_redirect($url);
			if ( !isset($redirect_object->id) ) {
				// Try to get the relative URL
				$redirect_object = $this->get_redirect_like(basename($url));
			}
			return $redirect_object;
		}
		
		/**
		 * Get the Post ID from the redirect URL
		 *
		 * @param string $url
		 * @result object (Post ID, post type)
		 */
		private function get_redirect($url) {
			global $wpdb;
			$url = $this->esc_url($url);
			$table_name = $wpdb->prefix . self::REDIRECT_TABLE;
			$sql = "
				SELECT id, type
				FROM $table_name
				WHERE old_url IN (\"$url\", \"$url/\", \"/$url\", \"/$url/\", \"$url\.html\", \"/$url\.html\")
				AND activated = 1";
			$result = $wpdb->get_row($sql);
			return $result;
		}
		
		/**
		 * Escape a URL
		 * Same function as WordPress native function esc_url() but without adding http://
		 * 
		 * @param string $url URL
		 * @return string URL
		 */
		private function esc_url($url) {
			if ( '' == $url ) {
				return $url;
			}
			$url = str_replace( ' ', '%20', $url );
			$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
			return $url;
		}
		
		/**
		 * Get the Post ID from the redirect URL
		 *
		 * @param string $url
		 * @result object (Post ID, post type)
		 */
		private function get_redirect_like($url) {
			global $wpdb;
			$url = $this->esc_url($url);
			$table_name = $wpdb->prefix . self::REDIRECT_TABLE;
			$sql = "
				SELECT id, type
				FROM $table_name
				WHERE old_url LIKE \"%$url\"
				AND activated = 1";
			$result = $wpdb->get_row($sql);
			return $result;
		}
		
		/**
		 * Find the URL to redirect to
		 * 
		 * @param object $redirect_object [ID, type]
		 * @return string URL to redirect to
		 */
		private function find_redirect_url($redirect_object) {
			$permalink = '';
			if ( isset($redirect_object->type) && isset($redirect_object->id) && !empty($redirect_object->id) ) {
				if ( taxonomy_exists($redirect_object->type) ) {
					$permalink = get_term_link(intval($redirect_object->id), $redirect_object->type);
				} else {
					$permalink = get_permalink(intval($redirect_object->id));
				}
			}
			return $permalink;
		}
		
		/**
		 * Load the content of the post or category
		 *
		 * @param object $redirect_object [ID, type]
		 */
		private function load_content($redirect_object) {
			if ( isset($redirect_object->type) && isset($redirect_object->id) && !empty($redirect_object->id) ) {
				if ( taxonomy_exists($redirect_object->type) ) {
					$this->load_taxonomy_content(intval($redirect_object->id), $redirect_object->type);
				} elseif ( $redirect_object->type == 'page') {
					$this->load_page_content(intval($redirect_object->id));
				} else {
					$this->load_post_content(intval($redirect_object->id));
				}
			}
		}
		
		/**
		 * Load the page content in the current WordPress page
		 * 
		 * @global object $wp_query
		 * @global object $post
		 * @param int $page_id Page ID
		 */
		private function load_page_content($page_id) {
			global $wp_query;
			global $post;
			
			$wp_query = new WP_Query(array(
				'page_id' => $page_id,
			));
			$post = $wp_query->post;
		}
		
		/**
		 * Load the post content in the current WordPress page
		 * 
		 * @global object $wp_query
		 * @global object $post
		 * @param int $post_id Post ID
		 */
		private function load_post_content($post_id) {
			global $wp_query;
			global $post;
			
			$wp_query = new WP_Query(array(
				'p' => $post_id,
				'post_type' => 'any'
			));
			$post = $wp_query->post;
		}
		
		/**
		 * Load the taxonomy content in the current WordPress page
		 *
		 * @global object $wp_query
		 * @param int $term_id Term ID
		 * @param string $taxonomy Taxonomy (category, product_cat, …)
		 */
		private function load_taxonomy_content($term_id, $taxonomy) {
			global $wp_query;
			
			$wp_query = new WP_Query(array('tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'terms'    => $term_id,
				),
			)));
		}
		
		/**
		 * Add a canonical tag to avoid duplicate content
		 * 
		 * @param string $permalink
		 */
		private function add_canonical_tag($permalink) {
			if ( !empty($permalink) ) {
				$this->canonical_url = $permalink;
				add_action('wp_head', array($this, 'add_canonical'));
			}
		}
		
		/**
		 * Add a canonical meta tag
		 *
		 */
		public function add_canonical() {
			echo '<link rel="canonical" href="' . esc_attr($this->canonical_url) . '" />' . "\n";
		}
		
		/**
		 * Redirect to the permalink
		 * 
		 * @param string $permalink
		 */
		private function redirect($permalink) {
			if ( !empty($permalink) && !is_wp_error($permalink) ) {
				$protocol = is_ssl()? 'https' : 'http';
				$host = $_SERVER['HTTP_HOST'];
				$url = $_SERVER['REQUEST_URI'];
				$current_url = "{$protocol}://{$host}{$url}"; // to avoid endless loop
				if ( $permalink != $current_url ) {
					wp_redirect($permalink, 301);
					exit;
				}
			}
		}
		
		/**
		 * Get all the imported redirects
		 * 
		 * @since 1.82.0
		 * 
		 * @return array Redirects
		 */
		public function get_imported_redirects() {
			global $wpdb;
			$redirects = array();
			
			$table_name = $wpdb->prefix . self::REDIRECT_TABLE;
			$sql = "
				SELECT old_url, id, type
				FROM $table_name
			";
			$result = $wpdb->get_results($sql, ARRAY_A);
			foreach ( $result as $row ) {
				$redirects[$row['old_url']] = $row;
			}
			return $redirects;
		}
		
	}
}
