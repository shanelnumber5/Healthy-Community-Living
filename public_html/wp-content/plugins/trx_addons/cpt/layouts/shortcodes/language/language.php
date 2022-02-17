<?php
/**
 * Shortcode: Display WPML Language Selector
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.18
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_language_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_language_load_scripts_front');
	function trx_addons_sc_layouts_language_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts_menu', trx_addons_get_file_url('cpt/layouts/shortcodes/menu/menu.css'), array(), null );
			wp_enqueue_style( 'trx_addons-sc_layouts_language', trx_addons_get_file_url('cpt/layouts/shortcodes/language/language.css'), array(), null );
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_language_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_language_merge_styles');
	function trx_addons_sc_layouts_language_merge_styles($list) {
		$list[] = 'cpt/layouts/shortcodes/language/language.css';
		return $list;
	}
}

	

// trx_sc_layouts_language
//-------------------------------------------------------------
/*
[trx_sc_layouts_language id="unique_id"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_language' ) ) {
	function trx_addons_sc_layouts_language($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_language', $atts, array(
			// Individual params
			"type" => "default",
			"flag" => "both",
			"title_link" => "name",
			"title_menu" => "name",
			"hide_on_tablet" => "0",
			"hide_on_mobile" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', trx_addons_get_file_url('cpt/layouts/shortcodes/menu/superfish.js'), array('jquery'), null, true );
		// Menu support
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-sc_layouts_menu', trx_addons_get_file_url('cpt/layouts/shortcodes/menu/menu.js'), array('jquery'), null, true );
		}

		ob_start();
		trx_addons_get_template_part(array(
										'cpt/layouts/shortcodes/language/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/layouts/shortcodes/language/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_language',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_language', $atts, $content);
	}
}


// Add [trx_sc_layouts_language] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_language_add_in_vc')) {
	function trx_addons_sc_layouts_language_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;

		add_shortcode("trx_sc_layouts_language", "trx_addons_sc_layouts_language");

		vc_lean_map("trx_sc_layouts_language", 'trx_addons_sc_layouts_language_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Language extends WPBakeryShortCode {}
	}

	add_action('init', 'trx_addons_sc_layouts_language_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_language_add_in_vc_params')) {
	function trx_addons_sc_layouts_language_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_language",
				"name" => esc_html__("Layouts: Language", 'trx_addons'),
				"description" => wp_kses_data( __("Insert WPML Language Selector", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_language',
				"class" => "trx_sc_layouts_language",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array(
								esc_html__('Default', 'trx_addons') => 'default',
							), 'trx_sc_layouts_language' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "flag",
							"heading" => esc_html__("Show flag", 'trx_addons'),
							"description" => wp_kses_data( __("Where you want to show flag?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
					        'save_always' => true,
							"std" => "both",
							"value" => array(
								esc_html__('Hide', 'trx_addons') => "none",
								esc_html__('Only in the title', 'trx_addons') => "title",
								esc_html__('Only in the menu', 'trx_addons') => "menu",
								esc_html__('Both', 'trx_addons') => "both",
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title_link",
							"heading" => esc_html__("Show link's title", 'trx_addons'),
							"description" => wp_kses_data( __("Select link's title type", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "name",
							"value" => array(
								esc_html__('Hide', 'trx_addons') => "none",
								esc_html__('Language name', 'trx_addons') => "name",
								esc_html__('Language code', 'trx_addons') => "code",
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title_menu",
							"heading" => esc_html__("Show menu item's title", 'trx_addons'),
							"description" => wp_kses_data( __("Select menu item's title type", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "name",
							"value" => array(
								esc_html__('Hide', 'trx_addons') => "none",
								esc_html__('Language name', 'trx_addons') => "name",
								esc_html__('Language code', 'trx_addons') => "code",
							),
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_language');
	}
}
?>