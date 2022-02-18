<?php
/*
Plugin Name: ThemeREX Addons
Plugin URI: http://themerex.net
Description: Add many widgets, shortcodes and custom post types for your theme
Version: 1.6.28.3
Author: ThemeREX
Author URI: http://themerex.net
*/

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );

// Current version
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) define( 'TRX_ADDONS_VERSION', '1.6.28.3' );

// Hooks order for the plugin and theme on action 'after_setup_theme':
// 1 - plugin's components and/or theme register hooks for next filters:
//     'trx_addons_filter_options' - to add/remove plugin options array
//     'trx_addons_cpt_list' - to enable/disable plugin's CPT
//     'trx_addons_sc_list' - to enable/disable plugin's shortcodes
//     'trx_addons_widgets_list' - to enable/disable plugin's widgets
//     'trx_addons_cv_enable' - to enable/disable plugin's CV functionality
// 3 - plugin do apply_filters('trx_addons_filter_options', $options) and load options
// 4 - plugin save options (if on the ThemeREX Addons Options page)
// 6 - plugin include components (shortcodes, widgets, CPT, etc.) filtered by theme hooks

// Plugin's storage
if (!defined('TRX_ADDONS_PLUGIN_DIR'))	define('TRX_ADDONS_PLUGIN_DIR', plugin_dir_path(__FILE__));
if (!defined('TRX_ADDONS_PLUGIN_URL'))	define('TRX_ADDONS_PLUGIN_URL', plugin_dir_url(__FILE__));
if (!defined('TRX_ADDONS_PLUGIN_BASE'))	define('TRX_ADDONS_PLUGIN_BASE',dirname(plugin_basename(__FILE__)));

//global $TRX_ADDONS_STORAGE;
$TRX_ADDONS_STORAGE = array(
	// Plugin's custom post types
	'post_types' => array(),
	// Plugin's messages with last operation's result
	'message' => array(
		'error' => '',
		'success' => ''
	),
	// Arguments to register widgets
	'widgets_args' => array(
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget_title">',
		'after_title'   => '</h5>',
	),
    // Shortcodes stack
    'sc_stack' => array(),
    'sc_stack_data' => array(),
	// Other components
	'components_list' => array(
		'cv' => array(
				'title' => __('CV Card', 'trx_addons')
				)
	),
	// Profiler points
	'profiler_points' => array()
);

// Next files must be loaded before options
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.socials.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.files.php';

// Plugin's internal utilities
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.debug.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.utils.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.media.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.wp.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.lists.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.html.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.users.php';

// Plugin's options
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.options.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.options.components.php';
if (is_admin()) {
	require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.admin.php';
	require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.options.customizer.php';
	require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.options.meta-box.php';
}

// Third-party plugins support
require_once TRX_ADDONS_PLUGIN_DIR . 'api/js_composer/js_composer.php';	// Must be first
require_once TRX_ADDONS_PLUGIN_DIR . 'api/bbpress.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/booked/booked.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/calculated-fields-form/calculated-fields-form.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/contact-form-7.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/content_timeline/content_timeline.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/essential-grid.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/instagram-feed.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/mailchimp-for-wp.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/revslider.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/the-events-calendar/the-events-calendar.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/trx_donations/trx_donations.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/twitter/twitter.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/ubermenu.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/vc-extensions-bundle.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/woocommerce/woocommerce.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/wpml.php';
require_once TRX_ADDONS_PLUGIN_DIR . 'api/elegro-payment/elegro-payment.php';

// Extra buttons into TinyMCE Editor
require_once TRX_ADDONS_PLUGIN_DIR . 'editor/editor.php';

// Custom post types
require_once TRX_ADDONS_PLUGIN_DIR . 'cpt/cpt.php';

// Widgets
require_once TRX_ADDONS_PLUGIN_DIR . 'widgets/widgets.php';

// Shortcodes
require_once TRX_ADDONS_PLUGIN_DIR . 'shortcodes/shortcodes.php';

