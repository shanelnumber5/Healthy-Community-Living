<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * @since      1.0.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/includes
 * @author     Frédéric GILLES
 */
class FG_Drupal_to_WordPress_Premium {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since      1.0.0
	 * @access   protected
	 * @var      FG_Drupal_to_WordPress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since      1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since      1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since      1.0.0
	 */
	public function __construct() {

		if ( defined( 'FGD2WPP_PLUGIN_VERSION' ) ) {
			$this->version = FGD2WPP_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'fgd2wpp';
		$this->parent_plugin_name = 'fg-drupal-to-wp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - FG_Drupal_to_WordPress_Loader. Orchestrates the hooks of the plugin.
	 * - FG_Drupal_to_WordPress_i18n. Defines internationalization functionality.
	 * - FG_Drupal_to_WordPress_Admin. Defines all hooks for the admin area.
	 * - FG_Drupal_to_WordPress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since      1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-drupal-to-wp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-drupal-to-wp-i18n.php';

		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';
		if ( !class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				require_once $class_wp_importer;
			}
		}

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-premium-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-drupal-to-wp-tools.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-compatibility.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-progressbar.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-debug-info.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-modules-check.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-download.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-download-fs.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-download-ftp.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-download-http.php';
		
		/**
		 *  FTP functions
		 */
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-ftpext.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-ftp.php';

		/**
		 *  Premium features
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-cli.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-users.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-user-profile.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-menus.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-comments.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-custom-content.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-icpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-cpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-cpt-acf.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-cpt-toolset.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-urls.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-parent-pages.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-video-embed-field.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-image-attach.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-media-entity.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-media.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-bbcode.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-drupal-to-wp-blocks.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fg-drupal-to-wp-users-authenticate.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/Crypt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/PasswordInterface.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/PhpassHashedPassword.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fg-drupal-to-wp-redirect.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fg-drupal-to-wp-url-rewriting.php';

		$this->loader = new FG_Drupal_to_WordPress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the FG_Drupal_to_WordPress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since      1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new FG_Drupal_to_WordPress_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		// Load parent translation file
		$plugin_i18n_parent = new FG_Drupal_to_WordPress_i18n();
		$plugin_i18n_parent->set_domain( $this->get_parent_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n_parent, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since      1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		global $fgd2wpp;
		
		// Add links to the plugin page
		$this->loader->add_filter( 'plugin_action_links_fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php', $this, 'plugin_action_links' );
		
		$this->loader->add_action( 'init', $this, 'acf_hack' ); // Prevent ACF from removing the Custom Fields metabox
		
		/**
		 * The plugin is hooked to the WordPress importer
		 */
		if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') && !defined('DOING_CRON') && !defined('WP_CLI') ) {
			return;
		}

		$plugin_admin = new FG_Drupal_to_WordPress_Premium_Admin( $this->get_plugin_name(), $this->get_version() );
		$fgd2wpp = $plugin_admin; // Used by add-ons

