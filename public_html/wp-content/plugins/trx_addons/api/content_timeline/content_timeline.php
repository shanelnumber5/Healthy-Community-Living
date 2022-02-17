<?php
/**
 * Plugin support: Content Timeline
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if plugin is installed and activated
if ( !function_exists( 'trx_addons_exists_content_timeline' ) ) {
	function trx_addons_exists_content_timeline() {
		return class_exists( 'ContentTimelineAdmin' );
	}
}

// Return Content Timelines list, prepended inherit (if need)
if ( !function_exists( 'trx_addons_get_list_content_timelines' ) ) {
	function trx_addons_get_list_content_timelines($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			if (trx_addons_exists_content_timeline()) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT id, name FROM " . esc_sql($wpdb->prefix . 'ctimelines') );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->id] = $row->name;
					}
				}
			}
		}
		return $prepend_inherit ? array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) : $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_content_timeline_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_content_timeline_importer_required_plugins', 10, 2 );
	function trx_addons_content_timeline_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'content_timeline')!==false && !trx_addons_exists_content_timeline() )
			$not_installed .= '<br>' . esc_html__('Content Timeline', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_content_timeline_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_content_timeline_importer_set_options' );
	function trx_addons_content_timeline_importer_set_options($options=array()) {
		if ( trx_addons_exists_content_timeline() && in_array('content_timeline', $options['required_plugins']) ) {
			//$options['additional_options'][] = 'content_timeline_calendar_options';
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_content_timeline'] = str_replace('name.ext', 'content_timeline.txt', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_content_timeline_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_content_timeline_importer_show_params', 10, 1 );
	function trx_addons_content_timeline_importer_show_params($importer) {
		if ( trx_addons_exists_content_timeline() && in_array('content_timeline', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'content_timeline',
				'title' => esc_html__('Import Content Timeline', 'trx_addons'),
				'part' => 0
			));
		}
	}
}

// Import posts
if ( !function_exists( 'trx_addons_content_timeline_importer_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import',	'trx_addons_content_timeline_importer_import', 10, 2 );
	function trx_addons_content_timeline_importer_import($importer, $action) {
		if ( trx_addons_exists_content_timeline() && in_array('content_timeline', $importer->options['required_plugins']) ) {
			if ( $action == 'import_content_timeline' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump('content_timeline', esc_html__('Content Timeline', 'trx_addons'));
			}
		}
	}
}

// Display import progress
if ( !function_exists( 'trx_addons_content_timeline_importer_import_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_content_timeline_importer_import_fields', 10, 1 );
	function trx_addons_content_timeline_importer_import_fields($importer) {
		if ( trx_addons_exists_content_timeline() && in_array('content_timeline', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields(array(
				'slug'	=> 'content_timeline', 
				'title'	=> esc_html__('Content Timeline', 'trx_addons')
				)
			);
		}
	}
}

// Export posts
if ( !function_exists( 'trx_addons_content_timeline_importer_export' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export',	'trx_addons_content_timeline_importer_export', 10, 1 );
	function trx_addons_content_timeline_importer_export($importer) {
		if ( trx_addons_exists_content_timeline() && in_array('content_timeline', $importer->options['required_plugins']) ) {
			trx_addons_fpc(trx_addons_get_file_dir('importer/export/content_timeline.txt'), serialize( array(
				'ctimelines' => $importer->export_dump('ctimelines')
				) )
			);
		}
	}
}

// Display exported data in the fields
if ( !function_exists( 'trx_addons_content_timeline_importer_export_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_content_timeline_importer_export_fields', 10, 1 );
	function trx_addons_content_timeline_importer_export_fields($importer) {
		if ( trx_addons_exists_content_timeline() && in_array('content_timeline', $importer->options['required_plugins']) ) {
			$importer->show_exporter_fields(array(
				'slug'	=> 'content_timeline',
				'title' => esc_html__('Content Timeline', 'trx_addons')
				)
			);
		}
	}
}



// VC support
//------------------------------------------------------------------------

// Add [content_timeline] in the VC shortcodes list
if (!function_exists('trx_addons_sc_content_timeline_add_in_vc')) {
	function trx_addons_sc_content_timeline_add_in_vc() {

		if (!trx_addons_exists_visual_composer() || !trx_addons_exists_content_timeline()) return;

		vc_lean_map( "content_timeline", 'trx_addons_sc_content_timeline_add_in_vc_params');
		class WPBakeryShortCode_Content_Timeline extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_content_timeline_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_content_timeline_add_in_vc_params')) {
	function trx_addons_sc_content_timeline_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "content_timeline",
				"name" => esc_html__("Content Timeline", 'trx_addons'),
				"description" => esc_html__("Insert Content timeline", 'trx_addons'),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_content_timeline',
				"class" => "trx_sc_content_timeline",
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Timeline", 'trx_addons'),
						"description" => esc_html__("Select Timeline to insert into current page", 'trx_addons'),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_content_timelines()),
						"type" => "dropdown"
					)
				)
			), 'content_timeline' );
	}
}
?>