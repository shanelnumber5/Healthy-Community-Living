<?php
/**
 * Plugin support: Booked Appointments
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if plugin is installed and activated
if ( !function_exists( 'trx_addons_exists_booked' ) ) {
	function trx_addons_exists_booked() {
		return class_exists( 'booked_plugin' );
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_booked_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_booked_importer_required_plugins', 10, 2 );
	function trx_addons_booked_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'booked')!==false && !trx_addons_exists_booked() )
			$not_installed .= '<br>' . esc_html__('Booked Appointments', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_booked_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options', 'trx_addons_booked_importer_set_options', 10, 1 );
	function trx_addons_booked_importer_set_options($options=array()) {
		if ( trx_addons_exists_booked() && in_array('booked', $options['required_plugins']) ) {
			$options['additional_options'][] = 'booked_%';				// Add slugs to export options of this plugin
		}
		return $options;
	}
}

// Check if the row will be imported
if ( !function_exists( 'trx_addons_booked_importer_check_row' ) ) {
	if (is_admin()) add_filter('trx_addons_filter_importer_import_row', 'trx_addons_booked_importer_check_row', 9, 4);
	function trx_addons_booked_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'booked')===false) return $flag;
		if ( trx_addons_exists_booked() ) {
			if ($table == 'posts')
				$flag = $row['post_type']=='booked_appointments';
		}
		return $flag;
	}
}


// VC support
//------------------------------------------------------------------------

// Add [cff] in the VC shortcodes list
if (!function_exists('trx_addons_sc_booked_add_in_vc')) {
	function trx_addons_sc_booked_add_in_vc() {

		if (!trx_addons_exists_visual_composer() || !trx_addons_exists_booked()) return;
		
		vc_lean_map( "booked-appointments", 'trx_addons_sc_booked_add_in_vc_params_ba');
		class WPBakeryShortCode_Booked_Appointments extends WPBakeryShortCode {}

		vc_lean_map( "booked-calendar", 'trx_addons_sc_booked_add_in_vc_params_bc');
		class WPBakeryShortCode_Booked_Calendar extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_booked_add_in_vc', 20);
}



// Params for Booked Appointments
if (!function_exists('trx_addons_sc_booked_add_in_vc_params_ba')) {
	function trx_addons_sc_booked_add_in_vc_params_ba() {
		return array(
				"base" => "booked-appointments",
				"name" => esc_html__("Booked Appointments", "trx_addons"),
				"description" => esc_html__("Display the currently logged in user's upcoming appointments", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_booked_appointments',
				"class" => "trx_sc_single trx_sc_booked_appointments",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array()
			);
	}
}
			
// Params for Booked Calendar
if (!function_exists('trx_addons_sc_booked_add_in_vc_params_bc')) {
	function trx_addons_sc_booked_add_in_vc_params_bc() {
		return array(
				"base" => "booked-calendar",
				"name" => esc_html__("Booked Calendar", "trx_addons"),
				"description" => esc_html__("Insert booked calendar", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_booked_calendar',
				"class" => "trx_sc_single trx_sc_booked_calendar",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "calendar",
						"heading" => esc_html__("Calendar", "trx_addons"),
						"description" => esc_html__("Select booked calendar to display", "trx_addons"),
						"admin_label" => true,
						"std" => "0",
						"value" => array_flip(trx_addons_array_merge(array(0 => esc_html__('- Select calendar -', 'trx_addons')), trx_addons_get_list_terms(false, 'booked_custom_calendars'))),
						"type" => "dropdown"
					),
					array(
						"param_name" => "year",
						"heading" => esc_html__("Year", "trx_addons"),
						"description" => esc_html__("Year to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date("Y"),
						"value" => date("Y"),
						"type" => "textfield"
					),
					array(
						"param_name" => "month",
						"heading" => esc_html__("Month", "trx_addons"),
						"description" => esc_html__("Month to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date("m"),
						"value" => date("m"),
						"type" => "textfield"
					)
				)
			);
	}
}
?>