// CV
require_once TRX_ADDONS_PLUGIN_DIR . 'cv/cv.php';

// Importer
require_once TRX_ADDONS_PLUGIN_DIR . 'importer/importer.php';


//-------------------------------------------------------
//-- Plugin init
//-------------------------------------------------------

// Plugin activate hook
if (!function_exists('trx_addons_activate')) {
	register_activation_hook(__FILE__, 'trx_addons_activate');
	function trx_addons_activate() {
		update_option('trx_addons_just_activated', 'yes');
	}
}

// Plugin init (after init custom post types and after all other plugins)
if ( !function_exists('trx_addons_init') ) {
	add_action( 'init', 'trx_addons_init', 11 );
	function trx_addons_init() {

		// Add thumb sizes
		$thumb_sizes = apply_filters('trx_addons_filter_add_thumb_sizes', array(
			'trx_addons-thumb-huge'			=> array(1170,658, true),
			'trx_addons-thumb-big'			=> array(770, 433, true),
			'trx_addons-thumb-big-avatar'	=> array(570, 560, true),
			'trx_addons-thumb-medium'		=> array(370, 208, true),
			'trx_addons-thumb-small'		=> array(270, 152, true),
			'trx_addons-thumb-portrait'		=> array(370, 493, true),
			'trx_addons-thumb-avatar'		=> array(370, 370, true),
			'trx_addons-thumb-tiny'			=> array( 75,  75, true),
			'trx_addons-thumb-masonry-big'	=> array(770,   0, false),	// Only downscale, not crop
			'trx_addons-thumb-masonry'		=> array(370,   0, false)	// Only downscale, not crop
			)
		);
		$mult = trx_addons_get_option('retina_ready', 1);
		foreach ($thumb_sizes as $k=>$v) {
			// Add Original dimensions
			add_image_size( $k, $v[0], $v[1], $v[2]);
			// Add Retina dimensions
			if ($mult > 1) add_image_size( $k.'-@retina', $v[0]*$mult, $v[1]*$mult, $v[2]);
		}

		// Check if this is first run - flush rewrite rules
		if (get_option('trx_addons_just_activated')=='yes') {
			update_option('trx_addons_just_activated', 'no');
			flush_rewrite_rules();			
		}
	}
}



//-------------------------------------------------------
//-- Featured images
//-------------------------------------------------------
if ( !function_exists('trx_addons_image_sizes') ) {
	add_filter( 'image_size_names_choose', 'trx_addons_image_sizes' );
	function trx_addons_image_sizes( $sizes ) {
		$thumb_sizes = apply_filters('trx_addons_filter_add_thumb_sizes', array(
			'trx_addons-thumb-big'		=> esc_html__( 'Large image', 'trx_addons' ),
			'trx_addons-thumb-big-avatar'=> esc_html__( 'Large avatar image', 'trx_addons' ),
			'trx_addons-thumb-med'		=> esc_html__( 'Medium image', 'trx_addons' ),
			'trx_addons-thumb-small'	=> esc_html__( 'Small image', 'trx_addons' ),
			'trx_addons-thumb-portrait'	=> esc_html__( 'Portrait', 'trx_addons' ),
			'trx_addons-thumb-avatar'	=> esc_html__( 'Big square avatar', 'trx_addons' ),
			'trx_addons-thumb-tiny'		=> esc_html__( 'Small square avatar', 'trx_addons' ),
			'trx_addons-thumb-masonry'	=> esc_html__( 'Masonry (scaled)', 'trx_addons' )
			)
		);
		$mult = trx_addons_get_option('retina_ready', 1);
		foreach($thumb_sizes as $k=>$v) {
			$sizes[$k] = $v;
			if ($mult > 1) $sizes[$k.'-@retina'] = $v.' '.esc_html('@2x', 'trx_addons' );
		}
		return $sizes;
	}
}


