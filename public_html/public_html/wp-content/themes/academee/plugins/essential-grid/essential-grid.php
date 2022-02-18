<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('academee_essential_grid_theme_setup9')) {
	add_action( 'after_setup_theme', 'academee_essential_grid_theme_setup9', 9 );
	function academee_essential_grid_theme_setup9() {
		if (academee_exists_essential_grid()) {
			add_action( 'wp_enqueue_scripts', 							'academee_essential_grid_frontend_scripts', 1100 );
			add_filter( 'academee_filter_merge_styles',					'academee_essential_grid_merge_styles' );
		}
		if (is_admin()) {
			add_filter( 'academee_filter_tgmpa_required_plugins',		'academee_essential_grid_tgmpa_required_plugins' );
		}
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'academee_exists_essential_grid' ) ) {
	function academee_exists_essential_grid() {
		return defined('EG_PLUGIN_PATH');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'academee_essential_grid_tgmpa_required_plugins' ) ) {
	
	function academee_essential_grid_tgmpa_required_plugins($list=array()) {
		if (in_array('essential-grid', academee_storage_get('required_plugins'))) {
			$path = academee_get_file_dir('plugins/essential-grid/essential-grid.zip');
			$list[] = array(
						'name' 		=> esc_html__('Essential Grid', 'academee'),
						'slug' 		=> 'essential-grid',
                        'version'	=> '3.0.11',
						'source'	=> !empty($path) ? $path : 'upload://essential-grid.zip',
						'required' 	=> false
			);
		}
		return $list;
	}
}
	
// Enqueue plugin's custom styles
if ( !function_exists( 'academee_essential_grid_frontend_scripts' ) ) {
	
	function academee_essential_grid_frontend_scripts() {
		if (academee_is_on(academee_get_theme_option('debug_mode')) && academee_get_file_dir('plugins/essential-grid/essential-grid.css')!='')
			wp_enqueue_style( 'academee-essential-grid',  academee_get_file_url('plugins/essential-grid/essential-grid.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'academee_essential_grid_merge_styles' ) ) {
	
	function academee_essential_grid_merge_styles($list) {
		$list[] = 'plugins/essential-grid/essential-grid.css';
		return $list;
	}
}
?>