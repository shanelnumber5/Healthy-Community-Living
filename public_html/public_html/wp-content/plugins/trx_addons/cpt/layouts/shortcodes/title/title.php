<?php
/**
 * Shortcode: Display site meta and/or title and/or breadcrumbs
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_title_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_title_load_scripts_front');
	function trx_addons_sc_layouts_title_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts_title', trx_addons_get_file_url('cpt/layouts/shortcodes/title/title.css'), array(), null );
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_title_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_title_merge_styles');
	function trx_addons_sc_layouts_title_merge_styles($list) {
		$list[] = 'cpt/layouts/shortcodes/title/title.css';
		return $list;
	}
}



// trx_sc_layouts_title
//-------------------------------------------------------------
/*
[trx_sc_layouts_title id="unique_id" icon="hours" text1="Opened hours" text2="8:00am - 5:00pm"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_title' ) ) {
	function trx_addons_sc_layouts_title($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_title', $atts, array(
			// Individual params
			"type" => "default",
			"meta" => "0",
			"title" => "0",
			"breadcrumbs" => "0",
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
										'cpt/layouts/shortcodes/title/tpl.'.trx_addons_esc($atts['type']).'.php',
                                        'cpt/layouts/shortcodes/title/tpl.default.php'
                                        ),
                                        'trx_addons_args_sc_layouts_title',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_title', $atts, $content);
	}
}


// Add [trx_sc_layouts_title] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_title_add_in_vc')) {
	function trx_addons_sc_layouts_title_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;

		add_shortcode("trx_sc_layouts_title", "trx_addons_sc_layouts_title");
		
		vc_lean_map("trx_sc_layouts_title", 'trx_addons_sc_layouts_title_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Title extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_title_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_title_add_in_vc_params')) {
	function trx_addons_sc_layouts_title_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_title",
				"name" => esc_html__("Layouts: Title and Breadcrumbs", 'trx_addons'),
				"description" => wp_kses_data( __("Insert post meta and/or title and/or breadcrumbs", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_title',
				"class" => "trx_sc_layouts_title",
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
							), 'trx_sc_layouts_title' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Show post title", 'trx_addons'),
							"description" => wp_kses_data( __("Show post/page title", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "meta",
							"heading" => esc_html__("Show post meta", 'trx_addons'),
							"description" => wp_kses_data( __("Show post meta: date, author, categories list, etc.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "breadcrumbs",
							"heading" => esc_html__("Show breadcrumbs", 'trx_addons'),
							"description" => wp_kses_data( __("Show breadcrumbs under the title", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_title');
	}
}
?>