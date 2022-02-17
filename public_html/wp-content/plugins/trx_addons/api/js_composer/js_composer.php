<?php
/**
 * Plugin support: WPBakery Page Builder
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if WPBakery Page Builder installed and activated
if ( !function_exists( 'trx_addons_exists_visual_composer' ) ) {
	function trx_addons_exists_visual_composer() {
		return class_exists('Vc_Manager');
	}
}

// Check if WPBakery Page Builder in frontend editor mode
if ( !function_exists( 'trx_addons_vc_is_frontend' ) ) {
	function trx_addons_vc_is_frontend() {
		return (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true')
			|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline');
		//return function_exists('vc_is_frontend_editor') && vc_is_frontend_editor();
	}
}

// Add new param's option to the specified param
if ( !function_exists( 'trx_addons_vc_add_param_option' ) ) {
	function trx_addons_vc_add_param_option($params, $param_name, $option) {
		if (is_array($params)) {
			foreach($params as $k=>$v) {
				if (isset($v['param_name']) && $v['param_name']==$param_name) {
					$params[$k] = array_merge($v, $option);
					break;
				}
			}
		}
		return $params;
	}
}

// Delete param from list
if ( !function_exists( 'trx_addons_vc_remove_param' ) ) {
	function trx_addons_vc_remove_param($params, $param_name) {
		if (is_array($params)) {
			foreach($params as $k=>$v) {
				if (isset($v['param_name']) && $v['param_name']==$param_name) {
					unset($params[$k]);
					break;
				}
			}
		}
		return $params;
	}
}

// Add div before vc_new_row in the vc_edit_form
if ( !function_exists( 'trx_addons_vc_edit_form_start' ) ) {
    add_action( 'wp_ajax_vc_edit_form', 'trx_addons_vc_edit_form_start', 0 );
    function trx_addons_vc_edit_form_start() {
        if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '6.0.3', '<' ) ) {
            ob_start();
        }
    }
}
if ( !function_exists( 'trx_addons_vc_edit_form_end' ) ) {
    add_filter( 'vc_edit_form_fields_after_render', 'trx_addons_vc_edit_form_end');
    function trx_addons_vc_edit_form_end( $output = '' ) {
        if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '6.0.3', '<' ) ) {
            $output = ob_get_contents();
            ob_end_clean();
        }
        $output = preg_replace('/(<div[^>]*class="[^"]*vc_new_row)/', '<div class="vc_new_row_before"></div>$1', $output, -1, $count);
        if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '6.0.3', '<' ) ) {
            trx_addons_show_layout($output);
        }
        return $output;
    }
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_vc_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_vc_load_scripts_front');
	function trx_addons_vc_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-js_composer', trx_addons_get_file_url('api/js_composer/js_composer.css'), array(), null );
			wp_enqueue_script( 'trx_addons-js_composer', trx_addons_get_file_url('api/js_composer/js_composer.js'), array('jquery'), null, true );
		}
	}
}

	
// Merge specific styles into single stylesheet
if ( !function_exists( 'trx_addons_vc_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_vc_merge_styles');
	function trx_addons_vc_merge_styles($list) {
		$list[] = 'api/js_composer/js_composer.css';
		return $list;
	}
}

	
// Merge plugin's specific scripts into single file
if ( !function_exists( 'trx_addons_vc_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_vc_merge_scripts');
	function trx_addons_vc_merge_scripts($list) {
		$list[] = 'api/js_composer/js_composer.js';
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_vc_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_vc_importer_required_plugins', 10, 2 );
	function trx_addons_vc_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'js_composer')!==false && !trx_addons_exists_visual_composer())
			$not_installed .= '<br>' . esc_html__('WPBakery Page Builder', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_vc_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_vc_importer_set_options' );
	function trx_addons_vc_importer_set_options($options=array()) {
		if ( trx_addons_exists_visual_composer() && in_array('js_composer', $options['required_plugins']) ) {
			$options['additional_options'][] = 'wpb_js_templates';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}

// Check if the row will be imported
if ( !function_exists( 'trx_addons_vc_importer_check_row' ) ) {
	if (is_admin()) add_filter('trx_addons_filter_importer_import_row', 'trx_addons_vc_importer_check_row', 9, 4);
	function trx_addons_vc_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'js_composer')===false) return $flag;
		if ( trx_addons_exists_visual_composer() ) {
			if ($table == 'posts')
				$flag = $row['post_type']=='vc_grid_item';
		}
		return $flag;
	}
}



// Custom param's types for VC
//------------------------------------------------------------------------
if (trx_addons_exists_visual_composer()) {
	require_once TRX_ADDONS_PLUGIN_DIR . 'api/js_composer/params/radio/radio.php';
	require_once TRX_ADDONS_PLUGIN_DIR . 'api/js_composer/params/icons/icons.php';
}
?>