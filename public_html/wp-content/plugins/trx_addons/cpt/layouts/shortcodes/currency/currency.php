<?php
/**
 * Shortcode: Display WooCommerce Currency Switcher with items number and totals
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.14
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_currency_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_currency_load_scripts_front');
	function trx_addons_sc_layouts_currency_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts_currency', trx_addons_get_file_url('cpt/layouts/shortcodes/currency/currency.css'), array(), null );
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_currency_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_currency_merge_styles');
	function trx_addons_sc_layouts_currency_merge_styles($list) {
		$list[] = 'cpt/layouts/shortcodes/currency/currency.css';
		return $list;
	}
}

	

// trx_sc_layouts_currency
//-------------------------------------------------------------
/*
[trx_sc_layouts_currency id="unique_id" text="Shopping currency"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_currency' ) ) {
	function trx_addons_sc_layouts_currency($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_currency', $atts, array(
			// Individual params
			"type" => "default",
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
										'cpt/layouts/shortcodes/currency/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/layouts/shortcodes/currency/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_currency',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_currency', $atts, $content);
	}
}


// Add [trx_sc_layouts_currency] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_currency_add_in_vc')) {
	function trx_addons_sc_layouts_currency_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;

		add_shortcode("trx_sc_layouts_currency", "trx_addons_sc_layouts_currency");

		vc_lean_map("trx_sc_layouts_currency", 'trx_addons_sc_layouts_currency_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Currency extends WPBakeryShortCode {}
	}

	add_action('init', 'trx_addons_sc_layouts_currency_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_currency_add_in_vc_params')) {
	function trx_addons_sc_layouts_currency_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_currency",
				"name" => esc_html__("Layouts: Currency", 'trx_addons'),
				"description" => wp_kses_data( __("Insert Currency Switcher", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_currency',
				"class" => "trx_sc_layouts_currency",
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
							), 'trx_sc_layouts_currency' ),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_currency');
	}
}
?>