<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('academee_revslider_theme_setup9')) {
	add_action( 'after_setup_theme', 'academee_revslider_theme_setup9', 9 );
	function academee_revslider_theme_setup9() {
		if (academee_exists_revslider()) {
			add_action( 'wp_enqueue_scripts', 					'academee_revslider_frontend_scripts', 1100 );
			add_filter( 'academee_filter_merge_styles',			'academee_revslider_merge_styles' );
		}
		if (is_admin()) {
			add_filter( 'academee_filter_tgmpa_required_plugins','academee_revslider_tgmpa_required_plugins' );
		}
	}
}

// Check if RevSlider installed and activated
if ( !function_exists( 'academee_exists_revslider' ) ) {
	function academee_exists_revslider() {
		return function_exists('rev_slider_shortcode');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'academee_revslider_tgmpa_required_plugins' ) ) {
	
	function academee_revslider_tgmpa_required_plugins($list=array()) {
		if (in_array('revslider', academee_storage_get('required_plugins'))) {
			$path = academee_get_file_dir('plugins/revslider/revslider.zip');
			$list[] = array(
					'name' 		=> esc_html__('Revolution Slider', 'academee'),
					'slug' 		=> 'revslider',
                    'version'	=> '6.3.9',
					'source'	=> !empty($path) ? $path : 'upload://revslider.zip',
					'required' 	=> false
			);
		}
		return $list;
	}
}
	
// Enqueue custom styles
if ( !function_exists( 'academee_revslider_frontend_scripts' ) ) {
	
	function academee_revslider_frontend_scripts() {
		if (academee_is_on(academee_get_theme_option('debug_mode')) && academee_get_file_dir('plugins/revslider/revslider.css')!='')
			wp_enqueue_style( 'academee-revslider',  academee_get_file_url('plugins/revslider/revslider.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'academee_revslider_merge_styles' ) ) {
	
	function academee_revslider_merge_styles($list) {
		$list[] = 'plugins/revslider/revslider.css';
		return $list;
	}
}
?>