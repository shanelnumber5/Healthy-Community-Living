<?php
/**
 * Shortcode: Display selected widgets area
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.19
 */


// trx_sc_layouts_widgets
//-------------------------------------------------------------
/*
[trx_sc_layouts_widgets id="unique_id" widgets="slug"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_widgets' ) ) {
	function trx_addons_sc_layouts_widgets($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_widgets', $atts, array(
			// Individual params
			"type" => "default",
			"widgets" => "",
			"columns" => "",
			"hide_on_tablet" => "0",
			"hide_on_mobile" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		ob_start();
		trx_addons_get_template_part(array(
										'cpt/layouts/shortcodes/widgets/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/layouts/shortcodes/widgets/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_widgets',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_widgets', $atts, $content);
	}
}


// Add [trx_sc_layouts_widgets] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_widgets_add_in_vc')) {
	function trx_addons_sc_layouts_widgets_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;

		add_shortcode("trx_sc_layouts_widgets", "trx_addons_sc_layouts_widgets");
		
		vc_lean_map("trx_sc_layouts_widgets", 'trx_addons_sc_layouts_widgets_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Widgets extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_widgets_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_widgets_add_in_vc_params')) {
	function trx_addons_sc_layouts_widgets_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_widgets",
				"name" => esc_html__("Layouts: Widgets", 'trx_addons'),
				"description" => wp_kses_data( __("Insert selected widgets area", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_widgets',
				"class" => "trx_sc_layouts_widgets",
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
							), 'trx_sc_layouts_widgets' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "widgets",
							"heading" => esc_html__("Widgets", 'trx_addons'),
							"description" => wp_kses_data( __("Select previously filled widgets area", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "widgetised_sidebars"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Select number columns to show widgets. If 0 - autodetect by the widgets count", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array(0,1,2,3,4,5,6),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_widgets');
	}
}
?>