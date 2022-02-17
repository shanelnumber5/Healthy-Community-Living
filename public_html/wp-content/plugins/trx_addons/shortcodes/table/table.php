<?php
/**
 * Shortcode: Table
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.3
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_table_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_table_load_scripts_front');
	function trx_addons_sc_table_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_table', trx_addons_get_file_url('shortcodes/table/table.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_table_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_table_merge_styles');
	function trx_addons_sc_table_merge_styles($list) {
		$list[] = 'shortcodes/table/table.css';
		return $list;
	}
}


// trx_sc_table
//-------------------------------------------------------------
/*
[trx_sc_table id="unique_id" style="default" aligh="left"]
*/
if ( !function_exists( 'trx_addons_sc_table' ) ) {
	function trx_addons_sc_table($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_table', $atts, array(
			// Individual params
			"type" => "default",
			"width" => "100%",
			"align" => "none",
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
		
		$atts['css'] .= trx_addons_get_css_dimensions_from_values($atts['width']);

		$atts['content'] = do_shortcode(str_replace(
											array('<p><table', 'table></p>', '><br />'),
											array('<table', 'table>', '>'),
											html_entity_decode($content, ENT_COMPAT, 'UTF-8')
											)
							);
		
		ob_start();
		trx_addons_get_template_part(array(
										'shortcodes/table/tpl.'.trx_addons_esc($atts['type']).'.php',
										'shortcodes/table/tpl.default.php'
										),
										'trx_addons_args_sc_table', 
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_table', $atts, $content);
	}
}


// Add [trx_sc_table] in the VC shortcodes list
if (!function_exists('trx_addons_sc_table_add_in_vc')) {
	function trx_addons_sc_table_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_table", "trx_addons_sc_table");
		
		vc_lean_map("trx_sc_table", 'trx_addons_sc_table_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Table extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_table_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_table_add_in_vc_params')) {
	function trx_addons_sc_table_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_table",
				"name" => esc_html__("Table", 'trx_addons'),
				"description" => wp_kses_data( __("Insert a table", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_table',
				"class" => "trx_sc_table",
				'content_element' => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_sc_table'),
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'table')), 'trx_sc_table' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Table alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the table", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array(
								esc_html__('None', 'trx_addons') => 'none',
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Center', 'trx_addons') => 'center',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "width",
							"heading" => esc_html__("Width", 'trx_addons'),
							"description" => wp_kses_data( __("Width of the table", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => '100%',
							"type" => "textfield"
						),
						array(
							'heading' => __( 'Content', 'trx_addons' ),
							"description" => wp_kses_data( __("Content, created with any table-generator, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", 'trx_addons') ),
							'param_name' => 'content',
							'value' => '',
							'holder' => 'div',
							'type' => 'textarea_html',
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
				
			), 'trx_sc_table' );
	}
}
?>