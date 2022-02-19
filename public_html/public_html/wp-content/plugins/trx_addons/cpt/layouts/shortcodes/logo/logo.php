<?php
/**
 * Shortcode: Display site Logo
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_logo_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_logo_load_scripts_front');
	function trx_addons_sc_layouts_logo_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts_logo', trx_addons_get_file_url('cpt/layouts/shortcodes/logo/logo.css'), array(), null );
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_logo_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_logo_merge_styles');
	function trx_addons_sc_layouts_logo_merge_styles($list) {
		$list[] = 'cpt/layouts/shortcodes/logo/logo.css';
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_logo_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_logo_merge_scripts');
	function trx_addons_sc_layouts_logo_merge_scripts($list) {
		$list[] = 'cpt/layouts/shortcodes/logo/logo.js';
		return $list;
	}
}



// trx_sc_layouts_logo
//-------------------------------------------------------------
/*
[trx_sc_layouts_logo id="unique_id" logo="image_url" logo_retina="image_url"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_logo' ) ) {
	function trx_addons_sc_layouts_logo($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_logo', $atts, array(
			// Individual params
			"type" => "default",
			"logo" => "",
			"logo_retina" => "",
			"logo_text" => "",
			"logo_slogan" => "",
			"hide_on_tablet" => "0",
			"hide_on_mobile" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (trx_addons_is_on(trx_addons_get_option('debug_mode')))
			wp_enqueue_script( 'trx_addons-sc_layouts_logo', trx_addons_get_file_url('cpt/layouts/shortcodes/logo/logo.js'), array('jquery'), null, true );

		// Get logo from current theme (if empty)
		if (empty($atts['logo'])) {
			$logo = apply_filters('trx_addons_filter_theme_logo', '');
			if (is_array($logo)) {
				$atts['logo'] = !empty($logo['logo']) ? $logo['logo'] : '';
				$atts['logo_retina'] = !empty($logo['logo_retina']) ? $logo['logo_retina'] : $atts['logo_retina'];
			} else
				$atts['logo'] = $logo;
		}
		
		ob_start();
		trx_addons_get_template_part(array(
										'cpt/layouts/shortcodes/logo/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/layouts/shortcodes/logo/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_logo',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_logo', $atts, $content);
	}
}


// Add [trx_sc_layouts_logo] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_logo_add_in_vc')) {
	function trx_addons_sc_layouts_logo_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;

		add_shortcode("trx_sc_layouts_logo", "trx_addons_sc_layouts_logo");
		
		vc_lean_map("trx_sc_layouts_logo", 'trx_addons_sc_layouts_logo_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_logo extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_logo_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_logo_add_in_vc_params')) {
	function trx_addons_sc_layouts_logo_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_logo",
				"name" => esc_html__("Layouts: Logo", 'trx_addons'),
				"description" => wp_kses_data( __("Insert site logo to the custom layout", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_logo',
				"class" => "trx_sc_layouts_logo",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array(
								esc_html__('Default', 'trx_addons') => 'default'
							), 'trx_sc_layouts_logo' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "logo",
							"heading" => esc_html__("Logo", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for site's logo.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "attach_image"
						),
						array(
							"param_name" => "logo_retina",
							"heading" => esc_html__("Logo Retina", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site: site's logo for the Retina display.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "attach_image"
						),
						array(
							"param_name" => "logo_text",
							"heading" => esc_html__("Logo text", 'trx_addons'),
							"description" => wp_kses_data( __("Site name (used if logo is empty). If not specified - use blog name", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "logo_slogan",
							"heading" => esc_html__("Logo slogan", 'trx_addons'),
							"description" => wp_kses_data( __("Slogan or description below site name (used if logo is empty). If not specified - use blog description", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_logo');
	}
}
?>