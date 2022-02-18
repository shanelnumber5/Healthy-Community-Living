<?php
/**
 * Shortcode: Icons
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_icons_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_icons_load_scripts_front');
	function trx_addons_sc_icons_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_icons', trx_addons_get_file_url('shortcodes/icons/icons.css'), array(), null );
		}
	}
}
	
// Merge contact form specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_icons_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_icons_merge_styles');
	function trx_addons_sc_icons_merge_styles($list) {
		$list[] = 'shortcodes/icons/icons.css';
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_icons_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_icons_merge_scripts');
	function trx_addons_sc_icons_merge_scripts($list) {
		$list[] = 'shortcodes/icons/vivus.js';
		$list[] = 'shortcodes/icons/icons.js';
		return $list;
	}
}



// trx_sc_icons
//-------------------------------------------------------------
/*
[trx_sc_icons id="unique_id" columns="2" values="encoded_json_data"]
*/
if ( !function_exists( 'trx_addons_sc_icons' ) ) {
	function trx_addons_sc_icons($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_icons', $atts, array(
			// Individual params
			"type" => "default",
			"align" => "center",
			"size" => "medium",
			"color" => "",
			"columns" => "",
			"icons" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"icons_animation" => "0",
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
		if (!is_array($atts['icons']) || count($atts['icons']) == 0) return '';

		if (empty($atts['columns'])) $atts['columns'] = count($atts['icons']);
		$atts['columns'] = max(1, min(count($atts['icons']), $atts['columns']));

		foreach ($atts['icons'] as $k=>$v)
			if (!empty($v['description'])) $atts['icons'][$k]['description'] = trim( vc_value_from_safe( $v['description'] ) );

		ob_start();
		trx_addons_get_template_part(array(
										'shortcodes/icons/tpl.'.trx_addons_esc($atts['type']).'.php',
										'shortcodes/icons/tpl.default.php'
										),
										'trx_addons_args_sc_icons', 
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_icons', $atts, $content);
	}
}


// Add [trx_sc_icons] in the VC shortcodes list
if (!function_exists('trx_addons_sc_icons_add_in_vc')) {
	function trx_addons_sc_icons_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_icons", "trx_addons_sc_icons");
		
		vc_lean_map("trx_sc_icons", 'trx_addons_sc_icons_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Icons extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_icons_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_icons_add_in_vc_params')) {
	function trx_addons_sc_icons_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_icons",
				"name" => esc_html__("Icons", 'trx_addons'),
				"description" => wp_kses_data( __("Insert icons or images with title and description", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_icons',
				"class" => "trx_sc_icons",
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
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'icons')), 'trx_sc_icons' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Align", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of this item", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "center",
					        'save_always' => true,
							"value" => array(
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Center', 'trx_addons') => 'center',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "size",
							"heading" => esc_html__("Icon size", 'trx_addons'),
							"description" => wp_kses_data( __("Select icon size", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array(
								esc_html__('Medium', 'trx_addons') => 'medium',
								esc_html__('Small', 'trx_addons') => 'small',
								esc_html__('Large', 'trx_addons') => 'large'
							),
					        'save_always' => true,
							"std" => "medium",
							"type" => "dropdown"
						),
						array(
							'param_name' => 'color',
							'heading' => esc_html__( 'Color', 'trx_addons' ),
							'description' => esc_html__( 'Select custom color for each icon', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'colorpicker',
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify number of columns for icons. If empty - auto detect by items number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "icons_animation",
							"heading" => esc_html__("Animation", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want animate icons. Attention! Animation enabled only if in your theme exists .SVG icon with same name as selected icon", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Animate icons", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'icons',
							'heading' => esc_html__( 'Icons', 'trx_addons' ),
							"description" => wp_kses_data( __("Select icons, specify title and/or description for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'title' => esc_html__( 'One', 'trx_addons' ),
									'description' => '',
									'color' => '',
									'image' => '',
									'link' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
							), 'trx_sc_icons') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
									array(
										'param_name' => 'title',
										'heading' => esc_html__( 'Title', 'trx_addons' ),
										'description' => esc_html__( 'Enter title for this item', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'link',
										'heading' => esc_html__( 'Link', 'trx_addons' ),
										'description' => esc_html__( 'URL to link this block', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'description',
										'heading' => esc_html__( 'Description', 'trx_addons' ),
										'description' => esc_html__( 'Enter short description for this item', 'trx_addons' ),
										'type' => 'textarea_safe',
									),
									array(
										'param_name' => 'color',
										'heading' => esc_html__( 'Color', 'trx_addons' ),
										'description' => esc_html__( 'Select custom color for this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'colorpicker',
									),
									array(
										"param_name" => "image",
										"heading" => esc_html__("Image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image or specify URL from other site", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-6',
										"type" => "attach_image"
									),
								), trx_addons_vc_add_icon_param('')
							), 'trx_sc_icons')
						)
					),
					trx_addons_vc_add_title_param(false, false),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_icons' );
	}
}
?>