//-------------------------------------------------------
//-- Load scripts and styles
//-------------------------------------------------------

// Redirect browser 'Safari mobile' from iframe-version to the whole page version
// because it incorrectly detect height of the window in the iframe
if ( !function_exists( 'trx_addons_safari_to_top' ) ) {
	add_action('wp_head', 'trx_addons_safari_to_top', 0);
	function trx_addons_safari_to_top() {
		if (wp_is_mobile() && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'themeforest')) {
			?><script>navigator.userAgent.match(/iPad|iPhone|iPod/i) != null && window.name != '' && top.location.href != location.href && (top.location.href = location.href);</script><?php
		}
	}
}
	
// Load required styles and scripts in the admin mode
if ( !function_exists( 'trx_addons_load_scripts_admin' ) ) {
	add_action("admin_enqueue_scripts", 'trx_addons_load_scripts_admin');
	function trx_addons_load_scripts_admin() {
		// Fontello icons must be loaded before main stylesheet
		if (strpos($_SERVER['REQUEST_URI'], 'post.php')!==false 
			|| strpos($_SERVER['REQUEST_URI'], 'post-new.php')!==false
			|| strpos($_SERVER['REQUEST_URI'], 'edit-tags.php')!==false
			|| strpos($_SERVER['REQUEST_URI'], 'term.php')!==false
			|| strpos($_SERVER['REQUEST_URI'], 'widgets.php')!==false
			) {
			wp_enqueue_style( 'trx_addons-icons', trx_addons_get_file_url('css/font-icons/css/trx_addons_icons-embedded.css') );
			wp_enqueue_style( 'trx_addons-icons-animation', trx_addons_get_file_url('css/font-icons/css/animation.css') );
		}
		wp_enqueue_style( 'trx_addons-admin', trx_addons_get_file_url('css/trx_addons.admin.css'), array(), null );
        wp_enqueue_script( 'trx_addons-admin', trx_addons_get_file_url('js/trx_addons.admin.js'), array('jquery', 'wp-color-picker'), null, true );
		wp_enqueue_script( 'trx_addons-utils', trx_addons_get_file_url('js/trx_addons.utils.js'), array('jquery'), null, true );
	}
}
	
