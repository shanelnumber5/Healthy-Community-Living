<?php
/**
 * CV Card: About Me
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Return true if current screen is a about page
if ( !function_exists( 'trx_addons_cv_is_about_page' ) ) {
	add_filter('trx_addons_filter_is_cv_page', 'trx_addons_cv_is_about_page');
	function trx_addons_cv_is_about_page($cv = false) {
		global $post;
		return $cv || (is_page() && $post->ID > 0 && $post->ID == trx_addons_get_option('cv_about_page'));
	}
}


// -----------------------------------------------------------------
// -- Load scripts and styles
// -----------------------------------------------------------------

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cv_about_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cv_about_load_scripts_front');
	function trx_addons_cv_about_load_scripts_front() {
		if (trx_addons_get_value_gp('cv_prn')=='' && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			if (trx_addons_is_cv_page()) {
				wp_enqueue_style( 'trx_addons-cv.about', trx_addons_get_file_url('cv/css/cv.about.css'), array(), null );
			}
		}
	}
}

	
// Merge CV specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cv_about_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cv_about_merge_styles');
	function trx_addons_cv_about_merge_styles($list) {
		$list[] = 'cv/css/cv.about.css';
		return $list;
	}
}
?>