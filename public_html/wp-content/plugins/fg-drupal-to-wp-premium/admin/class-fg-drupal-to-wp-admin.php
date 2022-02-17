<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/fg-drupal-to-wp/
 * @since      1.0.0
 *
 * @package    FG_Drupal_to_WordPress
 * @subpackage FG_Drupal_to_WordPress/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_Admin', false) ) {

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @package    FG_Drupal_to_WordPress
	 * @subpackage FG_Drupal_to_WordPress/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_Admin extends WP_Importer {

		/**
		 * The ID of this plugin.
		 *
		 * @since      1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since      1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;					// Plugin version
		private $importer = 'fgd2wp';		// URL parameter

		public $drupal_version;
		public $plugin_options;				// Plug-in options
		public $download_manager;			// Download Manager
		public $progressbar;
		public $imported_media = array();
		public $imported_taxonomies = array();
		public $taxonomy_term_hierarchy = array(); // Terms with their parent
		public $chunks_size = 10;
		public $media_count = 0;			// Number of imported medias
		public $links_count = 0;			// Number of links modified
		public $file_public_path = '';		// Drupal public medias directory
		public $file_private_path = '';		// Drupal private medias directory
		public $taxonomies_enabled = true;
		public $comments_enabled = true;

		protected $faq_url;					// URL of the FAQ page
		protected $notices = array();		// Error or success messages
		
		private $log_file;
		private $log_file_url;
		private $test_antiduplicate = false;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since      1.0.0
		 * @param    string    $plugin_name       The name of this plugin.
		 * @param    string    $version           The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version = $version;
			$this->faq_url = 'https://wordpress.org/plugins/fg-drupal-to-wp/faq/';
			$upload_dir = wp_upload_dir();
			$this->log_file = $upload_dir['basedir'] . '/' . $this->plugin_name . '.logs';
			$this->log_file_url = $upload_dir['baseurl'] . '/' . $this->plugin_name . '.logs';
			// Replace the protocol if the WordPress address is wrong in the WordPress General settings
			if ( is_ssl() ) {
				$this->log_file_url = preg_replace('/^https?/', 'https', $this->log_file_url);
			}

			// Progress bar
			$this->progressbar = new FG_Drupal_to_WordPress_ProgressBar($this);

		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since      1.0.0
		 * @return     string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since      1.0.0
		 */
		public function enqueue_styles() {

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fg-drupal-to-wp-admin.css', array(), $this->version, 'all' );

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since      1.0.0
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fg-drupal-to-wp-admin.js', array( 'jquery', 'jquery-ui-progressbar' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'objectL10n', array(
				'delete_imported_data_confirmation_message' => __( 'All previously imported data will be deleted from WordPress.', 'fg-drupal-to-wp' ),
				'delete_all_confirmation_message' => __( 'All content will be deleted from WordPress.', 'fg-drupal-to-wp' ),
				'delete_no_answer_message' => __( 'Please select a remove option.', 'fg-drupal-to-wp' ),
				'import_completed' => __( 'IMPORT COMPLETED', 'fg-drupal-to-wp' ),
				'content_removed_from_wordpress' => __( 'Content removed from WordPress', 'fg-drupal-to-wp' ),
				'settings_saved' => __( 'Settings saved', 'fg-drupal-to-wp' ),
				'importing' => __( 'Importing…', 'fg-drupal-to-wp' ),
				'import_stopped_by_user' => __( 'IMPORT STOPPED BY USER', 'fg-drupal-to-wp' ),
				'internal_links_modified' => __( 'Internal links modified', 'fg-drupal-to-wp' ),
			) );
			wp_localize_script( $this->plugin_name, 'objectPlugin', array(
				'log_file_url' => $this->log_file_url,
				'progress_url' => $this->progressbar->get_url(),
			));

		}

		/**
		 * Initialize the plugin
		 */
		public function init() {
			register_importer($this->importer, __('Drupal', 'fg-drupal-to-wp'), __('Import a Drupal database into WordPress.', 'fg-drupal-to-wp'), array($this, 'importer'));
		}

		/**
		 * Display the stored notices
		 */
		public function display_notices() {
			foreach ( $this->notices as $notice ) {
				echo '<div class="' . $notice['level'] . '"><p>[' . $this->plugin_name . '] ' . $notice['message'] . "</p></div>\n";
			}
		}
		
		/**
		 * Write a message in the log file
		 * 
		 * @param string $message
		 */
		public function log($message) {
			file_put_contents($this->log_file, "$message\n", FILE_APPEND);
		}
		
		/**
		 * Store an admin notice
		 */
		public function display_admin_notice( $message )	{
			$this->notices[] = array('level' => 'updated', 'message' => $message);
			error_log('[INFO] [' . $this->plugin_name . '] ' . $message);
			$this->log($message);
			if ( defined('WP_CLI') && WP_CLI ) {
				WP_CLI::log($message);
			}
		}

		/**
		 * Store an admin error
		 */
		public function display_admin_error( $message )	{
			$this->notices[] = array('level' => 'error', 'message' => $message);
			error_log('[ERROR] [' . $this->plugin_name . '] ' . $message);
			$this->log('[ERROR] ' . $message);
			if ( defined('WP_CLI') && WP_CLI ) {
				WP_CLI::error($message, false);
			}
		}

		/**
		 * Store an admin warning
		 */
		public function display_admin_warning( $message )	{
			$this->notices[] = array('level' => 'error', 'message' => $message);
			error_log('[WARNING] [' . $this->plugin_name . '] ' . $message);
			$this->log('[WARNING] ' . $message);
			if ( defined('WP_CLI') && WP_CLI ) {
				WP_CLI::warning($message);
			}
		}

		/**
		 * Run the importer
		 */
		public function importer() {
			$feasible_actions = array(
				'empty',
				'save',
				'test_database',
				'test_download',
				'import',
				'modify_links',
			);
			$action = '';
			foreach ( $feasible_actions as $potential_action ) {
				if ( isset($_POST[$potential_action]) ) {
					$action = $potential_action;
					break;
				}
			}
			$this->set_plugin_options();
			$this->set_local_timezone();
			$this->dispatch($action);
			$this->display_admin_page(); // Display the admin page
		}
		
		/**
		 * Import triggered by AJAX
		 *
		 * @since      1.0.0
		 */
		public function ajax_importer() {
			$current_user = wp_get_current_user();
			if ( !empty($current_user) && $current_user->has_cap('import') ) {
				$action = filter_input(INPUT_POST, 'plugin_action', FILTER_SANITIZE_STRING);

				$this->set_plugin_options();
			
				if ( $action == 'update_wordpress_info') {
					// Update the WordPress database info
					echo $this->get_database_info();

				} else {
					ini_set('display_errors', true); // Display the errors that may happen (ex: Allowed memory size exhausted)

					// Empty the log file if we empty the WordPress content
					if ( ($action == 'empty') || (($action == 'import') && filter_input(INPUT_POST, 'automatic_empty', FILTER_VALIDATE_BOOLEAN)) ) {
						$this->empty_log_file();
					}

					$this->set_local_timezone();
					$time_start = date('Y-m-d H:i:s');
					$this->display_admin_notice("=== START $action $time_start ===");
					$result = $this->dispatch($action);
					if ( !empty($result) ) {
						echo json_encode($result); // Send the result to the AJAX caller
					}
					$time_end = date('Y-m-d H:i:s');
					$this->display_admin_notice("=== END $action $time_end ===\n");
				}
			}
			wp_die();
		}
		
		/**
		 * Set the plugin options
		 * 
		 * @since 3.0.2
		 */
		public function set_plugin_options() {
			// Default values
			$this->plugin_options = array(
				'automatic_empty'			=> 0,
				'url'						=> null,
				'download_protocol'			=> 'http',
				'base_dir'					=> '',
				'driver'					=> 'mysql',
				'hostname'					=> 'localhost',
				'port'						=> 3306,
				'database'					=> null,
				'username'					=> 'root',
				'password'					=> '',
				'sqlite_file'				=> '',
				'prefix'					=> '',
				'summary'					=> 'in_content',
				'skip_media'				=> 0,
				'file_public_path_source'	=> 'default',
				'file_public_path'			=> 'sites/default/files',
				'file_private_path_source'	=> 'default',
				'file_private_path'			=> 'sites/default/private/files',
				'featured_image'			=> 'featured',
				'only_featured_image'		=> 0,
				'remove_first_image'		=> 0,
				'skip_thumbnails'			=> 0,
				'import_external'			=> 0,
				'import_duplicates'			=> 0,
				'force_media_import'		=> 0,
				'timeout'					=> 20,
				'logger_autorefresh'		=> 1,
			);
			$options = get_option('fgd2wp_options');
			if ( is_array($options) ) {
				$this->plugin_options = array_merge($this->plugin_options, $options);
			}
			
			do_action('fgd2wp_set_plugin_options');
		}
		
		/**
		 * Empty the log file
		 * 
		 * @since 2.26.0
		 */
		public function empty_log_file() {
			file_put_contents($this->log_file, '');
		}
		
		/**
		 * Set the local timezone
		 * 
		 * @since 2.22.0
		 */
		public function set_local_timezone() {
			// Set the time zone
			$timezone = get_option('timezone_string');
			if ( !empty($timezone) ) {
				date_default_timezone_set($timezone);
			}
		}
		
		/**
		 * Dispatch the actions
		 * 
		 * @param string $action Action
		 * @return object Result to return to the caller
		 */
		public function dispatch($action) {
			$timeout = defined('IMPORT_TIMEOUT')? IMPORT_TIMEOUT : 7200; // 2 hours
			set_time_limit($timeout);

			// Suspend the cache during the migration to avoid exhausted memory problem
			wp_suspend_cache_addition(true);
			wp_suspend_cache_invalidation(true);

			// Check if the upload directory is writable
			$upload_dir = wp_upload_dir();
			if ( !is_writable($upload_dir['basedir']) ) {
				$this->display_admin_error(__('The wp-content directory must be writable.', 'fg-drupal-to-wp'));
			}

			// Requires at least WordPress 4.4
			if ( version_compare(get_bloginfo('version'), '4.4', '<') ) {
				$this->display_admin_error(sprintf(__('WordPress 4.4+ is required. Please <a href="%s">update WordPress</a>.', 'fg-drupal-to-wp'), admin_url('update-core.php')));
			}
			
			else {
				do_action('fgd2wp_pre_dispatch');
				
				if ( !empty($action) ) {
					switch($action) {

						// Delete content
						case 'empty':
							if ( defined('WP_CLI') || check_admin_referer( 'empty', 'fgd2wp_nonce_empty' ) ) { // Security check
								if ($this->empty_database($_POST['empty_action'])) { // Empty WP database
									$this->display_admin_notice(__('WordPress content removed', 'fg-drupal-to-wp'));
								} else {
									$this->display_admin_error(__('Couldn\'t remove content', 'fg-drupal-to-wp'));
								}
								wp_cache_flush();
							}
							break;

						// Save database options
						case 'save':
							if ( check_admin_referer( 'parameters_form', 'fgd2wp_nonce' ) ) { // Security check
								$this->save_plugin_options();
								$this->display_admin_notice(__('Settings saved', 'fg-drupal-to-wp'));
							}
							break;

						// Test the database connection
						case 'test_database':
							if ( defined('WP_CLI') || check_admin_referer( 'parameters_form', 'fgd2wp_nonce' ) ) { // Security check
								if ( !defined('WP_CLI') ) {
									// Save database options
									$this->save_plugin_options();
								}

								if ( $this->test_database_connection() ) {
									return apply_filters('fgd2wp_database_connection_successful', array('status' => 'OK', 'message' => __('Connection successful', 'fg-drupal-to-wp')));
								} else {
									return array('status' => 'Error', 'message' => __('Connection failed', 'fg-drupal-to-wp') . '<br />' . __('See the errors in the log below', 'fg-drupal-to-wp'));
								}
							}
							break;

						// Test the media connection
						case 'test_download':
							if ( defined('WP_CLI') || check_admin_referer( 'parameters_form', 'fgd2wp_nonce' ) ) { // Security check
								if ( !defined('WP_CLI') ) {
									// Save database options
									$this->save_plugin_options();
								}

								$protocol = $this->plugin_options['download_protocol'];
								$protocol_upcase = strtoupper(str_replace('_', ' ', $protocol));
								$this->download_manager = new FG_Drupal_to_WordPress_Download($this, $protocol);
								if ( $this->download_manager->test_connection() ) {
									return array('status' => 'OK', 'message' => sprintf(__('%s connection successful', 'fg-drupal-to-wp'), $protocol_upcase));
								} else {
									return array('status' => 'Error', 'message' => sprintf(__('%s connection failed', 'fg-drupal-to-wp'), $protocol_upcase));
								}
							}
							break;

						// Run the import
						case 'import':
							if ( defined('WP_CLI') || defined('DOING_CRON') || check_admin_referer( 'parameters_form', 'fgd2wp_nonce' ) ) { // Security check
								if ( !defined('DOING_CRON') && !defined('WP_CLI') ) {
									// Save database options
									$this->save_plugin_options();
								} else {
									if ( defined('DOING_CRON') ) {
										// CRON triggered
										$this->plugin_options['automatic_empty'] = 0; // Don't delete the existing data when triggered by cron
									}
								}

								if ( $this->test_database_connection() ) {
									// Automatic empty
									if ( $this->plugin_options['automatic_empty'] ) {
										if ($this->empty_database('all')) {
											$this->display_admin_notice(__('WordPress content removed', 'fg-drupal-to-wp'));
										} else {
											$this->display_admin_error(__('Couldn\'t remove content', 'fg-drupal-to-wp'));
										}
										wp_cache_flush();
									}

									// Import content
									$this->import();
								}
							}
							break;

						// Stop the import
						case 'stop_import':
							if ( check_admin_referer( 'parameters_form', 'fgd2wp_nonce' ) ) { // Security check
								$this->stop_import();
							}
							break;

						// Modify internal links
						case 'modify_links':
							if ( defined('WP_CLI') || check_admin_referer( 'modify_links', 'fgd2wp_nonce_modify_links' ) ) { // Security check
								$this->modify_links();
								$this->display_admin_notice(sprintf(_n('%d internal link modified', '%d internal links modified', $this->links_count, 'fg-drupal-to-wp'), $this->links_count));
							}
							break;

						default:
							// Do other actions
							do_action('fgd2wp_dispatch', $action);
					}
				}
			}
		}

		/**
		 * Display the admin page
		 * 
		 */
		private function display_admin_page() {
			$data = $this->plugin_options;

			$data['importer'] = $this->importer;
			$data['title'] = __('Import Drupal', 'fg-drupal-to-wp');
			$data['description'] = __('This plugin will import articles, stories, pages, images, categories and tags from a Drupal database into WordPress.<br />Compatible with Drupal versions 4, 5, 6, 7, 8 and 9.', 'fg-drupal-to-wp');
			$data['description'] .= "<br />\n" . sprintf(__('For any issue, please read the <a href="%s" target="_blank">FAQ</a> first.', 'fg-drupal-to-wp'), $this->faq_url);
			$data['database_info'] = $this->get_database_info();

			$data['tab'] = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);
			
			// Hook for modifying the admin page
			$data = apply_filters('fgd2wp_pre_display_admin_page', $data);

			// Load the CSS and Javascript
			$this->enqueue_styles();
			$this->enqueue_scripts();
			
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/admin-display.php';

			// Hook for doing other actions after displaying the admin page
			do_action('fgd2wp_post_display_admin_page');

		}

		/**
		 * Get the WP options name
		 * 
		 * @since 2.0.0
		 * 
		 * @param array $option_names Option names
		 * @return array Option names
		 */
		public function get_option_names($option_names) {
			$option_names[] = 'fgd2wp_options';
			return $option_names;
		}
		
		/**
		 * Get the WordPress database info
		 * 
		 * @return string Database info
		 */
		private function get_database_info() {
			$cat_count = wp_count_terms('category', array('hide_empty' => 0));
			$tags_count = wp_count_terms('post_tag', array('hide_empty' => 0));
			$posts_count = $this->count_posts('post');
			$pages_count = $this->count_posts('page');
			$media_count = $this->count_posts('attachment');

			$database_info =
				sprintf(_n('%d category', '%d categories', $cat_count, 'fg-drupal-to-wp'), $cat_count) . "<br />" .
				sprintf(_n('%d tag', '%d tags', $tags_count, 'fg-drupal-to-wp'), $tags_count) . "<br />" .
				sprintf(_n('%d post', '%d posts', $posts_count, 'fg-drupal-to-wp'), $posts_count) . "<br />" .
				sprintf(_n('%d page', '%d pages', $pages_count, 'fg-drupal-to-wp'), $pages_count) . "<br />" .
				sprintf(_n('%d media', '%d medias', $media_count, 'fg-drupal-to-wp'), $media_count) . "<br />";
			$database_info = apply_filters('fgd2wp_get_database_info', $database_info);
			return $database_info;
		}
		
		/**
		 * Count the number of posts for a post type
		 * 
		 * @param string $post_type Post type
		 */
		public function count_posts($post_type) {
			$count = 0;
			$excluded_status = array('trash', 'auto-draft');
			$tab_count = wp_count_posts($post_type);
			foreach ( $tab_count as $key => $value ) {
				if ( !in_array($key, $excluded_status) ) {
					$count += $value;
				}
			}
			return $count;
		}

		/**
		 * Add an help tab
		 * 
		 */
		public function add_help_tab() {
			$screen = get_current_screen();
			$screen->add_help_tab(array(
				'id'	=> 'fgd2wp_help_instructions',
				'title'	=> __('Instructions', 'fg-drupal-to-wp'),
				'content'	=> '',
				'callback' => array($this, 'help_instructions'),
			));
			$screen->add_help_tab(array(
				'id'	=> 'fgd2wp_help_options',
				'title'	=> __('Options', 'fg-drupal-to-wp'),
				'content'	=> '',
				'callback' => array($this, 'help_options'),
			));
			$screen->set_help_sidebar('<a href="' . $this->faq_url . '" target="_blank">' . __('FAQ', 'fg-drupal-to-wp') . '</a>');
		}

		/**
		 * Instructions help screen
		 * 
		 * @return string Help content
		 */
		public function help_instructions() {
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-instructions.tpl.php';
		}

		/**
		 * Options help screen
		 * 
		 * @return string Help content
		 */
		public function help_options() {
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-options.tpl.php';
		}

		/**
		 * Open the connection on Drupal database
		 *
		 * return boolean Connection successful or not
		 */
		public function drupal_connect() {
			global $drupal_db;

			if ( !class_exists('PDO') ) {
				$this->display_admin_error(__('PDO is required. Please enable it.', 'fg-drupal-to-wp'));
				return false;
			}
			try {
				switch ( $this->plugin_options['driver'] ) {
					case 'mysql':
						// MySQL
						$drupal_db = new PDO('mysql:host=' . $this->plugin_options['hostname'] . ';port=' . $this->plugin_options['port'] . ';dbname=' . $this->plugin_options['database'], $this->plugin_options['username'], $this->plugin_options['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
						break;
					case 'postgresql':
						// PostgreSQL
						$drupal_db = new PDO('pgsql:host=' . $this->plugin_options['hostname'] . ';port=' . $this->plugin_options['port'] . ';dbname=' . $this->plugin_options['database'], $this->plugin_options['username'], $this->plugin_options['password']);
						break;
					case 'sqlite':
						// SQLite
						if ( !file_exists($this->plugin_options['sqlite_file']) ) {
							$this->display_admin_error(__("Couldn't read the Drupal database SQLite file: ", 'fg-drupal-to-wp') . $this->plugin_options['sqlite_file']);
							return false;
						}
						$drupal_db = new PDO('sqlite:' . $this->plugin_options['sqlite_file']);
						break;
				}
				if ( function_exists('wp_get_environment_type') && (wp_get_environment_type() == 'development') && $drupal_db ) {
					$drupal_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Display SQL errors
				}
			} catch ( PDOException $e ) {
				$this->display_admin_error(__('Couldn\'t connect to the Drupal database. Please check your parameters. And be sure the WordPress server can access the Drupal database.', 'fg-drupal-to-wp') . "<br />\n" . $e->getMessage() . "<br />\n" . sprintf(__('Please read the <a href="%s" target="_blank">FAQ for the solution</a>.', 'fg-drupal-to-wp'), $this->faq_url));
				return false;
			}
			$this->drupal_version = $this->drupal_version();
			return true;
		}

		/**
		 * Execute a SQL query on the Drupal database
		 * 
		 * @param string $sql SQL query
		 * @param bool $display_error Display the error?
		 * @return array Query result
		 */
		public function drupal_query($sql, $display_error = true) {
			global $drupal_db;
			$result = array();

			try {
				$query = $drupal_db->query($sql, PDO::FETCH_ASSOC);
				if ( is_object($query) ) {
					foreach ( $query as $row ) {
						$result[] = $row;
					}
				}

			} catch ( PDOException $e ) {
				if ( $display_error ) {
					$this->display_admin_error(__('Error:', 'fg-drupal-to-wp') . $e->getMessage());
				}
			}
			return $result;
		}

		/**
		 * Delete all posts, medias and categories from the database
		 *
		 * @param string $action	imported = removes only new imported data
		 * 							all = removes all
		 * @return boolean
		 */
		private function empty_database($action) {
			global $wpdb;
			$result = true;

			$wpdb->show_errors();

			// Hook for doing other actions before emptying the database
			do_action('fgd2wp_pre_empty_database', $action);

			$sql_queries = array();

			if ( $action == 'all' ) {
				// Remove all content
				
				$this->save_wp_data();
				
				$sql_queries[] = "TRUNCATE $wpdb->commentmeta";
				$sql_queries[] = "TRUNCATE $wpdb->comments";
				$sql_queries[] = "TRUNCATE $wpdb->term_relationships";
				$sql_queries[] = "TRUNCATE $wpdb->termmeta";
				$sql_queries[] = "TRUNCATE $wpdb->postmeta";
				$sql_queries[] = "TRUNCATE $wpdb->posts";
				$sql_queries[] = <<<SQL
-- Delete Terms
DELETE FROM $wpdb->terms
WHERE term_id > 1 -- non-classe
SQL;
				$sql_queries[] = <<<SQL
-- Delete Terms taxonomies
DELETE FROM $wpdb->term_taxonomy
WHERE term_id > 1 -- non-classe
SQL;
				$sql_queries[] = "ALTER TABLE $wpdb->terms AUTO_INCREMENT = 2";
				$sql_queries[] = "ALTER TABLE $wpdb->term_taxonomy AUTO_INCREMENT = 2";
				
			} else {
				
				// (Re)create a temporary table with the IDs to delete
				$sql_queries[] = <<<SQL
DROP TEMPORARY TABLE IF EXISTS {$wpdb->prefix}fg_data_to_delete;
SQL;

				$sql_queries[] = <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS {$wpdb->prefix}fg_data_to_delete (
`id` bigint(20) unsigned NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
SQL;
				
				// Insert the imported posts IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT post_id FROM $wpdb->postmeta
WHERE meta_key LIKE '_fgd2wp_%'
SQL;
				
				// Delete the imported posts and related data

				$sql_queries[] = <<<SQL
-- Delete Comments and Comment metas
DELETE c, cm
FROM $wpdb->comments c
LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = c.comment_ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE c.comment_post_ID = del.id;
SQL;

				$sql_queries[] = <<<SQL
-- Delete Term relashionships
DELETE tr
FROM $wpdb->term_relationships tr
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE tr.object_id = del.id;
SQL;

				$sql_queries[] = <<<SQL
-- Delete Posts Children and Post metas
DELETE p, pm
FROM $wpdb->posts p
LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE p.post_parent = del.id
AND p.post_type != 'attachment'; -- Don't remove the old medias attached to posts
SQL;

				$sql_queries[] = <<<SQL
-- Delete Posts and Post metas
DELETE p, pm
FROM $wpdb->posts p
LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE p.ID = del.id;
SQL;

				// Truncate the temporary table
				$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;
				
				// Insert the imported terms IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT term_id FROM $wpdb->termmeta
WHERE meta_key LIKE '_fgd2wp_%'
SQL;
				
				// Delete the imported terms and related data

				$sql_queries[] = <<<SQL
-- Delete Terms, Term taxonomies and Term metas
DELETE t, tt, tm
FROM $wpdb->terms t
LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id
LEFT JOIN $wpdb->termmeta tm ON tm.term_id = t.term_id
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE t.term_id = del.id;
SQL;

				// Truncate the temporary table
				$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;
				
				// Insert the imported comments IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT comment_id FROM $wpdb->commentmeta
WHERE meta_key LIKE '_fgd2wp_%'
SQL;
				
				// Delete the imported comments and related data
				$sql_queries[] = <<<SQL
-- Delete Comments and Comment metas
DELETE c, cm
FROM $wpdb->comments c
LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = c.comment_ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE c.comment_ID = del.id;
SQL;

			}

			// Execute SQL queries
			if ( count($sql_queries) > 0 ) {
				foreach ( $sql_queries as $sql ) {
					$result &= $wpdb->query($sql);
				}
			}

			if ( $action == 'all' ) {
				$this->restore_wp_data();
			}
				
			// Hook for doing other actions after emptying the database
			do_action('fgd2wp_post_empty_database', $action);

			// Drop the temporary table
			$wpdb->query("DROP TEMPORARY TABLE IF EXISTS {$wpdb->prefix}fg_data_to_delete;");
				
			// Reset the Drupal import counters
			update_option('fgd2wp_last_node_article_id', 0);
			update_option('fgd2wp_last_node_story_id', 0);
			update_option('fgd2wp_last_node_post_id', 0);
			update_option('fgd2wp_last_node_page_id', 0);
			update_option('fgd2wp_last_taxonomy_categories_id', 0);
			update_option('fgd2wp_last_taxonomy_tags_id', 0);

			// Re-count categories and tags items
			$this->terms_count();

			// Update cache
			$this->clean_cache(array(), 'category');
			$this->clean_cache(array(), 'post_tag');
			delete_transient('wc_count_comments');
			
			$this->optimize_database();

			$this->progressbar->set_total_count(0);
			
			$wpdb->hide_errors();
			return ($result !== false);
		}

		/**
		 * Save the data used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function save_wp_data() {
			$this->save_wp_posts();
			$this->save_wp_terms();
			$this->save_wp_term_relationships();
		}
		
		/**
		 * Save the posts and post meta used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function save_wp_posts() {
			global $wpdb;
			$sql = "
				SELECT *
				FROM {$wpdb->posts} p
				WHERE p.`post_type` LIKE 'wp\_%'
				ORDER BY p.`ID`
			";
			$posts = $wpdb->get_results($sql, ARRAY_A);
			foreach ( $posts as &$post ) {
				$sql_meta = "SELECT `meta_key`, `meta_value` FROM {$wpdb->postmeta} WHERE `post_id` = %d ORDER BY `meta_id`";
				$postmetas = $wpdb->get_results($wpdb->prepare($sql_meta, $post['ID']), ARRAY_A);
				$post['meta'] = $postmetas;
				unset($post['ID']);
			}
			update_option('fgd2wp_save_posts', $posts);
		}

		/**
		 * Save the terms, term taxonomies and term meta used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function save_wp_terms() {
			global $wpdb;
			$sql = "
				SELECT t.term_id, t.name, t.slug, tt.taxonomy, tt.description, tt.count
				FROM {$wpdb->terms} t
				INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
				WHERE tt.`taxonomy` LIKE 'wp\_%'
				ORDER BY t.term_id
			";
			$terms = $wpdb->get_results($sql, ARRAY_A);
			foreach ( $terms as &$term ) {
				$sql_meta = "SELECT `meta_key`, `meta_value` FROM {$wpdb->termmeta} WHERE `term_id` = %d ORDER BY `meta_id`";
				$termmetas = $wpdb->get_results($wpdb->prepare($sql_meta, $term['term_id']), ARRAY_A);
				$term['meta'] = $termmetas;
				unset($term['term_id']);
			}
			update_option('fgd2wp_save_terms', $terms);
		}

		/**
		 * Save the terms relationships used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function save_wp_term_relationships() {
			global $wpdb;
			$sql = "
				SELECT p.post_name, t.name AS term_name
				FROM {$wpdb->term_relationships} tr
				INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
				INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
				WHERE p.`post_type` LIKE 'wp\_%'
			";
			$term_relationships = $wpdb->get_results($sql, ARRAY_A);
			update_option('fgd2wp_save_term_relationships', $term_relationships);
		}

		/**
		 * Restore the saved data used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function restore_wp_data() {
			$this->restore_wp_posts();
			$this->restore_wp_terms();
			$this->restore_wp_term_relationships();
		}
		
		/**
		 * Restore the saved posts and post meta used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function restore_wp_posts() {
			global $wpdb;
			$posts = get_option('fgd2wp_save_posts');
			foreach ( $posts as $post ) {
				$postmetas = $post['meta'];
				unset($post['meta']);
				$wpdb->insert($wpdb->posts, $post);
				$post_id = $wpdb->insert_id;
				if ( $post_id ) {
					foreach ( $postmetas as $meta ) {
						add_post_meta($post_id, $meta['meta_key'], $meta['meta_value']);
					}
				}
			}
		}

		/**
		 * Restore the saved terms, term taxonomies and term meta used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function restore_wp_terms() {
			global $wpdb;
			$terms = get_option('fgd2wp_save_terms');
			foreach ( $terms as $term ) {
				$wpdb->insert($wpdb->terms, array(
					'name' => $term['name'],
					'slug' => $term['slug'],
				));
				$term_id = $wpdb->insert_id;
				if ( $term_id ) {
					$wpdb->insert($wpdb->term_taxonomy, array(
						'term_id' => $term_id,
						'taxonomy' => $term['taxonomy'],
						'description' => $term['description'],
						'count' => $term['count'],
					));
					foreach ( $term['meta'] as $meta ) {
						add_term_meta($term_id, $meta['meta_key'], $meta['meta_value']);
					}
				}
			}
		}
		
		/**
		 * Restore the saved term relationships used by the theme (WP 5.9)
		 * 
		 * @since 3.18.0
		 */
		private function restore_wp_term_relationships() {
			global $wpdb;
			$term_relationships = get_option('fgd2wp_save_term_relationships');
			foreach ( $term_relationships as $term_relationship ) {
				$post_id = $wpdb->get_var($wpdb->prepare("SELECT `ID` FROM {$wpdb->posts} WHERE post_name = %s", $term_relationship['post_name']));
				$term_taxonomy_id = $wpdb->get_var($wpdb->prepare("SELECT tt.`term_taxonomy_id` FROM {$wpdb->term_taxonomy} tt INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id WHERE t.name = %s", $term_relationship['term_name']));
				if ( $post_id && $term_taxonomy_id ) {
					$wpdb->insert($wpdb->term_relationships, array(
						'object_id' => $post_id,
						'term_taxonomy_id' => $term_taxonomy_id,
					));
				}
			}
		}

		/**
		 * Optimize the database
		 *
		 */
		protected function optimize_database() {
			global $wpdb;

			$sql = <<<SQL
OPTIMIZE TABLE 
`$wpdb->commentmeta`,
`$wpdb->comments`,
`$wpdb->options`,
`$wpdb->postmeta`,
`$wpdb->posts`,
`$wpdb->terms`,
`$wpdb->term_relationships`,
`$wpdb->term_taxonomy`,
`$wpdb->termmeta`
SQL;
			$wpdb->query($sql);
		}

		/**
		 * Test the database connection
		 * 
		 * @return boolean
		 */
		public function test_database_connection() {
			global $drupal_db;

			if ( $this->drupal_connect() ) {
				// Test that the "node" table exists
				if ( $this->table_exists('node') ) {
					$this->display_admin_notice(__('Connected with success to the Drupal database', 'fg-drupal-to-wp'));

					do_action('fgd2wp_post_test_database_connection');
					return true;
				} else {
					$this->display_admin_error(__('Couldn\'t connect to the Drupal database. Please check the Drupal table prefix.', 'fg-drupal-to-wp') . "<br />\n" . sprintf(__('Please read the <a href="%s" target="_blank">FAQ for the solution</a>.', 'fg-drupal-to-wp'), $this->faq_url));
				}
			}
			$drupal_db = null;
			return false;
		}

		/**
		 * Get some Drupal information
		 *
		 */
		public function get_drupal_info() {
			$this->taxonomies_enabled = version_compare($this->drupal_version, '7', '<')? $this->table_exists('vocabulary') : $this->table_exists('taxonomy_term_data');
			$this->comments_enabled = version_compare($this->drupal_version, '7', '<')? $this->table_exists('comments') : $this->table_exists('comment');
			
			$message = __('Drupal data found:', 'fg-drupal-to-wp') . "\n";

			// Articles
			$articles_count = $this->get_nodes_count('article');
			$message .= sprintf(_n('%d article', '%d articles', $articles_count, 'fg-drupal-to-wp'), $articles_count) . "\n";

			// Stories
			$stories_count = $this->get_nodes_count('story');
			if ( $stories_count > 0 ) {
				$message .= sprintf(_n('%d story', '%d stories', $stories_count, 'fg-drupal-to-wp'), $stories_count) . "\n";
			}

			// Posts
			$posts_count = $this->get_nodes_count('post');
			if ( $posts_count > 0 ) {
				$message .= sprintf(_n('%d post', '%d posts', $posts_count, 'fg-drupal-to-wp'), $posts_count) . "\n";
			}

			// Pages
			$pages_count = $this->get_nodes_count('page');
			$message .= sprintf(_n('%d page', '%d pages', $pages_count, 'fg-drupal-to-wp'), $pages_count) . "\n";

			// Categories
			$cat_count = $this->get_taxonomies_terms_count('categories');
			$message .= sprintf(_n('%d category', '%d categories', $cat_count, 'fg-drupal-to-wp'), $cat_count) . "\n";

			// Tags
			$tags_count = $this->get_taxonomies_terms_count('tags');
			$message .= sprintf(_n('%d tag', '%d tags', $tags_count, 'fg-drupal-to-wp'), $tags_count) . "\n";

			$message = apply_filters('fgd2wp_pre_display_drupal_info', $message);

			$this->display_admin_notice($message);
		}

		/**
		 * Get the number of Drupal taxonomies terms
		 * 
		 * @param string $taxonomy Taxonomy name (categories, tags)
		 * @return int Number of taxonomies terms
		 */
		public function get_taxonomies_terms_count($taxonomy) {
			if ( !$this->taxonomies_enabled) {
				return 0;
			}
			$prefix = $this->plugin_options['prefix'];
			$taxonomy = esc_sql($taxonomy);
			if ( version_compare($this->drupal_version, '7', '<') ) {
				// Drupal 6
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}term_data t
					INNER JOIN ${prefix}vocabulary tv ON tv.vid = t.vid
					WHERE tv.name = '$taxonomy'
				";
			} elseif ( version_compare($this->drupal_version, '8', '<') ) {
				// Drupal 7
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}taxonomy_term_data t
					INNER JOIN ${prefix}taxonomy_vocabulary tv ON tv.vid = t.vid
					WHERE tv.machine_name = '$taxonomy'
				";
			} else {
				// Drupal 8
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}taxonomy_term_field_data t
					INNER JOIN ${prefix}taxonomy_term_data td ON td.tid = t.tid AND td.langcode = t.langcode
					WHERE t.vid = '$taxonomy'
				";
			}
			$sql = apply_filters('fgd2wp_get_taxonomies_terms_count', $sql, $taxonomy);
			$result = $this->drupal_query($sql);
			$terms_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $terms_count;
		}

		/**
		 * Get the number of Drupal nodes
		 * 
		 * @param string $node_type Node type (article, page)
		 * @param string $entity_type Entity type (node, media)
		 * @return int Number of nodes
		 */
		public function get_nodes_count($node_type, $entity_type='node') {
			$prefix = $this->plugin_options['prefix'];
			if ( version_compare($this->drupal_version, '8', '<') ) {
				$table_name = $entity_type;
				$extra_joins = '';
			} else {
				// Drupal 8
				$table_name = $entity_type . '_field_data';
				$extra_joins = "INNER JOIN ${prefix}node nd ON nd.nid = n.nid AND nd.langcode = n.langcode";
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}$table_name n
				$extra_joins
				WHERE n.type = '$node_type'
			";
			$sql = apply_filters('fgd2wp_get_nodes_count_sql', $sql, $node_type, $entity_type);
			$result = $this->drupal_query($sql);
			$nodes_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $nodes_count;
		}

		/**
		 * Save the plugin options
		 *
		 */
		public function save_plugin_options() {
			$this->plugin_options = array_merge($this->plugin_options, $this->validate_form_info());
			update_option('fgd2wp_options', $this->plugin_options);

			// Hook for doing other actions after saving the options
			do_action('fgd2wp_post_save_plugin_options');
		}

		/**
		 * Validate POST info
		 *
		 * @return array Form parameters
		 */
		private function validate_form_info() {
			// Add http:// before the URL if it is missing
			$url = esc_url(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL));
			if ( !empty($url) && (preg_match('#^https?://#', $url) == 0) ) {
				$url = 'http://' . $url;
			}
			return array(
				'automatic_empty'			=> filter_input(INPUT_POST, 'automatic_empty', FILTER_VALIDATE_BOOLEAN),
				'url'						=> $url,
				'download_protocol'			=> filter_input(INPUT_POST, 'download_protocol', FILTER_SANITIZE_STRING),
				'base_dir'					=> filter_input(INPUT_POST, 'base_dir', FILTER_SANITIZE_STRING),
				'driver'					=> filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING),
				'hostname'					=> filter_input(INPUT_POST, 'hostname', FILTER_SANITIZE_STRING),
				'port'						=> filter_input(INPUT_POST, 'port', FILTER_SANITIZE_NUMBER_INT),
				'database'					=> filter_input(INPUT_POST, 'database', FILTER_SANITIZE_STRING),
				'username'					=> filter_input(INPUT_POST, 'username'),
				'password'					=> filter_input(INPUT_POST, 'password'),
				'sqlite_file'				=> filter_input(INPUT_POST, 'sqlite_file', FILTER_SANITIZE_STRING),
				'prefix'					=> filter_input(INPUT_POST, 'prefix', FILTER_SANITIZE_STRING),
				'summary'					=> filter_input(INPUT_POST, 'summary', FILTER_SANITIZE_STRING),
				'archived_posts'			=> filter_input(INPUT_POST, 'archived_posts', FILTER_SANITIZE_STRING),
				'skip_media'				=> filter_input(INPUT_POST, 'skip_media', FILTER_VALIDATE_BOOLEAN),
				'file_public_path_source'	=> filter_input(INPUT_POST, 'file_public_path_source', FILTER_SANITIZE_STRING),
				'file_public_path'			=> filter_input(INPUT_POST, 'file_public_path', FILTER_SANITIZE_STRING),
				'file_private_path_source'	=> filter_input(INPUT_POST, 'file_private_path_source', FILTER_SANITIZE_STRING),
				'file_private_path'			=> filter_input(INPUT_POST, 'file_private_path', FILTER_SANITIZE_STRING),
				'featured_image'			=> filter_input(INPUT_POST, 'featured_image', FILTER_SANITIZE_STRING),
				'only_featured_image'		=> filter_input(INPUT_POST, 'only_featured_image', FILTER_VALIDATE_BOOLEAN),
				'remove_first_image'		=> filter_input(INPUT_POST, 'remove_first_image', FILTER_VALIDATE_BOOLEAN),
				'skip_thumbnails'			=> filter_input(INPUT_POST, 'skip_thumbnails', FILTER_VALIDATE_BOOLEAN),
				'import_external'			=> filter_input(INPUT_POST, 'import_external', FILTER_VALIDATE_BOOLEAN),
				'import_duplicates'			=> filter_input(INPUT_POST, 'import_duplicates', FILTER_VALIDATE_BOOLEAN),
				'force_media_import'		=> filter_input(INPUT_POST, 'force_media_import', FILTER_VALIDATE_BOOLEAN),
				'timeout'					=> filter_input(INPUT_POST, 'timeout', FILTER_SANITIZE_NUMBER_INT),
				'logger_autorefresh'		=> filter_input(INPUT_POST, 'logger_autorefresh', FILTER_VALIDATE_BOOLEAN),
			);
		}

		/**
		 * Import
		 *
		 */
		private function import() {
			if ( $this->drupal_connect() ) {

				$time_start = microtime(true);

				define('WP_IMPORTING', true);
				update_option('fgd2wp_stop_import', false, false); // Reset the stop import action
				
				// To solve the issue of links containing ":" in multisite mode
				kses_remove_filters();
				
				global $wp_filter;
				unset($wp_filter['wp_insert_post']); // Remove the "wp_insert_post" that consumes a lot of CPU and memory
				
				// Check prerequesites before the import
				$do_import = apply_filters('fgd2wp_pre_import_check', true);
				if ( !$do_import) {
					return;
				}

				$total_elements_count = $this->get_total_elements_count();
				$this->progressbar->set_total_count($total_elements_count);
				
				// Default file paths
				$this->set_default_file_paths();
				
				// Set the Download Manager
				$this->download_manager = new FG_Drupal_to_WordPress_Download($this, $this->plugin_options['download_protocol']);
				$this->download_manager->test_connection();
				
				$this->imported_media = $this->get_imported_drupal_posts($meta_key = '_fgd2wp_old_file');
				
				// Hook for doing other actions before the import
				do_action('fgd2wp_pre_import');
				
				// Taxonomies
				if ( !isset($this->premium_options['skip_taxonomies']) || !$this->premium_options['skip_taxonomies'] ) {
					$this->taxonomy_term_hierarchy = $this->get_taxonomy_term_hierarchy();
					$cat_count = $this->import_taxonomies_terms('categories');
					$this->display_admin_notice(sprintf(_n('%d category imported', '%d categories imported', $cat_count, 'fg-drupal-to-wp'), $cat_count));
					$tags_count = $this->import_taxonomies_terms('tags');
					$this->display_admin_notice(sprintf(_n('%d tag imported', '%d tags imported', $tags_count, 'fg-drupal-to-wp'), $tags_count));
				}

				// Hook for doing other actions after importing the taxonomies
				do_action('fgd2wp_post_import_taxonomies');

				// Set the list of previously imported taxonomies
				$this->imported_taxonomies = $this->get_term_metas_by_metakey('_fgd2wp_old_taxonomy_id');
				
				if ( !isset($this->premium_options['skip_nodes']) || !$this->premium_options['skip_nodes'] ) {
					// Articles, pages and medias
					if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('article', $this->premium_options['nodes_to_skip']) ) {
						$articles_count = $this->get_nodes_count('article');
						if ( $articles_count > 0 ) {
							$articles_count = $this->import_nodes('article');
							if ( $articles_count === false ) { // Anti-duplicate
								return;
							}
							$this->display_admin_notice(sprintf(_n('%d article imported', '%d articles imported', $articles_count, 'fg-drupal-to-wp'), $articles_count));
						}
					}
					
					if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('story', $this->premium_options['nodes_to_skip']) ) {
						$stories_count = $this->get_nodes_count('story');
						if ( $stories_count > 0 ) {
							$stories_count = $this->import_nodes('story');
							$this->display_admin_notice(sprintf(_n('%d story imported', '%d stories imported', $stories_count, 'fg-drupal-to-wp'), $stories_count));
						}
					}
					
					if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('post', $this->premium_options['nodes_to_skip']) ) {
						$posts_count = $this->get_nodes_count('post');
						if ( $posts_count > 0 ) {
							$posts_count = $this->import_nodes('post');
							$this->display_admin_notice(sprintf(_n('%d post imported', '%d posts imported', $posts_count, 'fg-drupal-to-wp'), $posts_count));
						}
					}
					
					if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('page', $this->premium_options['nodes_to_skip']) ) {
						$pages_count = $this->import_nodes('page');
						$this->display_admin_notice(sprintf(_n('%d page imported', '%d pages imported', $pages_count, 'fg-drupal-to-wp'), $pages_count));
					}
					
				}
				if ( !$this->import_stopped() ) {
					// Hook for doing other actions after the import
					do_action('fgd2wp_post_import');
				}
				if ( !isset($this->premium_options['skip_nodes']) || !$this->premium_options['skip_nodes'] ) {
					$this->display_admin_notice(sprintf(_n('%d media imported', '%d medias imported', $this->media_count, 'fg-drupal-to-wp'), $this->media_count));
				}

				// Hook for other notices
				do_action('fgd2wp_import_notices');

				// Debug info
				if ( function_exists('wp_get_environment_type') && (wp_get_environment_type() == 'development') ) {
					$this->display_admin_notice(sprintf("Memory used: %s bytes<br />\n", number_format(memory_get_usage())));
					$time_end = microtime(true);
					$this->display_admin_notice(sprintf("Duration: %d sec<br />\n", $time_end - $time_start));
				}

				if ( $this->import_stopped() ) {
					
					// Import stopped by the user
					$this->display_admin_notice("IMPORT STOPPED BY USER");
					
				} else {
					// Import completed
					$this->display_admin_notice(__("Don't forget to modify internal links.", 'fg-drupal-to-wp'));
					$this->display_admin_notice("IMPORT COMPLETED");
				}
				
				wp_cache_flush();
			}
		}

		/**
		 * Actions to do before the import
		 * 
		 * @param bool $import_doable Can we start the import?
		 * @return bool Can we start the import?
		 */
		public function pre_import_check($import_doable) {
			if ( $import_doable ) {
				if ( !$this->plugin_options['skip_media'] && empty($this->plugin_options['url']) ) {
					$this->display_admin_error(__('The URL field is required to import the media.', 'fg-drupal-to-wp'));
					$import_doable = false;
				}
			}
			return $import_doable;
		}

		/**
		 * Get the number of elements to import
		 * 
		 * @return int Number of elements to import
		 */
		private function get_total_elements_count() {
			$count = 0;
			
			// Taxonomies
			if ( !isset($this->premium_options['skip_taxonomies']) || !$this->premium_options['skip_taxonomies'] ) {
				$count += $this->get_taxonomies_terms_count('categories');
				$count += $this->get_taxonomies_terms_count('tags');
			}

			// Nodes
			if ( !isset($this->premium_options['skip_nodes']) || !$this->premium_options['skip_nodes'] ) {
				if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('article', $this->premium_options['nodes_to_skip']) ) {
					$count += $this->get_nodes_count('article');
				}
				if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('story', $this->premium_options['nodes_to_skip']) ) {
					$count += $this->get_nodes_count('story');
				}
				if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('post', $this->premium_options['nodes_to_skip']) ) {
					$count += $this->get_nodes_count('post');
				}
				if ( !isset($this->premium_options['nodes_to_skip']) || !in_array('page', $this->premium_options['nodes_to_skip']) ) {
					$count += $this->get_nodes_count('page');
				}
			}

			$count = apply_filters('fgd2wp_get_total_elements_count', $count);
			
			return $count;
		}
		
		/**
		 * Get the whole taxonomies terms hierarchy
		 * 
		 * @return array Term hierarchy
		 */
		public function get_taxonomy_term_hierarchy() {
			if ( !$this->taxonomies_enabled) {
				return array();
			}
			$hierarchy = array();
			$prefix = $this->plugin_options['prefix'];
			
			if ( version_compare($this->drupal_version, '8.5', '<') ) {
				if ( version_compare($this->drupal_version, '7', '<') ) {
					// Drupal 6
					$table_name = 'term_hierarchy';
				} else {
					// Drupal 7 & 8
					$table_name = 'taxonomy_term_hierarchy';
				}
				$sql = "
					SELECT h.tid, h.parent
					FROM ${prefix}${table_name} h
					WHERE h.parent != 0
				";
			} else {
				// Drupal 8.5+
				$sql = "
					SELECT h.entity_id AS tid, h.parent_target_id AS parent
					FROM ${prefix}taxonomy_term__parent h
					WHERE h.parent_target_id != 0
				";
			}
			$result = $this->drupal_query($sql);
			foreach ( $result as $row ) {
				$hierarchy[$row['tid']] = $row['parent'];
			}
			return $hierarchy;
		}
		
		/**
		 * Import taxonomies terms
		 *
		 * @param string $taxonomy Taxonomy slug
		 * @param string $taxonomy_name Taxonomy name (for Drupal 6 only)
		 * @return int Number of terms imported
		 */
		public function import_taxonomies_terms($taxonomy, $taxonomy_name='') {
			$imported_terms_count = 0;
			$all_terms = array();
			
			if ( $this->import_stopped() ) {
				return 0;
			}
			
			$message = sprintf(__('Importing %s...', 'fg-drupal-to-wp'), $taxonomy);
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_taxonomies_terms_count($taxonomy));
			} else {
				$this->log($message);
			}
			
			if ( empty($taxonomy_name) ) {
				$taxonomy_name = $taxonomy; // For Drupal 6 only
			}
			
			do {
				if ( $this->import_stopped() ) {
					break;
				}
				$terms = $this->get_taxonomies_terms($taxonomy, $this->chunks_size, $taxonomy_name); // Get the Drupal taxonomies
				$terms_count = count($terms);
				$terms = apply_filters('fgd2wp_pre_insert_taxonomies_terms', $terms, $taxonomy);
				
				if ( !is_null($terms) && (count($terms) > 0) ) {
					$all_terms = array_merge($all_terms, $terms);
					// Insert the taxonomies terms
					$imported_terms_count += $this->insert_taxonomies_terms($terms, $taxonomy);
				}
				
				if ( defined('WP_CLI') ) {
					$progress_cli->tick($this->chunks_size);
				}
			} while ( !is_null($terms) && ($terms_count > 0) );
			
			$all_terms = apply_filters('fgd2wp_import_taxonomies_terms', $all_terms, $taxonomy);
			
			// Update the terms with their parent ids
			$this->update_parent_taxonomies_terms($all_terms, $taxonomy);
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			
			if ( !$this->import_stopped() ) {
				// Hook after importing all the taxonomies
				do_action('fgd2wp_post_import_taxonomies_terms', $all_terms, $taxonomy);
			}
			
			return $imported_terms_count;
		}
		
		/**
		 * Get Drupal taxonomies terms
		 *
		 * @param string $taxonomy Taxonomy slug
		 * @param int $limit Number of terms max
		 * @param string $taxonomy_name Taxonomy name (for Drupal 6 only)
		 * @param string $taxonomy_module Taxonomy module (for Drupal 6 only)
		 * @return array of taxonomies terms
		 */
		public function get_taxonomies_terms($taxonomy, $limit=1000, $taxonomy_name='', $taxonomy_module='') {
			$terms = array();
			
			if ( !$this->taxonomies_enabled) {
				return array();
			}

			$prefix = $this->plugin_options['prefix'];
			$taxonomy_name = esc_sql($taxonomy_name);
			$taxonomy_module = esc_sql($taxonomy_module);
			$last_taxonomy_metakey = "fgd2wp_last_taxonomy_${taxonomy}_id";
			$last_taxonomy_id = (int)get_option($last_taxonomy_metakey); // to restore the import where it left
			
			// Hooks for adding extra cols and extra joins
			$extra_cols = apply_filters('fgd2wp_get_terms_add_extra_cols', '');

			if ( version_compare($this->drupal_version, '7', '<') ) {
				if ( !empty($taxonomy_module) ) {
					// Search the taxonomy by module
					$criteria = "AND (tv.`module` = '$taxonomy_module' OR tv.`name` = '$taxonomy_name')";
				} elseif ( !empty($taxonomy_name) ) {
					// Search the taxonomy by name
					$criteria = "AND tv.`name` = '$taxonomy_name'";
				} else {
					return array();
				}
				// Drupal 6
				$sql = "
					SELECT t.tid, t.name, t.description, '' AS language
					$extra_cols
					FROM ${prefix}term_data t
					INNER JOIN ${prefix}vocabulary tv ON tv.vid = t.vid $criteria
					WHERE t.tid > '$last_taxonomy_id'
					ORDER BY t.tid
					LIMIT $limit
				";
			} elseif ( version_compare($this->drupal_version, '8', '<') ) {
				// Drupal 7
				$sql = "
					SELECT t.tid, t.name, t.description, '' AS language
					$extra_cols
					FROM ${prefix}taxonomy_term_data t
					INNER JOIN ${prefix}taxonomy_vocabulary tv ON tv.vid = t.vid AND tv.machine_name = '$taxonomy'
					WHERE t.tid > '$last_taxonomy_id'
					ORDER BY t.tid
					LIMIT $limit
				";
			} else {
				// Drupal 8
				$sql = "
					SELECT t.tid, t.name, t.description__value AS description, t.langcode AS language
					$extra_cols
					FROM ${prefix}taxonomy_term_field_data t
					INNER JOIN ${prefix}taxonomy_term_data td ON td.tid = t.tid AND td.langcode = t.langcode
					WHERE t.tid > '$last_taxonomy_id'
					AND t.vid = '$taxonomy'
					ORDER BY t.tid
					LIMIT $limit
				";
			}
			$sql = apply_filters('fgd2wp_get_taxonomies_terms_sql', $sql, $taxonomy);
			$terms = $this->drupal_query($sql);
			return $terms;
		}
		
		/**
		 * Insert a list of taxonomies terms in the database
		 * 
		 * @param array $terms List of terms
		 * @param string $taxonomy WordPress Taxonomy
		 * @return int Number of inserted terms
		 */
		public function insert_taxonomies_terms($terms, $taxonomy) {
			$terms_count = 0;
			$processed_terms_count = count($terms);
			$term_metakey = '_fgd2wp_old_taxonomy_id';
			$last_taxonomy_metakey = "fgd2wp_last_taxonomy_${taxonomy}_id";
			$wp_taxonomy = $this->map_taxonomy($taxonomy);
			
			// Set the list of previously imported taxonomies terms
			$this->imported_taxonomies = $this->get_term_metas_by_metakey($term_metakey);
			
			$new_terms = array();
			
			foreach ( $terms as $term ) {

				$term_id = apply_filters('fgd2wp_get_taxonomy_term_id', $term['tid'], $term);

				// Check if the taxonomy term is already imported
				if ( array_key_exists($term_id, $this->imported_taxonomies) ) {
					// Prevent the process to hang if the taxonomies terms counter has been resetted
					$term_id_without_prefix = preg_replace('/^(\D*)/', '', $term_id);
					update_option($last_taxonomy_metakey, $term_id_without_prefix);

					continue; // Do not import already imported term
				}
				
				$args = array();
				
				// Description
				if ( isset($term['description']) ) {
					$args['description'] = $term['description'];
				}

				// Parent term
				$parent_id = isset($this->taxonomy_term_hierarchy[$term['tid']])? $this->taxonomy_term_hierarchy[$term['tid']]: 0;
				if ( $parent_id != 0 ) {
					$parent_id = apply_filters('fgd2wp_get_taxonomy_term_id', $parent_id, $term);
					if ( isset($this->imported_taxonomies[$parent_id]) ) {
						$parent_tax_id = $this->imported_taxonomies[$parent_id];
						$args['parent'] = $parent_tax_id;
					}
				}
				
				// Hook before inserting the term
				$args = apply_filters('fgd2wp_pre_insert_taxonomy_term', $args, $term, $wp_taxonomy);
				
				$new_term = wp_insert_term($term['name'], $wp_taxonomy, $args);
				
				// Store the last ID to resume the import where it left off
				$term_id_without_prefix = preg_replace('/^(\D*)/', '', $term_id);
				update_option($last_taxonomy_metakey, $term_id_without_prefix);
				
				if ( is_wp_error($new_term) ) {
					if ( isset($new_term->error_data['term_exists']) ) {
						// Store the Drupal taxonomy term ID
						add_term_meta($new_term->error_data['term_exists'], $term_metakey, $term_id, false);
					}
					continue;
				}
				$terms_count++;
				$new_term_id = $new_term['term_id'];
				$new_terms[] = $new_term_id;
				$this->imported_taxonomies[$term_id] = $new_term_id;

				// Store the Drupal taxonomy term ID
				add_term_meta($new_term_id, $term_metakey, $term_id, true);
				
				// Hook after inserting the term
				do_action('fgd2wp_post_insert_taxonomy_term', $new_term_id, $term, $wp_taxonomy);
			}
			
			$this->progressbar->increment_current_count($processed_terms_count);
			
			// Update cache
			if ( !empty($new_terms) ) {
				wp_update_term_count_now($new_terms, $wp_taxonomy);
				$this->clean_cache($new_terms, $wp_taxonomy);
			}
			
			return $terms_count;
		}

		/**
		 * Build the taxonomy slug
		 * 
		 * @since 1.54.1
		 * 
		 * @param string $taxonomy Taxonomy name
		 * @return string Taxonomy slug
		 */
		public function build_taxonomy_slug($taxonomy) {
			if ( is_numeric($taxonomy) ) {
				$taxonomy = '_' . $taxonomy; // Avoid only numeric taxonomy slug
			}
			$taxonomy = substr(sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin(remove_accents($taxonomy))), 0, 30); // The taxonomy is limited to 30 characters in Types
			return $taxonomy;
		}
		
		/**
		 * Update the parent taxonomies terms
		 * 
		 * @param array $terms Taxonomies terms
		 * @param string $taxonomy Taxonomy
		 */
		public function update_parent_taxonomies_terms($terms, $taxonomy) {
			$wp_taxonomy = $this->map_taxonomy($taxonomy);
			foreach ( $terms as $term ) {
				$tid = apply_filters('fgd2wp_get_taxonomy_term_id', $term['tid'], $term);
				$parent_id = isset($this->taxonomy_term_hierarchy[$term['tid']])? $this->taxonomy_term_hierarchy[$term['tid']]: 0;
				if ( $parent_id != 0 ) {
					$parent_id = apply_filters('fgd2wp_get_taxonomy_term_id', $parent_id, $term);
					// Parent term
					if ( isset($this->imported_taxonomies[$tid]) && isset($this->imported_taxonomies[$parent_id]) ) {
						$tax_id = $this->imported_taxonomies[$tid];
						$parent_tax_id = $this->imported_taxonomies[$parent_id];
						wp_update_term($tax_id, $wp_taxonomy, array('parent' => $parent_tax_id));
					}
				}
			}
		}
		
		/**
		 * Clean the cache
		 * 
		 * @param array $terms Terms
		 * @param string $taxonomy Taxonomy
		 */
		public function clean_cache($terms, $taxonomy) {
			delete_option($taxonomy . '_children');
			clean_term_cache($terms, $taxonomy);
		}

		/**
		 * Import nodes
		 *
		 * @param string $content_type Content type (article, page)
		 * @param string $entity_type Entity type (node, media)
		 * @return int|bool Number of nodes imported or false
		 */
		public function import_nodes($content_type, $entity_type='node') {
			$imported_nodes_count = 0;

			$message = sprintf(__('Importing %s...', 'fg-drupal-to-wp'), FG_Drupal_to_WordPress_Tools::plural($content_type));
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_nodes_count($content_type));
			} else {
				$this->log($message);
			}
			
			// Hook for doing other actions before the import
			do_action('fgd2wp_pre_import_nodes');

			// Set the list of previously imported taxonomies
			$term_metakey = '_fgd2wp_old_taxonomy_id';
			$this->imported_taxonomies = $this->get_term_metas_by_metakey($term_metakey);
			
			$post_type = $this->map_post_type($content_type);
			
			do {
				if ( $this->import_stopped() ) {
					break;
				}
				$nodes = $this->get_nodes($content_type, $this->chunks_size, $entity_type); // Get the Drupal nodes
				$nodes_count = count($nodes);
				
				if ( is_array($nodes) ) {
					foreach ( $nodes as $node ) {
						$new_post_id = $this->import_node($node, $content_type, $post_type, $entity_type);
						if ( $new_post_id === false ) {
							return false;
						}
						// Hook for doing other actions after importing the node
						do_action('fgd2wp_post_import_post', $new_post_id, $node, $content_type, $post_type, $entity_type);
						$imported_nodes_count++;
						
						if ( defined('WP_CLI') ) {
							$progress_cli->tick();
						}
					}
				}
				$this->progressbar->increment_current_count($nodes_count);
			} while ( !is_null($nodes) && ($nodes_count > 0) );
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}

			if ( !$this->import_stopped() ) {
				// Hook for doing other actions after the import
				do_action('fgd2wp_post_import_nodes');
			}

			return $imported_nodes_count;
		}
		
		/**
		 * Get Drupal nodes
		 *
		 * @param string $content_type Content type (article, page)
		 * @param int $limit Number of nodes max
		 * @param string $entity_type Entity type (node, media)
		 * @return array of Posts
		 */
		protected function get_nodes($content_type, $limit=1000, $entity_type = 'node') {
			$nodes = array();

			$last_node_type_metakey = "fgd2wp_last_${entity_type}_${content_type}_id";
			$last_drupal_id = (int)get_option($last_node_type_metakey); // to restore the import where it left
			$prefix = $this->plugin_options['prefix'];

			$extra_joins = '';
			$language_field = 'n.language';
			if ( version_compare($this->drupal_version, '8', '<') ) {
				$table_name = $entity_type;
			} else {
				// Drupal 8
				$table_name = $entity_type . '_field_data';
				$extra_joins = "INNER JOIN ${prefix}node nd ON nd.nid = n.nid AND nd.langcode = n.langcode";
				$language_field = 'n.langcode';
			}
			if ( version_compare($this->drupal_version, '6', '<') ) {
				$language_field = "''";
			}
			if ( version_compare($this->drupal_version, '5', '<') ) {
				// Drupal 4
				$extra_cols = ", 0 AS vid, 0 AS sticky";
			} else {
				$extra_cols = ', n.vid, n.sticky';
			}
			// Hooks for adding extra cols and extra joins
			$extra_cols = apply_filters('fgd2wp_get_nodes_add_extra_cols', $extra_cols);
			$extra_joins = apply_filters('fgd2wp_get_nodes_add_extra_joins', $extra_joins);

			$sql = "
				SELECT n.nid, n.title, n.type, n.status, n.created, $language_field AS language
				$extra_cols
				FROM ${prefix}${table_name} n
				$extra_joins
				WHERE n.type = '$content_type'
				AND n.nid > '$last_drupal_id'
				ORDER BY n.nid
				LIMIT $limit
			";
			$sql = apply_filters('fgd2wp_get_nodes_sql', $sql, $prefix, $last_drupal_id, $limit, $content_type, $entity_type);
			$nodes = $this->drupal_query($sql);
			return $nodes;
		}

		/**
		 * Import a node
		 * 
		 * @param array $node Post data
		 * @param string $content_type Content type (article, page)
		 * @param string $post_type WP post type
		 * @param string $entity_type Entity type (node, media)
		 * @return int new post ID | false | WP_Error
		 */
		public function import_node($node, $content_type, $post_type, $entity_type='node') {
			
			// Anti-duplicate
			if ( !$this->test_antiduplicate ) {
				sleep(2);
				$test_node_id = $this->get_wp_post_id_from_drupal_id($node['nid'], $entity_type);
				if ( !empty($test_node_id) ) {
					$this->display_admin_error(__('The import process is still running. Please wait before running it again.', 'fg-drupal-to-wp'));
					return false;
				}
				$this->test_antiduplicate = true;
			}
			
			$last_node_type_metakey = "fgd2wp_last_${entity_type}_${content_type}_id";
			
			// Slug
			$slug = $this->get_node_slug($node);
			
			// Get the body
			if ( $entity_type == 'node' ) {
				$body = $this->get_data_body($node);
				$node = array_merge($node, $body);
			}
			
			// Hook for modifying the Drupal node before processing
			$node = apply_filters('fgd2wp_pre_process_node', $node);
			
			// Date
			$post_date = empty($node['created'])? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $node['created']);
			
			// Categories
			$categories_ids = array();
			if ( !isset($this->premium_options['skip_taxonomies']) || !$this->premium_options['skip_taxonomies'] ) {
				if ( $post_type != 'page' ) { // Pages don't have a category in WordPress
					$categories = $this->get_node_taxonomies_terms($node['nid'], 'categories');
					$categories_ids = $this->get_wp_taxonomies_terms_ids($categories);
					$categories_ids = apply_filters('fgd2wp_get_node_terms_ids', $categories_ids, $node, $categories);
					if ( ($post_type == 'post') && (count($categories_ids) == 0) ) {
						$categories_ids[] = 1; // default category
					}
				}
			}
			
			// Tags
			$tag_names = array();
			if ( !isset($this->premium_options['skip_taxonomies']) || !$this->premium_options['skip_taxonomies'] ) {
				if ( $post_type != 'page' ) { // Pages don't have a category in WordPress
					$tags = $this->get_node_taxonomies_terms($node['nid'], 'tags');
					foreach ( $tags as $tag ) {
						$tag_names[] = $tag['name'];
					}
				}
			}
			
			// Medias
			$post_media = array();
			$featured_image_id = '';
			$gallery_shortcode = array();
			if ( !$this->plugin_options['skip_media'] ) {
				// Featured image
				list($featured_image_id, $node) = $this->get_and_process_featured_image($node);
				
				// Import media
				if ( !$this->plugin_options['only_featured_image'] ) {
					// Media from content
					$body_summary = isset($node['body_summary'])? $node['body_summary'] : '';
					$body_value = isset($node['body_value'])? $node['body_value'] : '';
					$body = $body_summary . $body_value;
					$result = $this->import_media_from_content($body, $post_date, array('ref' => 'node ID=' . $node['nid']));
					$post_media = $result['media'];
					$this->media_count += $result['media_count'];
					
					// Media gallery (image_attach)
					$media_gallery = array();
					$media_gallery = apply_filters('fgd2wp_import_media_gallery', $media_gallery, $node, $post_date);
					if ( !empty($media_gallery) ) {
						// Set the featured image
						if ( empty($featured_image_id) ) {
							$featured_image_id = reset($media_gallery);
						}
						
						// Create the gallery
						$gallery_ids = array_values($media_gallery);
						$gallery_shortcode = '[gallery ids="' . implode(', ', $gallery_ids) . '"]';
						
						$post_media = array_merge($post_media, $media_gallery);
					}
				}
			}
			
			// Define excerpt and node content
			list($excerpt, $content) = $this->set_excerpt_content($node);
			
			// Add the gallery
			if ( !empty($gallery_shortcode) ) {
				$content .= $gallery_shortcode;
			}
			
			// Process content
			$excerpt = $this->process_content($excerpt, $post_media);
			$content = $this->process_content($content, $post_media);
			
			// Status
			$status = ($node['status'] > 0)? 'publish': 'draft';
			
			// Insert the post
			$new_post = array(
				'post_category'		=> $categories_ids,
				'post_content'		=> $content,
				'post_date'			=> $post_date,
				'post_excerpt'		=> substr($excerpt, 0, 65535),
				'post_status'		=> $status,
				'post_title'		=> $node['title'],
				'post_name'			=> sanitize_title($slug),
				'post_type'			=> $post_type,
				'tags_input'		=> $tag_names,
			);

			// Hook for modifying the WordPress post just before the insert
			$new_post = apply_filters('fgd2wp_pre_insert_post', $new_post, $node);

			$new_post_id = wp_insert_post($new_post, true);
			
			// Increment the Drupal last imported post ID
			update_option($last_node_type_metakey, $node['nid']);
			
			if ( is_wp_error($new_post_id) ) {
				$this->display_admin_error(sprintf(__('Node #%d:', 'fg-drupal-to-wp'), $node['nid']) . ' ' . $new_post_id->get_error_message() . ' ' . $new_post_id->get_error_data());
			} else {
				// Add links between the post and its medias
				if ( !empty($featured_image_id) ) {
					$post_media[] = $featured_image_id;
				}
				$this->add_post_media($new_post_id, $new_post, $post_media, false);
				
				// Set the featured image
				if ( !empty($featured_image_id) ) {
					set_post_thumbnail($new_post_id, $featured_image_id);
				}

				// Add the Drupal ID as a post meta in order to modify links after
				add_post_meta($new_post_id, '_fgd2wp_old_' . $entity_type . '_id', $node['nid'], true);
				
				// Hook for doing other actions after inserting the post
				do_action('fgd2wp_post_insert_post', $new_post_id, $node, $post_type, $entity_type);
			}

			return $new_post_id;
		}
		
		/**
		 * Get the node slug
		 * 
		 * @since 2.31.0
		 * 
		 * @param array $node Node
		 * @return string Slug
		 */
		private function get_node_slug($node) {
			$slug = $node['title'];
			if ( version_compare($this->drupal_version, '7', '<') ) {
				// Drupal 6
				$table = 'url_alias';
				$source_field = 'src';
				$alias_field = 'dst';
			} elseif ( version_compare($this->drupal_version, '8', '<') ) {
				// Drupal 7
				$table = 'url_alias';
				$source_field = 'source';
				$alias_field = 'alias';
			} else {
				// Drupal 8+
				$table = 'path_alias';
				$source_field = 'path';
				$alias_field = 'alias';
			}
			if ( $this->table_exists($table) ) {
				$prefix = $this->plugin_options['prefix'];
				$sql = "
					SELECT u.$alias_field AS alias
					FROM ${prefix}$table u
					WHERE u.$source_field = 'node/{$node['nid']}'
					OR u.$source_field = '/node/{$node['nid']}'
					LIMIT 1
				";
				$urls = $this->drupal_query($sql);
				if ( count($urls) > 0 ) {
					$slug = $urls[0]['alias'];
					$slug = preg_replace('#.*/#', '', $slug); // Remove the path before /
				}
			}
			return $slug;
		}
		
		/**
		 * Get the body (content, summary) of the node
		 * 
		 * @param array $node Node
		 * @return array Node body (content and summary)
		 */
		private function get_data_body($node) {
			$body = array();

			$prefix = $this->plugin_options['prefix'];

			if ( version_compare($this->drupal_version, '5', '<') ) {
				// Drupal 4
				$sql = "
				SELECT n.body AS body_value, n.teaser AS body_summary
				FROM ${prefix}node n
				WHERE n.nid = " . $node['nid'];
			} elseif ( version_compare($this->drupal_version, '7', '<') ) {
				// Drupal 5 & 6
				$sql = "
				SELECT nr.body AS body_value, nr.teaser AS body_summary
				FROM ${prefix}node_revisions nr
				WHERE nr.vid = " . $node['vid'];
			} else {
				$extra_criteria = '';
				$order_by = '';
				// Drupal 7 & 8
				if ( version_compare($this->drupal_version, '8', '<') ) {
					// Drupal 7
					$table_name = 'field_data_body';
					$order_by = "ORDER BY b.language";
				} else {
					// Drupal 8
					$table_name = 'node__body';
					$order_by = "ORDER BY b.langcode";
				}
				// Default language
				if ( isset($node['language']) && !empty($node['language']) && ($node['language'] != 'und') ) {
					if ( version_compare($this->drupal_version, '8', '>=') ) {
						// Version 8
						$extra_criteria .= " AND b.langcode IN('" . $node['language'] . "', 'und')";
					} else if ( version_compare($this->drupal_version, '7', '>=') ) {
						// Version 7
						$extra_criteria .= " AND b.language IN('" . $node['language'] . "', 'und')";
					}
				}
				
				if ( !$this->table_exists($table_name) ) {
					return array('', '');
				}
				if ( $this->column_exists($table_name, 'body_summary') ) {
					$body_summary_field = 'b.body_summary';
				} else {
					$body_summary_field = "'' AS body_summary";
				}
				$extra_criteria = apply_filters('fgd2wp_get_data_field_extra_criteria', $extra_criteria, $node, 'b.');
				$order_by = apply_filters('fgd2wp_get_data_field_order_by', $order_by, $node);
				$sql = "
					SELECT b.body_value, $body_summary_field
					FROM ${prefix}${table_name} b
					WHERE b.entity_id = " . $node['nid'] . "
					AND b.deleted = 0
					$extra_criteria
					$order_by
					LIMIT 1
				";
			}
			$result = $this->drupal_query($sql);
			if ( isset($result[0]) ) {
				$body = $result[0];
			}
			return $body;
		}
		
		/**
		 * Get the taxonomies terms associated with a node
		 * 
		 * @param int $node_id Node ID
		 * @param string $taxonomy Taxonomy name (all by default)
		 * @param string $taxonomy_module Taxonomy module (for Drupal 6 only)
		 * @param string $entity_type Entity type (node, media)
		 * @return array Taxonomies terms
		 */
		public function get_node_taxonomies_terms($node_id, $taxonomy='', $taxonomy_module='', $entity_type='node') {
			$terms = array();

			if ( !$this->taxonomies_enabled) {
				return array();
			}
			
			$prefix = $this->plugin_options['prefix'];
			
			// Hooks for adding extra cols and extra joins
			$extra_cols = apply_filters('fgd2wp_get_terms_add_extra_cols', '');


			if ( version_compare($this->drupal_version, '7', '<') ) {
				// Drupal 6
				$sql = "
					SELECT i.tid, t.name, LOWER(v.name) AS taxonomy
					$extra_cols
					FROM ${prefix}term_node i
					INNER JOIN ${prefix}term_data t ON t.tid = i.tid
					INNER JOIN ${prefix}vocabulary v ON v.vid = t.vid
					WHERE i.nid = '$node_id'
				";
				if ( !empty($taxonomy_module) ) {
					if ( !empty($taxonomy) ) {
						$sql .= "
							AND (v.module = '$taxonomy_module' OR v.name = '$taxonomy')
						";
					} else {
						$sql .= "
							AND v.module = '$taxonomy_module'
						";
					}
				} elseif ( !empty($taxonomy) ) {
					$sql .= "
						AND v.name = '$taxonomy'
					";
				}
			} elseif ( version_compare($this->drupal_version, '8', '<') ) {
				// Drupal 7
				$sql = "
					SELECT i.tid, t.name, v.machine_name AS taxonomy
					$extra_cols
					FROM ${prefix}taxonomy_index i
					INNER JOIN ${prefix}taxonomy_term_data t ON t.tid = i.tid
					INNER JOIN ${prefix}taxonomy_vocabulary v ON v.vid = t.vid
					WHERE i.nid = '$node_id'
				";
				if ( !empty($taxonomy) ) {
					$sql .= "
						AND v.machine_name = '$taxonomy'
					";
				}
			} else {
				// Drupal 8
				$sql = "
					SELECT i.tid, t.name, t.vid AS taxonomy
					$extra_cols
					FROM ${prefix}taxonomy_index i
					INNER JOIN ${prefix}taxonomy_term_field_data t ON t.tid = i.tid
					WHERE i.nid = '$node_id'
				";
				if ( !empty($taxonomy) ) {
					$sql .= "
						AND t.vid = '$taxonomy'
					";
				}
			}
			$terms = $this->drupal_query($sql);
			$terms = apply_filters('fgd2wp_get_node_taxonomies_terms', $terms, $node_id, $entity_type);
			return $terms;
		}
		
		/**
		 * Get the WordPress term ids corresponding to the Drupal terms
		 * 
		 * @param array $terms Taxonomies terms
		 * @return array Taxonomies terms ids
		 */
		public function get_wp_taxonomies_terms_ids($terms) {
			$terms_ids = array();
			foreach ( $terms as $term ) {
				$term_id = apply_filters('fgd2wp_get_taxonomy_term_id', $term['tid'], $term);
				if ( isset($this->imported_taxonomies[$term_id]) ) {
					$terms_ids[] = (int)$this->imported_taxonomies[$term_id];
				}
			}
			return $terms_ids;
		}
		
		/**
		 * Get the post type associated to a Drupal content type
		 * 
		 * @param string $content_type Drupal content type
		 * @return string WordPress post type
		 */
		public function map_post_type($content_type) {
			$post_type = '';
			switch ( $content_type ) {
				case 'article':
				case 'story':
				case 'post':
					$post_type = 'post';
					break;
				case 'page':
					$post_type = 'page';
					break;
				default:
					$post_type = substr(sanitize_key($content_type), 0, 20);
			}
			$post_type = apply_filters('fgd2wp_convert_node_type', $post_type, $content_type); // TODO rename field to fgd2wp_map_post_type
			return $post_type;
		}
		
		/**
		 * Map a taxonomy
		 * 
		 * @since 1.40.0
		 * 
		 * @param string $taxonomy Taxonomy
		 * @return string Taxonomy
		 */
		public function map_taxonomy($taxonomy) {
			$wp_taxonomy = '';
			switch ( $taxonomy ) {
				case 'categories':
					$wp_taxonomy = 'category';
					break;
				case 'tags':
					$wp_taxonomy = 'post_tag';
					break;
				case 'post_type': // The taxonomy "post_type" prevents the posts to display on the backend with CPT UI
					$wp_taxonomy = 'posttype';
					break;
				default:
					$wp_taxonomy = $this->build_taxonomy_slug($taxonomy);
			}
			return $wp_taxonomy;
		}
		
		/**
		 * Determine the featured image and modify the node if needed
		 * 
		 * @since      1.0.0
		 * 
		 * @param array $node Post data
		 * @return array [Featured image ID, Node]
		 */
		public function get_and_process_featured_image($node) {
			$featured_image = '';
			$featured_image_id = 0;
			list($featured_image, $node) = apply_filters('fgd2wp_pre_import_media', array($featured_image, $node));
			
			// Set the featured image from the image field
			if ( empty($featured_image) && $this->plugin_options['featured_image'] == 'featured' ) {
				$field_image = $this->get_field_image($node['nid'], $node['type']);
				if ( !empty($field_image) ) {
					$filename = preg_replace('/\..*$/', '', $field_image['filename']);
					$featured_image = array(
						'name' => $filename,
						'filename' => $this->get_path_from_uri($field_image['uri']),
						'date' => date('Y-m-d H:i:s', $field_image['timestamp']),
						'attributs' => array(
							'image_alt' => $this->get_image_attributes($field_image, 'alt'),
							'description' => $this->get_image_attributes($field_image, 'description'),
						),
					);
					$featured_image = apply_filters('fgd2wp_get_featured_image', $featured_image, $node);
					$featured_image_id = $this->import_media($featured_image['name'], $featured_image['filename'], $featured_image['date'], $featured_image['attributs'], array('ref' => 'node ID=' . $node['nid']));
				}
			}
			
			// Set the featured image from the content
			if ( empty($featured_image) && $this->plugin_options['featured_image'] != 'none' ) {
				if ( isset($node['body_summary']) && isset($node['body_value']) ) {
					$image_string = $this->get_first_image_from($node['body_summary']);
					if ( empty($image_string) ) {
						$image_string = $this->get_first_image_from($node['body_value']);
					}
					$post_date = date('Y-m-d H:i:s', $node['created']);

					// Remove the first image from the content
					if ( !empty($image_string) && $this->plugin_options['remove_first_image'] ) {
						$node['body_summary'] = $this->remove_image_from_content($image_string, $node['body_summary']);
						$node['body_value'] = $this->remove_image_from_content($image_string, $node['body_value']);
					}
					// Import the first image
					if ( !empty($image_string) ) {
						$result = $this->import_media_from_content($image_string, $post_date, array('ref' => 'node ID=' . $node['nid']));
						if ( !empty($result['media']) ) {
							$featured_image_id = array_shift($result['media']);
						}
					}
				}
			}
			
			if ( !empty($featured_image_id) ) {
				$this->media_count++;
			}
			return array($featured_image_id, $node);
		}
		
		/**
		 * Get the field image from a node
		 * 
		 * @param int $node_id Node ID
		 * @param string $node_type Node type
		 * @return array Image data
		 */
		public function get_field_image($node_id, $node_type='') {
			$image = array();

			$prefix = $this->plugin_options['prefix'];

			if ( version_compare($this->drupal_version, '7', '<') ) {
				// Drupal 6
				// No native image field for Drupal 6, but can be obtained with a CCK field
				$image = apply_filters('fgd2wp_get_drupal6_field_image', $image, $node_id, $node_type);
				return $image;
				
			} elseif ( version_compare($this->drupal_version, '8', '<') ) {
				// Drupal 7
				$table_name = 'field_data_field_image';
				$timestamp_field = 'timestamp';
				$field_image_id_field = 'field_image_fid';
				
			} else {
				// Drupal 8
				$table_name = 'node__field_image';
				$timestamp_field = 'created AS timestamp';
				$field_image_id_field = 'field_image_target_id';
			}
			if ( $this->table_exists($table_name) ) {
				if ( $this->column_exists($table_name, 'field_image_alt') ) {
					$alt_field = 'i.field_image_alt AS alt';
					$alt_field_exists = true;
				} else {
					$alt_field = "'' AS alt";
					$alt_field_exists = false;
				}
				if ( version_compare($this->drupal_version, '8', '<') || $alt_field_exists ) {
					$sql = "
						SELECT f.fid, $alt_field, i.field_image_title AS title, f.filename, f.uri, f.filemime, f.${timestamp_field}
						FROM ${prefix}${table_name} i
						INNER JOIN ${prefix}file_managed f ON f.fid = i.${field_image_id_field}
						WHERE i.entity_id = '$node_id'
						AND i.deleted = 0
						LIMIT 1
					";
				} else {
					// Drupal 8 structure modified using media_field_data as an intermediary table
					$sql = "
						SELECT f.fid, m.thumbnail__alt AS alt, m.thumbnail__title AS title, f.filename, f.uri, f.filemime, f.${timestamp_field}
						FROM ${prefix}${table_name} i
						INNER JOIN ${prefix}media_field_data m ON m.mid = i.${field_image_id_field}
						INNER JOIN ${prefix}file_managed f ON f.fid = m.thumbnail__target_id
						WHERE i.entity_id = '$node_id'
						AND i.deleted = 0
						LIMIT 1
					";
				}
				$result = $this->drupal_query($sql);
				if ( isset($result[0]) ) {
					$image = $result[0];
				}
			}
			return $image;
		}
		
		/**
		 * Get the default Drupal file paths and set them as globals
		 * 
		 * @since 1.16.4
		 */
		private function set_default_file_paths() {
				
			// Public path
			if ( $this->plugin_options['file_public_path_source'] == 'changed' ) {
				// Get the path entered in the options
				$this->file_public_path = $this->plugin_options['file_public_path'];
				
			} else {
				// Get the default values from the database
				$this->file_public_path = $this->get_drupal_variable('file_public_path');
				if ( empty($this->file_public_path) ) {
					$this->file_public_path = 'sites/default/files';
				}
			}
			$this->file_public_path = trailingslashit($this->file_public_path);

			// Private path
			if ( $this->plugin_options['file_private_path_source'] == 'changed' ) {
				// Get the path entered in the options
				$this->file_private_path = $this->plugin_options['file_private_path'];
				
			} else {
				$this->file_private_path = $this->get_drupal_variable('file_private_path');
				if ( empty($this->file_private_path) ) {
					$this->file_private_path = 'sites/default/private/files';
				}
			}
			$this->file_private_path = trailingslashit($this->file_private_path);
			
			do_action('fgd2wp_set_default_file_paths');
		}
		
		/**
		 * Get the real image path from the uri field
		 * 
		 * @param string $uri Image URI
		 * @return string Image path
		 */
		public function get_path_from_uri($uri) {
			$path = str_replace('public://', trailingslashit($this->plugin_options['url']) . $this->file_public_path, $uri);
			$path = str_replace('private://', trailingslashit($this->plugin_options['url']) . $this->file_private_path, $path);
			$path = apply_filters('fgd2wp_get_path_from_uri', $path);
			return $path;
		}
		
		/**
		 * Get the image attributes
		 * 
		 * @since 1.16.0
		 * 
		 * @param array $file File data
		 * @param string $field_name Field name (alt, description)
		 * @return string Image field value
		 */
		public function get_image_attributes($file, $field_name) {
			$value = '';
			
			if ( isset($file[$field_name]) && !empty($file[$field_name]) ) {
				// field
				$value = $file[$field_name];
			} elseif ( isset($file['data']) && !empty($file['data']) ) {
				// Data field serialized
				$data = unserialize($file['data']);
				if ( isset($data[$field_name]) ) {
					$value = $data[$field_name];
				}
			}
			return $value;
		}
		
		/**
		 * Get the first image from a content
		 * 
		 * @since      1.0.0
		 * 
		 * @param string $content
		 * @return string Featured image tag
		 */
		private function get_first_image_from($content) {
			$matches = array();
			$featured_image = '';
			
			$img_pattern = '#(<img .*?>)#i';
			if ( preg_match($img_pattern, $content, $matches) ) {
				$featured_image = $matches[1];
			}
			return $featured_image;
		}
		
		/**
		 * Remove the image from the content
		 * 
		 * @since      1.0.0
		 * 
		 * @param string $image Image to remove
		 * @param string $content Content
		 * @return string Content
		 */
		private function remove_image_from_content($image, $content) {
			$matches = array();
			$image_src = '';
			if ( preg_match('#src=["\'](.*?)["\']#', $image, $matches) ) {
				$image_src = $matches[1];
			}
			if ( !empty($image_src) ) {
				$img_pattern = '#(<img.*?src=["\']' . preg_quote($image_src) . '["\'].*?>)#i';
				$content = preg_replace($img_pattern, '', $content, 1);
			}
			return $content;
		}
		
		/**
		 * Import post medias from content
		 *
		 * @param string $content post content
		 * @param date $post_date Post date (for storing media)
		 * @param array $options Options
		 * @return array:
		 * 		array media: Medias imported
		 * 		int media_count:   Medias count
		 */
		public function import_media_from_content($content, $post_date='', $options=array()) {
			if ( empty($post_date) ) {
				$post_date = date('Y-m-d H:i:s');
			}
			$media = array();
			$media_count = 0;
			$matches = array();
			$alt_matches = array();
			$title_matches = array();
			
			if ( preg_match_all('#<(img|a)(.*?)(src|href)="(.*?)"(.*?)>#s', $content, $matches, PREG_SET_ORDER) > 0 ) {
				if ( is_array($matches) ) {
					foreach ($matches as $match ) {
						$filename = $match[4];
						if ( preg_match('/\.html([#?].*)?$/', $filename) ) {
							continue; // Don't process HTML links
						}
						$filename_decoded = rawurldecode($filename); // for filenames with spaces or accents
						$other_attributes = $match[2] . $match[5];
						// Image Alt
						$image_alt = '';
						if (preg_match('#alt="(.*?)"#', $other_attributes, $alt_matches) ) {
							$image_alt = wp_strip_all_tags(stripslashes($alt_matches[1]), true);
						}
						// Image caption
						$image_caption = '';
						if (preg_match('#title="(.*?)"#', $other_attributes, $title_matches) ) {
							$image_caption = $title_matches[1];
						}
						$attachment_id = $this->import_media($image_alt, $filename_decoded, $post_date, array('image_caption' => $image_caption), $options);
						if ( $attachment_id !== false ) {
							$media_count++;
							$media[$filename] = $attachment_id;
						}
					}
				}
			}
			return array(
				'media'			=> $media,
				'media_count'	=> $media_count
			);
		}
		
		/**
		 * Import a media
		 *
		 * @param string $name Image name
		 * @param string $filename Image URL
		 * @param date $date Date
		 * @param array $attributes Image attributes (image_alt, image_caption)
		 * @param array $options Options
		 * @return int attachment ID or false
		 */
		public function import_media($name, $filename, $date='0000-00-00 00:00:00', $attributes=array(), $options=array()) {
			if ( $date == '0000-00-00 00:00:00' ) {
				$date = date('Y-m-d H:i:s');
			}
			$import_external = ($this->plugin_options['import_external'] == 1) || (isset($options['force_external']) && $options['force_external'] );
			
			$filename = trim($filename); // for filenames with extra spaces at the beginning or at the end
			$filename = preg_replace('/[?#].*/', '', $filename); // Remove the attributes and anchors
			$filename = html_entity_decode($filename); // for filenames with HTML entities
			// Filenames starting with //
			if ( preg_match('#^//#', $filename) ) {
				$filename = 'http:' . $filename;
			}
			
			$filetype = wp_check_filetype($filename);
			if ( empty($filetype['type']) ) { // Unrecognized file type
				return false;
			}
			if ( ($filetype['type'] == 'text/html') && !preg_match('/\.html?$/', $filename) ) { // HTML content, except if this is really an HTML file
				return false;
			}

			// Upload the file from the Drupal web site to WordPress upload dir
			if ( preg_match('/^http/', $filename) ) {
				if ( $import_external || // External file 
					preg_match('#^' . $this->plugin_options['url'] . '#', $filename) // Local file
				) {
					$old_filename = $filename;
				} else {
					return false;
				}
			} else {
				if ( strpos($filename, '/') === 0 ) { // Absolute path
					$domain = preg_replace('#(.*?://.*?)/.*#', "$1", $this->plugin_options['url']);
					$old_filename = untrailingslashit($domain) . $filename;
				} else {
					$old_filename = trailingslashit($this->plugin_options['url']) . $filename;
				}
			}
			
			// Don't re-import the already imported media
			if ( array_key_exists($old_filename, $this->imported_media) ) {
				return $this->imported_media[$old_filename];
			}
			
			// Get the upload path
			$upload_path = $this->upload_dir($filename, $date, get_option('uploads_use_yearmonth_folders'));
			
			// Make sure we have an uploads directory.
			if ( !wp_mkdir_p($upload_path) ) {
				$this->display_admin_error(sprintf(__("Unable to create directory %s", 'fg-drupal-to-wp'), $upload_path));
				return false;
			}
			
			$new_filename = $filename;
			if ( $this->plugin_options['import_duplicates'] == 1 ) {
				// Images with duplicate names
				$new_filename = preg_replace('#.*' . $this->file_public_path . '#', '', $new_filename);
				$new_filename = preg_replace('#.*' . $this->file_private_path . '#', '', $new_filename);
				$new_filename = str_replace('http://', '', $new_filename);
				$new_filename = str_replace('/', '_', $new_filename);
			}

			$basename = basename($new_filename);
			$basename = sanitize_file_name($basename);
			$new_full_filename = $upload_path . '/' . $basename;

			// GUID
			$upload_dir = wp_upload_dir();
			$guid = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $new_full_filename);
			$attachment_id = $this->get_post_id_from_guid($guid);
			
			if ( empty($attachment_id) ) {
				if ( !$this->download_manager->copy($old_filename, $new_full_filename) ) {
					$error = error_get_last();
					$error_message = $error['message'];
					$ref = isset($options['ref'])? ' (' . $options['ref'] . ')' : ''; // Reference of the Drupal entity
					$this->display_admin_error("Can't copy $old_filename$ref to $new_full_filename : $error_message");
					return false;
				}

				$post_title = !empty($name)? $name : preg_replace('/\.[^.]+$/', '', $basename);

				// Image Alt
				$image_alt = isset($attributes['image_alt'])? $attributes['image_alt']: '';
				if ( empty($image_alt) && !empty($name) ) {
					$image_alt = wp_strip_all_tags(stripslashes($name), true);
				}
				$description = isset($attributes['description'])? $attributes['description']: '';
				$image_caption = isset($attributes['image_caption'])? $attributes['image_caption']: '';

				$attachment_id = $this->insert_attachment($post_title, $basename, $new_full_filename, $guid, $date, $filetype['type'], $image_alt, $description, $image_caption);
				update_post_meta($attachment_id, '_fgd2wp_old_file', $old_filename);
				$this->imported_media[$old_filename] = $attachment_id;
			}
			
			return $attachment_id;
		}
		
		/**
		 * Determine the media upload directory
		 * 
		 * @param string $filename Filename
		 * @param date $date Date
		 * @param bool $use_yearmonth_folders Use the Year/Month tree folder
		 * @return string Upload directory
		 */
		private function upload_dir($filename, $date, $use_yearmonth_folders=true) {
			$upload_dir = wp_upload_dir(strftime('%Y/%m', strtotime($date)));
			if ( $use_yearmonth_folders ) {
				$upload_path = $upload_dir['path'];
			} else {
				$short_filename = preg_replace('#.*' . $this->file_public_path . '#', '/', $filename);
				if ( strpos($short_filename, '/') != 0 ) {
					$short_filename = '/' . $short_filename; // Add a slash before the filename
				}
				$upload_path = $upload_dir['basedir'] . untrailingslashit(dirname($short_filename));
			}
			return $upload_path;
		}
		
		/**
		 * Save the attachment and generates its metadata
		 * 
		 * @param string $attachment_title Attachment name
		 * @param string $basename Original attachment filename
		 * @param string $new_full_filename New attachment filename with path
		 * @param string $guid GUID
		 * @param date $date Date
		 * @param string $filetype File type
		 * @param string $image_alt Image alternative description
		 * @param string $description Image internal description
		 * @param string $image_caption Image caption
		 * @return int|false Attachment ID or false
		 */
		public function insert_attachment($attachment_title, $basename, $new_full_filename, $guid, $date, $filetype, $image_alt='', $description='', $image_caption='') {
			$post_name = 'attachment-' . sanitize_title($attachment_title); // Prefix the post name to avoid wrong redirect to a post with the same name
			
			// If the attachment does not exist yet, insert it in the database
			$attachment_id = 0;
			$attachment = $this->get_attachment_from_name($post_name);
			if ( $attachment ) {
				$attached_file = basename(get_attached_file($attachment->ID));
				if ( $attached_file == $basename ) { // Check if the filename is the same (in case of the legend is not unique)
					$attachment_id = $attachment->ID;
				}
			}
			if ( $attachment_id == 0 ) {
				$attachment_data = array(
					'guid'				=> $guid, 
					'post_date'			=> $date,
					'post_mime_type'	=> $filetype,
					'post_name'			=> $post_name,
					'post_title'		=> $attachment_title,
					'post_status'		=> 'inherit',
					'post_content'		=> $description,
					'post_excerpt'		=> $image_caption,
				);
				$attachment_id = wp_insert_attachment($attachment_data, $new_full_filename);
				add_post_meta($attachment_id, '_fgd2wp_imported', 1, true); // To delete the imported attachments
			}
			
			if ( !empty($attachment_id) ) {
				if ( preg_match('/(image|audio|video)/', $filetype) ) { // Image, audio or video
					if ( !$this->plugin_options['skip_thumbnails'] ) {
						// you must first include the image.php file
						// for the function wp_generate_attachment_metadata() to work
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attach_data = wp_generate_attachment_metadata( $attachment_id, $new_full_filename );
						wp_update_attachment_metadata($attachment_id, $attach_data);
					}

					// Image Alt
					if ( !empty($image_alt) ) {
						update_post_meta($attachment_id, '_wp_attachment_image_alt', addslashes($image_alt)); // update_post_meta expects slashed
					}
				}
				return $attachment_id;
			} else {
				return false;
			}
		}
		
		/**
		 * Check if the attachment exists in the database
		 *
		 * @param string $name
		 * @return object Post
		 */
		private function get_attachment_from_name($name) {
			$name = preg_replace('/\.[^.]+$/', '', basename($name));
			$r = array(
				'name'			=> $name,
				'post_type'		=> 'attachment',
				'numberposts'	=> 1,
			);
			$posts_array = get_posts($r);
			if ( is_array($posts_array) && (count($posts_array) > 0) ) {
				return $posts_array[0];
			}
			else {
				return false;
			}
		}

		/**
		 * Stop the import
		 * 
		 */
		public function stop_import() {
			update_option('fgd2wp_stop_import', true);
		}
		
		/**
		 * Test if the import needs to stop
		 * 
		 * @return boolean Import needs to stop or not
		 */
		public function import_stopped() {
			return get_option('fgd2wp_stop_import');
		}
		
		/**
		 * Return the excerpt and the content of a node
		 *
		 * @param array $node Node data
		 * @return array ($excerpt, $content)
		 */
		public function set_excerpt_content($node) {
			$excerpt = '';
			$content = isset($node['body_value'])? $node['body_value'] : '';
			
			switch ( $this->plugin_options['summary'] ) {
				case 'in_excerpt':
					if ( !empty($node['body_summary']) ) {
						$excerpt = $node['body_summary'];
					}
					break;
				
				case 'in_content':
					// Posts with a "Read more" link
					if ( !empty($node['body_summary']) ) {
						$content = $node['body_summary'] . "\n<!--more-->\n" . $content;
					}
					break;
				
				case 'in_excerpt_and_content':
					if ( !empty($node['body_summary']) ) {
						$excerpt = $node['body_summary'];
						$content = $excerpt . "\n\n" . $content;
					}
					break;
			}
			return array($excerpt, $content);
		}

		/**
		 * Process the post content
		 *
		 * @param string $content Post content
		 * @param array $post_media Post medias
		 * @return string Processed post content
		 */
		public function process_content($content, $post_media) {

			if ( !empty($content) ) {
				$content = apply_filters('fgd2wp_pre_process_content', $content, $post_media);
				
				// Replace page breaks
				$content = preg_replace("#<hr([^>]*?)class=\"system-pagebreak\"(.*?)/>#", "<!--nextpage-->", $content);

				// Replace media URLs with the new URLs
				$content = $this->process_content_media_links($content, $post_media);

				// For importing backslashes
				$content = addslashes($content);
			}

			return $content;
		}

		/**
		 * Replace media URLs with the new URLs
		 *
		 * @param string $content Post content
		 * @param array $post_media Post medias
		 * @return string Processed post content
		 */
		private function process_content_media_links($content, $post_media) {
			$matches = array();
			$matches_caption = array();

			if ( is_array($post_media) ) {

				// Get the attachments attributes
				$attachments_found = false;
				$medias = array();
				foreach ( $post_media as $old_filename => $attachment_id ) {
					$media = array();
					$media['attachment_id'] = $attachment_id;
					$media['url_old_filename'] = urlencode($old_filename); // for filenames with spaces or accents
					if ( preg_match('/image/', get_post_mime_type($attachment_id)) ) {
						// Image
						$image_src = wp_get_attachment_image_src($attachment_id, 'full');
						$media['new_url'] = $image_src[0];
						$media['width'] = $image_src[1];
						$media['height'] = $image_src[2];
					} else {
						// Other media
						$media['new_url'] = wp_get_attachment_url($attachment_id);
					}
					$medias[$old_filename] = $media;
					$attachments_found = true;
				}
				if ( $attachments_found ) {

					// Remove the links from the content
					$this->post_link_count = 0;
					$this->post_link = array();
					$content = preg_replace_callback('#<(a) (.*?)(href)=(.*?)</a>#i', array($this, 'remove_links'), $content);
					$content = preg_replace_callback('#<(img) (.*?)(src)=(.*?)>#i', array($this, 'remove_links'), $content);

					// Process the stored medias links
					foreach ($this->post_link as &$link) {
						$new_link = $link['old_link'];
						$alignment = '';
						if ( preg_match('/(align="|float: )(left|right)/', $new_link, $matches) ) {
							$alignment = 'align' . $matches[2];
						} elseif ( preg_match('/class="align-(left|right)/', $new_link, $matches) ) {
							$alignment = 'align' . $matches[1];
						}
						if ( preg_match_all('#(src|href)="(.*?)"#i', $new_link, $matches, PREG_SET_ORDER) ) {
							$caption = '';
							foreach ( $matches as $match ) {
								$old_filename = $match[2];
								$link_type = ($match[1] == 'src')? 'img': 'a';
								if ( array_key_exists($old_filename, $medias) ) {
									$media = $medias[$old_filename];
									if ( array_key_exists('new_url', $media) ) {
										if ( (strpos($new_link, $old_filename) > 0) || (strpos($new_link, $media['url_old_filename']) > 0) ) {
											// URL encode the filename
											$new_filename = basename($media['new_url']);
											$encoded_new_filename = rawurlencode($new_filename);
											$new_url = str_replace($new_filename, $encoded_new_filename, $media['new_url']);
											$new_link = preg_replace('#"(' . preg_quote($old_filename) . '|' . preg_quote($media['url_old_filename']) . ')"#', '"' . $new_url . '"', $new_link, 1);

											if ( $link_type == 'img' ) { // images only
												// Define the width and the height of the image if it isn't defined yet
												if ((strpos($new_link, 'width=') === false) && (strpos($new_link, 'height=') === false)) {
													$width_assertion = isset($media['width']) && !empty($media['width'])? ' width="' . $media['width'] . '"' : '';
													$height_assertion = isset($media['height']) && !empty($media['height'])? ' height="' . $media['height'] . '"' : '';
												} else {
													$width_assertion = '';
													$height_assertion = '';
												}

												// Caption shortcode
												if ( preg_match('/class=".*caption.*?"/', $link['old_link']) ) {
													if ( preg_match('/title="(.*?)"/', $link['old_link'], $matches_caption) ) {
														$caption_value = str_replace('%', '%%', $matches_caption[1]);
														$align_value = ($alignment != '')? $alignment : 'alignnone';
														$caption = '[caption id="attachment_' . $media['attachment_id'] . '" align="' . $align_value . '"' . $width_assertion . ']%s' . $caption_value . '[/caption]';
													}
												}

												$align_class = ($alignment != '')? $alignment . ' ' : '';
												$new_link = preg_replace('#<img(.*?)( class="(.*?)")?(.*) />#', "<img$1 class=\"$3 " . $align_class . 'size-full wp-image-' . $media['attachment_id'] . "\"$4" . $width_assertion . $height_assertion . ' />', $new_link);
											}
										}
									}
								}
							}

							// Add the caption
							if ( $caption != '' ) {
								$new_link = sprintf($caption, $new_link);
							}
						}
						$link['new_link'] = $new_link;
					}

					// Reinsert the converted medias links
					$content = preg_replace_callback('#__fg_link_(\d+)__#', array($this, 'restore_links'), $content);
				}
			}
			return $content;
		}

		/**
		 * Remove all the links from the content and replace them with a specific tag
		 * 
		 * @param array $matches Result of the preg_match
		 * @return string Replacement
		 */
		private function remove_links($matches) {
			$this->post_link[] = array('old_link' => $matches[0]);
			return '__fg_link_' . $this->post_link_count++ . '__';
		}

		/**
		 * Restore the links in the content and replace them with the new calculated link
		 * 
		 * @param array $matches Result of the preg_match
		 * @return string Replacement
		 */
		private function restore_links($matches) {
			$link = $this->post_link[$matches[1]];
			$new_link = array_key_exists('new_link', $link)? $link['new_link'] : $link['old_link'];
			return $new_link;
		}

		/**
		 * Add a link between a media and a post (parent id + thumbnail)
		 *
		 * @param int $post_id Post ID
		 * @param array $post_data Post data
		 * @param array $post_media Post medias IDs
		 * @param boolean $set_featured_image Set the featured image?
		 */
		public function add_post_media($post_id, $post_data, $post_media, $set_featured_image=true) {
			$thumbnail_is_set = false;
			if ( is_array($post_media) ) {
				foreach ( $post_media as $attachment_id ) {
					$attachment = get_post($attachment_id);
					if ( !empty($attachment) ) {
						$attachment->post_parent = $post_id; // Attach the post to the media
						$attachment->post_date = $post_data['post_date'] ;// Define the media's date
						wp_update_post($attachment);

						// Set the featured image. If not defined, it is the first image of the content.
						if ( (strpos($attachment->post_mime_type, 'image') === 0) && $set_featured_image && !$thumbnail_is_set ) {
							set_post_thumbnail($post_id, $attachment_id);
							$thumbnail_is_set = true;
						}
					}
				}
			}
		}

		/**
		 * Modify the internal links of all posts
		 *
		 */
		private function modify_links() {
			$step = 1000; // to limit the results
			$offset = 0;

			$message = __('Modifying internal links...', 'fg-drupal-to-wp');
			if ( defined('WP_CLI') ) {
				$posts_count = $this->count_posts('post') + $this->count_posts('page');
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $posts_count);
			} else {
				$this->log($message);
			}
			
			// Hook for doing other actions before modifying the links
			do_action('fgd2wp_pre_modify_links');

			do {
				$args = array(
					'numberposts'	=> $step,
					'offset'		=> $offset,
					'orderby'		=> 'ID',
					'order'			=> 'ASC',
					'post_type'		=> 'any',
					'post_status'	=> 'any',
				);
				$posts = get_posts($args);
				foreach ( $posts as $post ) {
					$current_links_count = $this->links_count;
					$post = apply_filters('fgd2wp_post_get_post', $post); // Used to translate the links
					
					// Modify the links in the content
					$content = $this->modify_links_in_string($post->post_content);
					$content = apply_filters('fgd2wp_modify_links_in_content', $content);
					
					if ( $this->links_count != $current_links_count ) { // Some links were modified
						// Update the post
						wp_update_post(array(
							'ID'			=> $post->ID,
							'post_content'	=> $content,
						));
						$post->post_content = $content;
					}
					
					do_action('fgd2wp_post_modify_post_links', $post);
					
					if ( defined('WP_CLI') ) {
						$progress_cli->tick();
					}
				}
				$offset += $step;
			} while ( !is_null($posts) && (count($posts) > 0) );

			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			
			// Hook for doing other actions after modifying the links
			do_action('fgd2wp_post_modify_links');
		}
		
		/**
		 * Modify the links in a string
		 * 
		 * @since 2.8.0
		 * 
		 * @param string $content Content
		 * @return string Content
		 */
		public function modify_links_in_string($content) {
			$matches = array();
			if ( preg_match_all('#<a(.*?)href="(.*?)"(.*?)>#', $content, $matches, PREG_SET_ORDER) > 0 ) {
				if ( is_array($matches) ) {
					foreach ( $matches as $match ) {
						$link = $match[2];
						list($link_without_anchor, $anchor_link) = $this->split_anchor_link($link); // Split the anchor link
						// Is it an internal link ?
						if ( !empty($link_without_anchor) && $this->is_internal_link($link_without_anchor) ) {
							$new_link = $this->modify_link($link_without_anchor);

							// Replace the link in the post content
							if ( !empty($new_link) ) {
								if ( !empty($anchor_link) ) {
									$new_link .= '#' . $anchor_link;
								}
								$content = str_replace("href=\"$link\"", "href=\"$new_link\"", $content);
								$this->links_count++;
							}
						}
					}
				}
			}
			return $content;
		}
		
		/**
		 * Modify a link
		 * 
		 * @since 2.8.0
		 * 
		 * @param string $link Link
		 * @return string Link modified
		 */
		private function modify_link($link) {
			$new_link = '';
			
			// Find a post link or a term link
			$linked_object = $this->get_wp_object_from_drupal_url($link);

			if ( is_a($linked_object, 'WP_Post') ) {
				// Post found
				$linked_post_id = $linked_object->ID;
				$linked_post_id = apply_filters('fgd2wp_post_get_post_by_drupal_id', $linked_post_id); // Used to get the ID of the translated post
				$new_link = get_permalink($linked_post_id);

			} elseif ( is_a($linked_object, 'WP_Term') ) {
				// Term found
				$linked_term_id = $linked_object->term_id;
				$linked_term_id = apply_filters('fgd2wp_post_get_term_by_drupal_id', $linked_term_id); // Used to get the ID of the translated term
				$new_link = get_term_link($linked_term_id);
			}
			return $new_link;
		}
		
		/**
		 * Test if the link is an internal link or not
		 *
		 * @param string $link
		 * @return bool
		 */
		private function is_internal_link($link) {
			$result = (preg_match("#^".$this->plugin_options['url']."#", $link) > 0) ||
				(preg_match("#^(http|//)#", $link) == 0);
			return $result;
		}
		
		/**
		 * Get a WordPress post or term that matches a Drupal URL
		 * 
		 * @since 1.6.0
		 * 
		 * @param string $url URL
		 * @return WP_Post | WP_Term | null
		 */
		private function get_wp_object_from_drupal_url($url) {
			$object = null;
			$object_type = '';
			$object_name = $this->remove_html_extension(basename($url));
			
			// Try to find a post by its post name
			$object_id = $this->get_post_by_name($object_name);
			if ( $object_id ) {
				$object_type = 'post';
			}
			
			// Try to find a post or a term in the redirect table
			if ( empty($object_id) && class_exists('FG_Drupal_to_WordPress_Redirect') ) {
				$redirect_obj = new FG_Drupal_to_WordPress_Redirect();
				$object_redir = $redirect_obj->find_url_in_redirect_table($object_name);
				if ( $object_redir ) {
					$object_id = $object_redir->id;
					if ( post_type_exists($object_redir->type) ) {
						$object_type = 'post';
					} elseif ( taxonomy_exists($object_redir->type) ) {
						$object_type = 'term';
					}
				}
			}
			
			// Try to find a post or a term by an ID in the URL
			if ( empty($object_id) ) {
				$meta_key_value = $this->get_drupal_id_in_link($url);
				if ( isset($meta_key_value['meta_key']) ) {
					switch ( $meta_key_value['meta_key'] ) {
						case '_fgd2wp_old_node_id':
						case '_fgd2wp_old_media_id':
							$object_id = $this->get_wp_post_id_from_meta($meta_key_value['meta_key'], $meta_key_value['meta_value']);
							$object_type = 'post';
							break;
						case '_fgd2wp_old_taxonomy_id':
							$object_id = $this->get_wp_term_id_from_meta($meta_key_value['meta_key'], $meta_key_value['meta_value']);
							$object_type = 'term';
							break;
					}
				}
			}
			
			if ( !empty($object_id) ) {
				switch ( $object_type ) {
					case 'post':
						$object = get_post($object_id);
						break;
					case 'term':
						$object = get_term($object_id);
						break;
				}
			}
			if ( !$object ) {
				$object = apply_filters('fgd2wp_get_wp_object_from_drupal_url', $object, $url);
			}
			return $object;
		}

		/**
		 * Remove the file extension .html
		 * 
		 * @param string $url URL
		 * @return string URL
		 */
		private function remove_html_extension($url) {
			$url = preg_replace('/\.html$/', '', $url);
			return $url;
		}
		
		/**
		 * Get a post by its name
		 * 
		 * @global object $wpdb
		 * @param string $post_name Post name
		 * @param string $post_type Post type
		 * @return int $post_id Post ID
		 */
		private function get_post_by_name($post_name, $post_type = 'post') {
			global $wpdb;
			$post_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $post_name, $post_type));
			return $post_id;
		}
		
		/**
		 * Get the Drupal ID in a link
		 *
		 * @param string $link
		 * @return array('meta_key' => $meta_key, 'meta_value' => $meta_value)
		 */
		private function get_drupal_id_in_link($link) {
			$matches = array();

			$meta_key_value = array(
				'meta_key'		=> '',
				'meta_value'	=> 0);
			$meta_key_value = apply_filters('fgd2wp_pre_get_drupal_id_in_link', $meta_key_value, $link);
			if ( $meta_key_value['meta_value'] == 0 ) {
				if ( preg_match("#node/(\d+)#", $link, $matches) || preg_match("#node-(\d+)-#", $link, $matches) ) {
					$meta_key_value['meta_value'] = $matches[1];
					$meta_key_value['meta_key'] = '_fgd2wp_old_node_id';
					
				} elseif ( preg_match("#taxonomy/term/(\d+)#", $link, $matches) || preg_match("#term-(\d+)-#", $link, $matches) ) {
					$meta_key_value['meta_value'] = $matches[1];
					$meta_key_value['meta_key'] = '_fgd2wp_old_taxonomy_id';
					
				} else {
					$meta_key_value = apply_filters('fgd2wp_post_get_drupal_id_in_link', $meta_key_value);
				}
			}
			return $meta_key_value;
		}

		/**
		 * Split a link by its anchor link
		 * 
		 * @param string $link Original link
		 * @return array(string link, string anchor_link) [link without anchor, anchor_link]
		 */
		private function split_anchor_link($link) {
			$pos = strpos($link, '#');
			if ( $pos !== false ) {
				// anchor link found
				$link_without_anchor = substr($link, 0, $pos);
				$anchor_link = substr($link, $pos + 1);
				return array($link_without_anchor, $anchor_link);
			} else {
				// anchor link not found
				return array($link, '');
			}
		}

		/**
		 * Copy a remote file
		 * in replacement of the copy function
		 * 
		 * @deprecated
		 * @param string $url URL of the source file
		 * @param string $path destination file
		 * @return boolean
		 */
		public function remote_copy($url, $path) {
			return $this->download_manager->copy($url, $path);
		}

		/**
		 * Recount the items for a taxonomy
		 * 
		 * @return boolean
		 */
		private function terms_tax_count($taxonomy) {
			$terms = get_terms(array($taxonomy));
			// Get the term taxonomies
			$terms_taxonomies = array();
			foreach ( $terms as $term ) {
				$terms_taxonomies[] = $term->term_taxonomy_id;
			}
			if ( !empty($terms_taxonomies) ) {
				return wp_update_term_count_now($terms_taxonomies, $taxonomy);
			} else {
				return true;
			}
		}

		/**
		 * Recount the items for each category and tag
		 * 
		 * @return boolean
		 */
		private function terms_count() {
			$result = $this->terms_tax_count('category');
			$result |= $this->terms_tax_count('post_tag');
		}

		/**
		 * Guess the Drupal version
		 *
		 * @return string Drupal version
		 */
		private function drupal_version() {
			$version = '';
			if ( $this->table_exists('taxonomy_term__parent') ) {
				$version = '8.5';
			} elseif ( $this->table_exists('config') ) {
				$version = '8';
			} elseif ( $this->table_exists('field_config') ) {
				$version = '7';
			} elseif ( $this->table_exists('content_node_field') ) {
				$version = '6';
			} elseif ( $this->table_exists('node_type') ) {
				$version = '5';
			} else {
				$version = '4';
			}
			return $version;
		}

		/**
		 * Returns the imported posts mapped with their Drupal ID
		 *
		 * @param string $meta_key Meta key (default = _fgd2wp_old_node_id)
		 * @return array of post IDs [drupal_article_id => wordpress_post_id]
		 */
		public function get_imported_drupal_posts($meta_key = '_fgd2wp_old_node_id') {
			global $wpdb;
			$posts = array();

			$sql = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '$meta_key'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$posts[$result->meta_value] = $result->post_id;
			}
			ksort($posts);
			return $posts;
		}

		/**
		 * Returns the imported posts (including their post type) mapped with their Drupal ID
		 *
		 * @param string $meta_key Meta key (default = _fgd2wp_old_node_id)
		 * @return array of post IDs [drupal_article_id => [wordpress_post_id, wordpress_post_type]]
		 */
		public function get_imported_drupal_posts_with_post_type($meta_key = '_fgd2wp_old_node_id') {
			global $wpdb;
			$posts = array();

			$sql = "
				SELECT pm.post_id, pm.meta_value, p.post_type
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = '$meta_key'
			";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$posts[$result->meta_value] = array(
					'post_id' => $result->post_id,
					'post_type' => $result->post_type,
				);
			}
			ksort($posts);
			return $posts;
		}

		/**
		 * Returns the imported post ID corresponding to a Drupal ID
		 *
		 * @param int $drupal_id Drupal article ID
		 * @param string $entity_type Entity type (node, media)
		 * @return int WordPress post ID
		 */
		public function get_wp_post_id_from_drupal_id($drupal_id, $entity_type='node') {
			$post_id = $this->get_wp_post_id_from_meta('_fgd2wp_old_' . $entity_type . '_id', $drupal_id);
			return $post_id;
		}

		/**
		 * Returns the imported post ID corresponding to a meta key and value
		 *
		 * @param string $meta_key Meta key
		 * @param string $meta_value Meta value
		 * @return int WordPress post ID
		 */
		public function get_wp_post_id_from_meta($meta_key, $meta_value) {
			global $wpdb;

			$sql = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '$meta_key' AND meta_value = '$meta_value' LIMIT 1";
			$post_id = $wpdb->get_var($sql);
			return $post_id;
		}

		/**
		 * Get a Post ID from its GUID
		 * 
		 * @since 2.30.0
		 * 
		 * @global object $wpdb
		 * @param string $guid GUID
		 * @return int Post ID
		 */
		public function get_post_id_from_guid($guid) {
			global $wpdb;
			return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid));
		}
		
		/**
		 * Returns the imported term ID corresponding to a meta key and value
		 *
		 * @since      1.0.0
		 * 
		 * @param string $meta_key Meta key
		 * @param string $meta_value Meta value
		 * @return int WordPress category ID
		 */
		public function get_wp_term_id_from_meta($meta_key, $meta_value) {
			global $wpdb;

			$sql = "SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key = '$meta_key' AND meta_value = '$meta_value' LIMIT 1";
			$term_id = $wpdb->get_var($sql);
			return $term_id;
		}

		/**
		 * Returns the imported term taxonomies (including their taxonomy) mapped with their Drupal ID
		 *
		 * @since 1.4.0
		 * 
		 * @return array of terms [drupal_taxonomy_id => [wordpress_term_id, wordpress_taxonomy]]
		 */
		public function get_imported_drupal_taxonomies() {
			global $wpdb;
			$terms = array();

			$sql = "
				SELECT tm.term_id, tm.meta_value, tt.taxonomy
				FROM {$wpdb->termmeta} tm
				INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = tm.term_id
				WHERE tm.meta_key = '_fgd2wp_old_taxonomy_id'
			";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$terms[$result->meta_value] = array(
					'term_id' => $result->term_id,
					'taxonomy' => $result->taxonomy,
				);
			}
			ksort($terms);
			return $terms;
		}

		/**
		 * Returns the imported users mapped with their Drupal ID
		 *
		 * @return array of user IDs [drupal_user_id => wordpress_user_id]
		 */
		public function get_imported_drupal_users() {
			global $wpdb;
			$users = array();

			$sql = "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = '_fgd2wp_old_user_id'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$users[$result->meta_value] = $result->user_id;
			}
			ksort($users);
			return $users;
		}

		/**
		 * Test if a column exists
		 *
		 * @param string $table Table name
		 * @param string $column Column name
		 * @return bool
		 */
		public function column_exists($table, $column) {
			global $drupal_db;

			if ( !$drupal_db ) {
				return false;
			}
			$cache_key = 'fgd2wp_column_exists:' . $table . '.' . $column;
			$found = false;
			$column_exists = wp_cache_get($cache_key, '', false, $found);
			if ( $found === false ) {
				$column_exists = false;
				try {
					$prefix = $this->plugin_options['prefix'];

					switch ( $this->plugin_options['driver'] ) {
						case 'mysql':
							$sql = "SHOW COLUMNS FROM ${prefix}${table} LIKE '$column'";
							break;
						case 'postgresql':
							$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '${prefix}${table}' AND column_name = '$column'";
							break;
						case 'sqlite':
							$sql = "PRAGMA table_info(${prefix}${table})";
							break;
					}
					$query = $drupal_db->query($sql, PDO::FETCH_ASSOC);
					if ( $query !== false ) {
						if ( $this->plugin_options['driver'] == 'sqlite' ) {
							// SQLite
							$result = $query->fetchAll();
							foreach ( $result as $row ) {
								if ( isset($row['name']) && ($row['name'] == $column) ) {
									$column_exists = true;
									break;
								}
							}
						} else {
							// MySQL & PostgreSQL
							$result = $query->fetch();
							$column_exists = !empty($result);
						}
					}
				} catch ( PDOException $e ) {}
				
				// Store the result in cache for the current request
				wp_cache_set($cache_key, $column_exists);
			}
			return $column_exists;
		}

		/**
		 * Test if a table exists
		 *
		 * @param string $table Table name
		 * @return bool
		 */
		public function table_exists($table) {
			global $drupal_db;

			if ( !$drupal_db ) {
				return false;
			}
			$cache_key = 'fgd2wp_table_exists:' . $table;
			$found = false;
			$table_exists = wp_cache_get($cache_key, '', false, $found);
			if ( $found === false ) {
				$table_exists = false;
				try {
					$prefix = $this->plugin_options['prefix'];

					switch ( $this->plugin_options['driver'] ) {
						case 'mysql':
							$sql = "SHOW TABLES LIKE '${prefix}${table}'";
							break;
						case 'postgresql':
							$sql = "SELECT tablename FROM pg_catalog.pg_tables WHERE tablename='${prefix}${table}';";
							break;
						case 'sqlite':
							$sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='${prefix}${table}';";
							break;
					}
					$query = $drupal_db->query($sql, PDO::FETCH_ASSOC);
					if ( $query !== false ) {
						$result = $query->fetch();
						$table_exists = !empty($result);
					}
				} catch ( PDOException $e ) {}
				
				// Store the result in cache for the current request
				wp_cache_set($cache_key, $table_exists);
			}
			return $table_exists;
		}

		/**
		 * Test if a remote file exists
		 * 
		 * @param string $filePath
		 * @return boolean True if the file exists
		 */
		public function url_exists($filePath) {
			$url = str_replace(' ', '%20', $filePath);
			
			// Try the get_headers method
			$headers = @get_headers($url);
			$result = preg_match("/200/", $headers[0]);
			
			if ( !$result && strpos($filePath, 'https:') !== 0 ) {
				// Try the fsock method
				$url = str_replace('http://', '', $url);
				if ( strstr($url, '/') ) {
					$url = explode('/', $url, 2);
					$url[1] = '/' . $url[1];
				} else {
					$url = array($url, '/');
				}

				$fh = fsockopen($url[0], 80);
				if ( $fh ) {
					fputs($fh,'GET ' . $url[1] . " HTTP/1.1\nHost:" . $url[0] . "\n");
					fputs($fh,"User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36\n\n");
					$response = fread($fh, 22);
					fclose($fh);
					$result = (strpos($response, '200') !== false);
				} else {
					$result = false;
				}
			}
			
			return $result;
		}
		
		/**
		 * Get all the term metas corresponding to a meta key
		 * 
		 * @param string $meta_key Meta key
		 * @return array List of term metas: term_id => meta_value
		 */
		public function get_term_metas_by_metakey($meta_key) {
			global $wpdb;
			$metas = array();
			
			$sql = "SELECT term_id, meta_value FROM {$wpdb->termmeta} WHERE meta_key = '$meta_key'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$metas[$result->meta_value] = $result->term_id;
			}
			ksort($metas);
			return $metas;
		}
		
		/**
		 * Search a term by its slug (LIKE search)
		 * 
		 * @param string $slug slug
		 * @return int Term id
		 */
		public function get_term_id_by_slug($slug) {
			global $wpdb;
			return $wpdb->get_var("
				SELECT term_id FROM $wpdb->terms
				WHERE slug LIKE '$slug'
			");
		}
		
		/**
		 * Get a Drupal variable
		 * 
		 * @since 1.9.0
		 * 
		 * @param string $variable_name Variable name
		 * @return string Variable value
		 */
		public function get_drupal_variable($variable_name) {
			$variable = '';
			if ( $this->table_exists('variable') ) {
				$prefix = $this->plugin_options['prefix'];

				$sql = "
					SELECT v.value
					FROM ${prefix}variable v
					WHERE v.name = '$variable_name'
					LIMIT 1
				";
				$result = $this->drupal_query($sql);
				if ( isset($result[0]) ) {
					$value = $result[0]['value'];
					if ( is_resource($value) ) { // PostgreSQL bytea type
						$value = stream_get_contents($value);
					}
					$variable = unserialize($value);
				}
			}
			return $variable;
		}
		
		/**
		 * Get a Drupal config
		 * 
		 * @since 2.13.0
		 * 
		 * @param string $query Query
		 * @param string $collection Collection
		 * @return string Config name and data
		 */
		public function get_drupal_config_like($query, $collection='') {
			$config = array();
			if ( $this->table_exists('config') ) {
				$prefix = $this->plugin_options['prefix'];
				$sql = "
					SELECT c.name, c.data
					FROM ${prefix}config c
					WHERE c.name LIKE '$query'
					AND collection = '$collection'
				";
				$result = $this->drupal_query($sql);
				foreach ( $result as $row ) {
					$config[$row['name']] = unserialize($row['data']);
				}
			}
			return $config;
		}
		
	}
}
