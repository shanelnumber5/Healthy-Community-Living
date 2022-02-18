<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('academee_mailchimp_theme_setup9')) {
	add_action( 'after_setup_theme', 'academee_mailchimp_theme_setup9', 9 );
	function academee_mailchimp_theme_setup9() {
		if (academee_exists_mailchimp()) {
			add_action( 'wp_enqueue_scripts',							'academee_mailchimp_frontend_scripts', 1100 );
			add_filter( 'academee_filter_merge_styles',					'academee_mailchimp_merge_styles');
		}
		if (is_admin()) {
			add_filter( 'academee_filter_tgmpa_required_plugins',		'academee_mailchimp_tgmpa_required_plugins' );
		}
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'academee_exists_mailchimp' ) ) {
	function academee_exists_mailchimp() {
		return function_exists('__mc4wp_load_plugin') || defined('MC4WP_VERSION');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'academee_mailchimp_tgmpa_required_plugins' ) ) {
	
	function academee_mailchimp_tgmpa_required_plugins($list=array()) {
		if (in_array('mailchimp-for-wp', academee_storage_get('required_plugins')))
			$list[] = array(
				'name' 		=> esc_html__('MailChimp for WP', 'academee'),
				'slug' 		=> 'mailchimp-for-wp',
				'required' 	=> false
			);
		return $list;
	}
}



// Custom styles and scripts
//------------------------------------------------------------------------

// Enqueue custom styles
if ( !function_exists( 'academee_mailchimp_frontend_scripts' ) ) {
	
	function academee_mailchimp_frontend_scripts() {
		if (academee_exists_mailchimp()) {
			if (academee_is_on(academee_get_theme_option('debug_mode')) && academee_get_file_dir('plugins/mailchimp-for-wp/mailchimp-for-wp.css')!='')
				wp_enqueue_style( 'academee-mailchimp-for-wp',  academee_get_file_url('plugins/mailchimp-for-wp/mailchimp-for-wp.css'), array(), null );
		}
	}
}
	
// Merge custom styles
if ( !function_exists( 'academee_mailchimp_merge_styles' ) ) {
	
	function academee_mailchimp_merge_styles($list) {
		$list[] = 'plugins/mailchimp-for-wp/mailchimp-for-wp.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if (academee_exists_mailchimp()) { require_once ACADEMEE_THEME_DIR . 'plugins/mailchimp-for-wp/mailchimp-for-wp.styles.php'; }
?>