		/*
		 * WP CLI
		 */
		if ( defined('WP_CLI') && WP_CLI ) {
			$plugin_cli = new FG_Drupal_to_WordPress_WPCLI($plugin_admin);
			WP_CLI::add_command('import-drupal', $plugin_cli);
		}
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'init' );
		$this->loader->add_action( 'fgd2wp_post_test_database_connection', $plugin_admin, 'get_drupal_info', 9 );
		$this->loader->add_action( 'load-importer-fgd2wp', $plugin_admin, 'add_help_tab', 20 );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'display_notices', 20 );
		$this->loader->add_action( 'wp_ajax_fgd2wpp_import', $plugin_admin, 'ajax_importer' );
		$this->loader->add_filter( 'fgd2wp_pre_import_check', $plugin_admin, 'pre_import_check', 10, 1 );
		$this->loader->add_filter( 'fgd2wp_get_option_names', $plugin_admin, 'get_option_names', 10, 1 );
		
		/*
		 * Modules checker
		 */
		$plugin_modules_check = new FG_Drupal_to_WordPress_Modules_Check( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_post_test_database_connection', $plugin_modules_check, 'check_modules' );
		
		/*
		 * FTP connection
		 */
		$plugin_ftp = new FG_Drupal_to_WordPress_FTP( $plugin_admin );
		$this->loader->add_filter( 'fgd2wp_post_display_settings_options', $plugin_ftp, 'display_ftp_settings' );
		$this->loader->add_filter( 'fgd2wp_post_save_plugin_options', $plugin_ftp, 'save_ftp_settings' );
		$this->loader->add_action( 'fgd2wp_dispatch', $plugin_ftp, 'test_ftp_connection', 10, 1 );
		$this->loader->add_filter( 'fgd2wp_get_option_names', $plugin_ftp, 'get_option_names', 10, 1 );
		
		/*
		 * Premium features
		 */
		$this->loader->add_action( 'fgd2wp_pre_display_admin_page', $plugin_admin, 'process_admin_page' );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_admin, 'delete_yoastseo_data' );
		$this->loader->add_action( 'fgd2wp_set_plugin_options', $plugin_admin, 'set_premium_options' );
		$this->loader->add_action( 'fgd2wp_post_save_plugin_options', $plugin_admin, 'save_premium_options' );
		$this->loader->add_filter( 'fgd2wp_pre_process_node', $plugin_admin, 'replace_media_shortcodes_in_node' );
		$this->loader->add_action( 'fgd2wp_post_insert_post', $plugin_admin, 'import_drupal5_attachments', 30, 2 );
		
		/*
		 * Users
		 */
		$plugin_users = new FG_Drupal_to_WordPress_Users( $plugin_admin );
		$this->loader->add_filter( 'fgd2wp_get_database_info', $plugin_users, 'get_database_info' );
		$this->loader->add_filter( 'fgd2wp_pre_display_drupal_info', $plugin_users, 'get_users_info' );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_users, 'delete_users', 10, 1 );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_users, 'allow_unicode_usernames' );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_users, 'import_authors' );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_users, 'import_users' );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_users, 'set_imported_users' );
		$this->loader->add_filter( 'fgd2wp_pre_insert_post', $plugin_users, 'set_post_author', 10, 2 );
		$this->loader->add_filter( 'fgd2wp_get_nodes_add_extra_cols', $plugin_users, 'add_user_cols_in_get_nodes', 10, 1 );
		$this->loader->add_filter( 'fgd2wp_get_total_elements_count', $plugin_users, 'get_total_elements_count' );

		/*
		 * User Profile
		 */
		$plugin_user_profile = new FG_Drupal_to_WordPress_User_Profile( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_user_profile, 'init_profile_fields', 9 );
		$this->loader->add_filter( 'fgd2wpp_get_user_first_name', $plugin_user_profile, 'get_user_first_name', 10, 2 );
		$this->loader->add_filter( 'fgd2wpp_get_user_last_name', $plugin_user_profile, 'get_user_last_name', 10, 2 );
		$this->loader->add_filter( 'fgd2wpp_get_user_website', $plugin_user_profile, 'get_user_website', 10, 2 );
		$this->loader->add_filter( 'fgd2wpp_get_user_fields_values', $plugin_user_profile, 'get_user_fields_values', 10, 2 );
		
		/*
		 * Menus
		 */
		$plugin_menus = new FG_Drupal_to_WordPress_Menus( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_menus, 'reset_last_menu_id', 10, 1 );
		$this->loader->add_action( 'fgd2wp_post_import', $plugin_menus, 'import_menus', 50 );
		$this->loader->add_filter( 'fgd2wp_get_total_elements_count', $plugin_menus, 'get_total_elements_count' );

		/*
		 * Comments
		 */
		$plugin_comments = new FG_Drupal_to_WordPress_Comments( $plugin_admin );
		$this->loader->add_filter( 'fgd2wp_get_database_info', $plugin_comments, 'get_database_info' );
		$this->loader->add_filter( 'fgd2wp_pre_display_drupal_info', $plugin_comments, 'get_comments_info' );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_comments, 'reset_last_comment_id' );
		$this->loader->add_filter( 'fgd2wp_get_total_elements_count', $plugin_comments, 'get_total_elements_count' );
		$this->loader->add_action( 'fgd2wp_post_import', $plugin_comments, 'import_comments', 30 ); // Import the comments after all the contents
		
		/*
		 * Blocks
		 */
		$plugin_blocks = new FG_Drupal_to_WordPress_Blocks( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_blocks, 'reset_last_block_id' );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_blocks, 'delete_imported_blocks' );
		$this->loader->add_action( 'fgd2wp_post_import', $plugin_blocks, 'import_blocks' );
		$this->loader->add_filter( 'fgd2wp_get_total_elements_count', $plugin_blocks, 'get_total_elements_count' );

		/*
		 * Custom content
		 */
		$plugin_custom_content = new FG_Drupal_to_WordPress_Custom_Content( $plugin_admin );
		$this->loader->add_filter( 'fgd2wp_get_database_info', $plugin_custom_content, 'get_database_info' );
		$this->loader->add_filter( 'fgd2wp_pre_display_drupal_info', $plugin_custom_content, 'get_custom_content_info' );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_custom_content, 'empty_database' );
		$this->loader->add_filter( 'fgd2wp_database_connection_successful', $plugin_custom_content, 'add_partial_import_nodes_content_to_response' );
		$this->loader->add_action( 'fgd2wp_post_save_plugin_options', $plugin_custom_content, 'save_partial_import_nodes_options' );
		$this->loader->add_action( 'fgd2wp_post_test_database_connection', $plugin_custom_content, 'check_required_plugins' );
		$this->loader->add_filter( 'fgd2wp_get_total_elements_count', $plugin_custom_content, 'get_total_elements_count' );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_custom_content, 'reset_last_custom_content_ids' );
		$this->loader->add_action( 'fgd2wp_set_plugin_options', $plugin_custom_content, 'init_cpt_format' );
		$this->loader->add_action( 'fgd2wp_post_save_plugin_options', $plugin_custom_content, 'init_cpt_format' );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_custom_content, 'register_custom_content', 9 );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_custom_content, 'register_user_fields', 9 );
		$this->loader->add_action( 'fgd2wp_post_import_taxonomies', $plugin_custom_content, 'import_custom_taxonomies_terms' );
		$this->loader->add_action( 'fgd2wp_post_import', $plugin_custom_content, 'import_custom_nodes' );
		$this->loader->add_action( 'fgd2wp_post_import', $plugin_custom_content, 'import_nodes_relations', 20 );
		$this->loader->add_action( 'fgd2wp_post_insert_post', $plugin_custom_content, 'set_node_taxonomies_relations', 10, 4 );
		$this->loader->add_action( 'fgd2wp_post_insert_post', $plugin_custom_content, 'import_node_fields', 10, 4 );
		$this->loader->add_action( 'fgd2wp_post_insert_taxonomy_term', $plugin_custom_content, 'import_term_custom_fields', 10, 3 );
		$this->loader->add_action( 'fgd2wpp_post_add_user', $plugin_custom_content, 'add_user_picture', 10, 2 );
		$this->loader->add_filter( 'fgd2wpp_get_user_first_name', $plugin_custom_content, 'get_user_first_name', 10, 2 );
		$this->loader->add_filter( 'fgd2wpp_get_user_last_name', $plugin_custom_content, 'get_user_last_name', 10, 2 );
		$this->loader->add_filter( 'fgd2wpp_get_user_website', $plugin_custom_content, 'get_user_website', 10, 2 );
		$this->loader->add_action( 'fgd2wpp_post_add_user', $plugin_custom_content, 'import_user_fields_values', 10, 2 );
		$this->loader->add_action( 'fgd2wp_post_modify_post_links', $plugin_custom_content, 'modify_links_in_custom_fields', 10, 1 );

		/*
		 * URLs
		 */
		$plugin_urls = new FG_Drupal_to_WordPress_Urls( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_urls, 'reset_urls' );
		$this->loader->add_action( 'fgd2wp_post_import', $plugin_urls, 'import_urls' );
		$this->loader->add_filter( 'fgd2wp_get_total_elements_count', $plugin_urls, 'get_total_elements_count' );
		
		/*
		 * Parent pages
		 */
		$plugin_parent_pages = new FG_Drupal_to_WordPress_Parent_Pages( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_post_import', $plugin_parent_pages, 'set_parent_pages', 11 ); // After importing the URLs
		
		/*
		 * Video Embed field
		 */
		$plugin_video_embed_field = new FG_Drupal_to_WordPress_Video_Embed_Field( $plugin_admin );
		$this->loader->add_filter( 'fgd2wp_post_get_custom_field', $plugin_video_embed_field, 'get_video_embed_custom_fields', 10, 4 );
		
		/*
		 * Image Attach
		 */
		$plugin_image_attach = new FG_Drupal_to_WordPress_Image_Attach( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_image_attach, 'test_image_attach_data' );
		$this->loader->add_filter( 'fgd2wp_import_media_gallery', $plugin_image_attach, 'import_images', 10, 3 );
		
		/*
		 * Media Entity
		 */
		$plugin_media_entity = new FG_Drupal_to_WordPress_Media_Entity( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_media_entity, 'reset_last_custom_content_ids' );
		$this->loader->add_filter( 'fgd2wp_get_nodes_count_sql', $plugin_media_entity, 'get_nodes_count_sql', 10, 3 );
		$this->loader->add_action( 'fgd2wp_pre_import', $plugin_media_entity, 'get_drupal8_media_taxonomies_fields' );
		$this->loader->add_filter( 'fgd2wp_get_nodes_types_sql', $plugin_media_entity, 'get_nodes_types', 10, 1 );
		$this->loader->add_filter( 'fgd2wp_node_type', $plugin_media_entity, 'build_node_type', 10, 3 );
		$this->loader->add_filter( 'fgd2wp_get_nodes_sql', $plugin_media_entity, 'get_media_entities', 10, 6 );
		$this->loader->add_filter( 'fgd2wp_get_node_taxonomies_terms', $plugin_media_entity, 'get_node_taxonomies_terms', 10, 3 );
		$this->loader->add_filter( 'fgd2wp_get_imported_posts', $plugin_media_entity, 'get_imported_media', 10, 1 );
		
		/*
		 * Media
		 */
		$plugin_media = new FG_Drupal_to_WordPress_Media( $plugin_admin );
		$this->loader->add_filter( 'fgd2wp_pre_process_content', $plugin_media, 'import_media', 10, 1 );
		$this->loader->add_filter( 'fgd2wp_get_custom_field_values', $plugin_media, 'get_caption', 10, 1 );
		
		/*
		 * bbCode
		 */
		$plugin_bbcode = new FG_Drupal_to_WordPress_BbCode( $plugin_admin );
		$this->loader->add_action( 'fgd2wp_pre_process_node', $plugin_bbcode, 'replace_bbcode_in_node' );
		
	}

	/**
	 * Customize the links on the plugins list page
	 *
	 * @param array $links Links
	 * @return array Links
	 */
	public function plugin_action_links($links) {
		// Add the import link
		$import_link = '<a href="admin.php?import=fgd2wp">'. __('Import', $this->plugin_name) . '</a>';
		array_unshift($links, $import_link);
		return $links;
	}

	/**
	 * Prevent ACF from removing the Custom Fields metabox
	 * 
	 * @since 3.4.4
	 */
	public function acf_hack() {
		if ( function_exists('acf_update_setting') ) {
			if ( function_exists('wp_get_environment_type') && (wp_get_environment_type() == 'development') ) { // Development mode only
				acf_update_setting('remove_wp_meta_box', false);
			}
		}
	}
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since      1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		/*
		 * Users authentication
		 */
		$plugin_users_authenticate = new FG_Drupal_to_WordPress_Users_Authenticate();
		$this->loader->add_filter('authenticate', $plugin_users_authenticate, 'auth_signon', 30, 3);
		
		/*
		 * URL redirect
		 */
		$plugin_redirect = new FG_Drupal_to_WordPress_Redirect();
		$this->loader->add_action( 'fgd2wp_post_empty_database', $plugin_redirect, 'empty_redirects' );
		$this->loader->add_action( 'fgd2wpp_post_404_redirect', $plugin_redirect, 'process_url' );
		
		/*
		 * URL rewriting
		 */
		new FG_Drupal_to_WordPress_URL_Rewriting();
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since      1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The name of the parent plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_parent_plugin_name() {
		return $this->parent_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    FG_Drupal_to_WordPress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
