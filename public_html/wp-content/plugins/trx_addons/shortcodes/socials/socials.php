<?php
/**
 * Shortcode: Socials
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_socials_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_socials_load_scripts_front');
	function trx_addons_sc_socials_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_socials', trx_addons_get_file_url('shortcodes/socials/socials.css'), array(), null );
		}
	}
}
	
// Merge contact form specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_socials_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_socials_merge_styles');
	function trx_addons_sc_socials_merge_styles($list) {
		$list[] = 'shortcodes/socials/socials.css';
		return $list;
	}
}



// trx_sc_socials
//-------------------------------------------------------------
/*
[trx_sc_socials id="unique_id" icons="encoded_json_data"]
*/
if ( !function_exists( 'trx_addons_sc_socials' ) ) {
	function trx_addons_sc_socials($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_socials', $atts, array(
			// Individual params
			"type" => "default",
			"icons" => "",
			"align" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (function_exists('vc_param_group_parse_atts'))
			$atts['icons'] = (array) vc_param_group_parse_atts( $atts['icons'] );

		ob_start();
		trx_addons_get_template_part(array(
										'shortcodes/socials/tpl.'.trx_addons_esc($atts['type']).'.php',
										'shortcodes/socials/tpl.default.php'
										),
                                        'trx_addons_args_sc_socials',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_socials', $atts, $content);
	}
}


// Add [trx_sc_socials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_socials_add_in_vc')) {
	function trx_addons_sc_socials_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_socials", "trx_addons_sc_socials");
		
		vc_lean_map("trx_sc_socials", 'trx_addons_sc_socials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Socials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_socials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_socials_add_in_vc_params')) {
	function trx_addons_sc_socials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_socials",
				"name" => esc_html__("Socials", 'trx_addons'),
				"description" => wp_kses_data( __("Insert social icons with links on your profiles", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_socials',
				"class" => "trx_sc_socials",
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
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'socials')), 'trx_sc_socials' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Icons alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the icons", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array(
								esc_html__('Default', 'trx_addons') => 'default',
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Center', 'trx_addons') => 'center',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"type" => "dropdown"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'icons',
							'heading' => esc_html__( 'Icons', 'trx_addons' ),
							"description" => wp_kses_data( __("Select social icons and specify link for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'link' => '',
									'icon_image' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
							), 'trx_sc_socials') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params',
									array_merge(
										array(
											array(
												'param_name' => 'title',
												'heading' => esc_html__( 'Title', 'trx_addons' ),
												'description' => esc_html__( 'Name of the social network', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-6',
												'admin_label' => true,
												'type' => 'textfield',
											),
											array(
												'param_name' => 'link',
												'heading' => esc_html__( 'Link', 'trx_addons' ),
												'description' => esc_html__( 'URL to your profile', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-6',
												'admin_label' => true,
												'type' => 'textfield',
											)
										),
										trx_addons_vc_add_icon_param('', true)
									),
									'trx_sc_socials')
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_socials' );
	}
}
?>