// Add variables in the admin mode
if ( !function_exists( 'trx_addons_localize_scripts_admin' ) ) {
	add_action("admin_footer", 'trx_addons_localize_scripts_admin');
	function trx_addons_localize_scripts_admin() {
		// Add variables into JS
		wp_localize_script( 'trx_addons-admin', 'TRX_ADDONS_STORAGE', apply_filters('trx_addons_localize_script_admin', array(
			// AJAX parameters
			'ajax_url'	=> esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce'=> esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))),
			// Site base url
			'site_url'	=> esc_url(get_site_url())
			) )
		);
	}
}

	
// Load required styles and scripts in the frontend
if ( !function_exists( 'trx_addons_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_load_scripts_front');
	function trx_addons_load_scripts_front() {

		// Fontello icons must be loaded before main stylesheet
		wp_enqueue_style( 'trx_addons-icons', trx_addons_get_file_url('css/font-icons/css/trx_addons_icons-embedded.css') );

		// Load Swiper slider script and styles
		trx_addons_enqueue_slider();

		// Load Popup script and styles
		trx_addons_enqueue_popup();

		// If 'debug_mode' is off - load merged style and scripts
		if (trx_addons_is_off(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons', trx_addons_get_file_url('css/trx_addons.css'), array(), null );
			wp_enqueue_script( 'trx_addons', trx_addons_get_file_url('js/trx_addons.js'), array('jquery'), null, true );

		// Else load all styles and scripts separate
		} else {
			wp_enqueue_style( 'trx_addons', trx_addons_get_file_url('css/trx_addons.front.css'), array(), null );
			wp_enqueue_style( 'trx_addons-hovers', trx_addons_get_file_url('css/trx_addons.hovers.css'), array(), null );
			wp_enqueue_script( 'trx_addons-utils', trx_addons_get_file_url('js/trx_addons.utils.js'), array('jquery'), null, true );
			wp_enqueue_script( 'trx_addons', trx_addons_get_file_url('js/trx_addons.front.js'), array('jquery'), null, true );
		}
		
		// Conditions to load animation.css - not mobile and not VC Frontend
		if ( !wp_is_mobile() && (!function_exists('trx_addons_vc_is_frontend') || !trx_addons_vc_is_frontend()))
			wp_enqueue_style( 'trx_addons-animation',	trx_addons_get_file_url('css/trx_addons.animation.css') );
	}
}

// Add variables in the frontend
if ( !function_exists( 'trx_addons_localize_scripts_front' ) ) {
	add_action("wp_footer", 'trx_addons_localize_scripts_front');
	function trx_addons_localize_scripts_front() {
		wp_localize_script( 'trx_addons', 'TRX_ADDONS_STORAGE', apply_filters('trx_addons_localize_script', array(
			// AJAX parameters
			'ajax_url'	=> esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce'=> esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))),
			// Site base url
			'site_url'	=> esc_url(get_site_url()),
			// Is single page/post
			'post_id' => get_the_ID(),
			// VC frontend edit mode
			'vc_edit_mode'	=> function_exists('trx_addons_vc_is_frontend') && trx_addons_vc_is_frontend() ? 1 : 0,
			// Popup engine
			'popup_engine'=> trx_addons_get_option('popup_engine'),
			// Animate to the inner links
			'animate_inner_links'=> trx_addons_get_option('animate_inner_links'),
			// User logged in
			'user_logged_in'=> is_user_logged_in() ? 1 : 0,
			// E-mail mask to validate forms
			'email_mask' => '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$',
			// JS Messages
			'msg_ajax_error'			=> addslashes(esc_html__('Invalid server answer!', 'trx_addons')),
			'msg_magnific_loading'		=> addslashes(esc_html__('Loading image', 'trx_addons')),
			'msg_magnific_error'		=> addslashes(esc_html__('Error loading image', 'trx_addons')),
			'msg_error_like'			=> addslashes(esc_html__('Error saving your like! Please, try again later.', 'trx_addons')),
			'msg_field_name_empty'		=> addslashes(esc_html__("The name can't be empty", 'trx_addons')),
			'msg_field_email_empty'		=> addslashes(esc_html__('Too short (or empty) email address', 'trx_addons')),
			'msg_field_email_not_valid'	=> addslashes(esc_html__('Invalid email address', 'trx_addons')),
			'msg_field_text_empty'		=> addslashes(esc_html__("The message text can't be empty", 'trx_addons')),
			'msg_search_error'			=> addslashes(esc_html__('Search error! Try again later.', 'trx_addons')),
			'msg_send_complete'			=> addslashes(esc_html__("Send message complete!", 'trx_addons')),
			'msg_send_error'			=> addslashes(esc_html__('Transmit failed!', 'trx_addons'))
			) )
		);
	}
}



// Capture all 'body' output to insert inline styles to the 'head'
if ( !function_exists( 'trx_addons_grab_inline_styles' ) ) {
	add_action('wp_head', 'trx_addons_grab_inline_styles', 9999);
	add_action('admin_head', 'trx_addons_grab_inline_styles', 9999);
	function trx_addons_grab_inline_styles() {
		
		// --------------- Start capture HTML-output -----------------
		if (trx_addons_is_on(trx_addons_get_option('move_styles_to_head'))) ob_start();
		
		// Add Google Analitics code (before </head>)
		if (current_action()=='wp_head') {
			$ga = trx_addons_get_option('api_google_analitics');
			if (!empty($ga)) trx_addons_show_layout($ga);
		}
	}
}
	
// Load inline styles
if ( !function_exists( 'trx_addons_add_inline_styles' ) ) {
	add_action('wp_footer', 'trx_addons_add_inline_styles', 9999);
	add_action('admin_footer', 'trx_addons_add_inline_styles', 9999);
	function trx_addons_add_inline_styles() {

		// Put custom html/js, prepared in shortcodes or any other output blocks
		trx_addons_show_layout(apply_filters('trx_addons_filter_inline_html', trx_addons_get_inline_html()));

		// Add Google Remarketing code (before </body>)
		if (current_action()=='wp_footer') {
			$gr = trx_addons_get_option('api_google_remarketing');
			if (!empty($gr)) trx_addons_show_layout($gr);
		}

		// ------------------ End capture HTML-output --------------------
		$output = '';
		if (trx_addons_is_on(trx_addons_get_option('move_styles_to_head'))) {
			$output = ob_get_contents();
            if (ob_get_contents()) { ob_end_clean(); }
		}
		// Attention! Don't change id in the tag 'style' - need to properly work the 'view more' script
		trx_addons_show_layout(apply_filters('trx_addons_filter_inline_css', trx_addons_get_inline_css()), '<style type="text/css" id="trx_addons-inline-styles-inline-css">', '</style>');
		if (!empty($output)) {
			$pos = $pos2 = -1;
			do {
				$pos = strpos($output, '<style', $pos2 + 1);
				if ($pos > 0) {
					$pos2 = strpos($output, '</style>', $pos+6);
					if ($pos2 > 0) {
						trx_addons_show_layout(substr($output, $pos, $pos2+8-$pos));
						$output = substr($output, 0, $pos) . substr($output, $pos2+8);
						$pos2 = $pos - 1;
					}
				}
			} while ($pos > 0 && $pos2 > 0);
			trx_addons_show_layout($output);
		}
	}
}



// Merge all separate styles and scripts into single file to increase page upload speed
if ( !function_exists( 'trx_addons_merge_styles' ) ) {
	add_action('trx_addons_action_save_options', 'trx_addons_merge_styles');
	function trx_addons_merge_styles() {
		$msg = 	'/* ' . strip_tags( __("ATTENTION! This file was generated automatically! Don't change it!!!", 'trx_addons') ) 
				. "\n----------------------------------------------------------------------- */\n";
		// Merge styles
		$list = apply_filters( 'trx_addons_filter_merge_styles', array(
																'css/trx_addons.front.css',
																'css/trx_addons.hovers.css'
																)
							);
		$css = '';
		foreach ($list as $f) {
			$css .= trx_addons_fgc(trx_addons_get_file_dir($f));
		}
		if ( $css != '') {
			trx_addons_fpc( trx_addons_get_file_dir('css/trx_addons.css'), $msg . trx_addons_minify_css( $css ) );
		}

		// Merge scripts
		$list = apply_filters( 'trx_addons_filter_merge_scripts', array(
																	'js/trx_addons.utils.js',
																	'js/trx_addons.front.js'
																	)
							);
		$js = '';
		foreach ($list as $f) {
			$js .= trx_addons_fgc(trx_addons_get_file_dir($f));
		}
		if ( $js != '') {
			trx_addons_fpc( trx_addons_get_file_dir('js/trx_addons.js'), $msg . trx_addons_minify_js( $js ) );
		}

	}
}



//-------------------------------------------------------
//-- Utilities
//-------------------------------------------------------

// Load plugin's translation file
// Attention! It must be loaded before the first call of any translation function
if ( !function_exists( 'trx_addons_load_plugin_textdomain' ) ) {
	add_action( 'plugins_loaded', 'trx_addons_load_plugin_textdomain');
	function trx_addons_load_plugin_textdomain() {
		static $loaded = false;
		if ( $loaded ) return true;
		$domain = 'trx_addons';
		if ( is_textdomain_loaded( $domain ) && !is_a( $GLOBALS['l10n'][ $domain ], 'NOOP_Translations' ) ) return true;
		$loaded = true;
		load_plugin_textdomain( $domain, false, TRX_ADDONS_PLUGIN_BASE . '/languages' );
	}
}


// Return result of last operation
if ( !function_exists( 'trx_addons_message' ) ) {
	function trx_addons_message($type=false, $msg=false) {
		global $TRX_ADDONS_STORAGE;
		if ($type===false)
			return $TRX_ADDONS_STORAGE['message'];
		else if ($msg===false)
			return $TRX_ADDONS_STORAGE['message'][$type];
		else
			$TRX_ADDONS_STORAGE['message'][$type] = $msg;
	}
}

// Set internal message from last operation
if (($msg = get_option('trx_addons_message')) != '') {
	trx_addons_message('success', $msg);
	update_option('trx_addons_message', '');
}


//------------------------------------------------------------
//-- Compatibility Gutenberg and other PageBuilders
//-------------------------------------------------------------

// Prevent simultaneous editing of posts for Gutenberg and other PageBuilders (VC, Elementor)
if ( ! function_exists( 'trx_addons_gutenberg_disable_cpt' ) ) {
    add_action( 'current_screen', 'trx_addons_gutenberg_disable_cpt' );
    function trx_addons_gutenberg_disable_cpt() {
        if ( trx_addons_get_setting( 'disable_gutenberg_on_other_pagebuilders' ) && trx_addons_exists_gutenberg() ) {
            $current_post_type = get_current_screen()->post_type;
            $disable = false;
            if ( !$disable && trx_addons_exists_elementor() ) {
                $post_types = get_post_types_by_support( 'elementor' );
                $disable = is_array($post_types) && in_array($current_post_type, $post_types);
            }
            if ( !$disable && trx_addons_exists_vc() ) {
                $post_types = function_exists('vc_editor_post_types') ? vc_editor_post_types() : array();
                $disable = is_array($post_types) && in_array($current_post_type, $post_types);
            }
            if ( $disable ) {
                remove_filter( 'replace_editor', 'gutenberg_init' );
                remove_action( 'load-post.php', 'gutenberg_intercept_edit_post' );
                remove_action( 'load-post-new.php', 'gutenberg_intercept_post_new' );
                remove_action( 'admin_init', 'gutenberg_add_edit_link_filters' );
                remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' );
                remove_action( 'admin_print_scripts-edit.php', 'gutenberg_replace_default_add_new_button' );
                remove_action( 'admin_enqueue_scripts', 'gutenberg_editor_scripts_and_styles' );
                remove_filter( 'screen_options_show_screen', '__return_false' );
            }
        }
    }
}

// Check if plugin 'WPBakery PageBuilder' (old name is 'Visual Composer') is installed and activated
if ( !function_exists( 'trx_addons_exists_vc' ) ) {
    function trx_addons_exists_vc() {
        return class_exists('Vc_Manager');
    }
}

// Check if plugin 'Elementor' is installed and activated
if ( !function_exists( 'trx_addons_exists_elementor' ) ) {
    function trx_addons_exists_elementor() {
        return class_exists('Elementor\Plugin');
    }
}

// Check if plugin 'Gutenberg' is installed and activated
if ( !function_exists( 'trx_addons_exists_gutenberg' ) ) {
    function trx_addons_exists_gutenberg() {
        return function_exists( 'the_gutenberg_project' ) && function_exists( 'register_block_type' );
    }
}

//-------------------------------------------------------
//-- Delayed action from previous session
//-- (after save options)
//-- to save new CSS, etc.
//-------------------------------------------------------
if ( !function_exists('trx_addons_do_delayed_action') ) {
	add_action( 'after_setup_theme', 'trx_addons_do_delayed_action' );
	function trx_addons_do_delayed_action() {
		if (($action = get_option('trx_addons_action')) != '') {
		    do_action($action);
			update_option('trx_addons_action', '');
		}
	}
}
?>