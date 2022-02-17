<?php
/**
 * Shortcode: Content container
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_content_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_content_load_scripts_front');
	function trx_addons_sc_content_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_content', trx_addons_get_file_url('shortcodes/content/content.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_content_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_content_merge_styles');
	function trx_addons_sc_content_merge_styles($list) {
		$list[] = 'shortcodes/content/content.css';
		return $list;
	}
}


// trx_sc_content
//-------------------------------------------------------------
/*
[trx_sc_content id="unique_id" width="1/2"]
*/
if ( !function_exists( 'trx_addons_sc_content' ) ) {
	function trx_addons_sc_content($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_content', $atts, array(
			// Individual params
			'type' => 'default',
			"width" => "",
			"size" => "none",
			"float" => 'center',
			"align" => "",
			"paddings" => "",
			"margins" => "",
			"push" => "",
			"push_hide_on_tablet" => 0,
			"push_hide_on_mobile" => 0,
			"pull" => "",
			"pull_hide_on_tablet" => 0,
			"pull_hide_on_mobile" => 0,
			"number" => "",
			"number_position" => "br",
			"number_color" => "",
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
		
		$output = '';

		if (empty($atts['width']) && !empty($atts['size'])) $atts['width'] = $atts['size'];
		
		$atts['content'] = do_shortcode($content);
		
		if (!empty($atts['content']) || !empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['description'])) {

			ob_start();
			trx_addons_get_template_part(array(
											'shortcodes/content/tpl.'.trx_addons_esc($atts['type']).'.php',
											'shortcodes/content/tpl.default.php'
											),
                                            'trx_addons_args_sc_content', 
                                            $atts
                                        );
			$output = ob_get_contents();
			ob_end_clean();

		}
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_content', $atts, $content);
	}
}


// Add [trx_sc_content] in the VC shortcodes list
if (!function_exists('trx_addons_sc_content_add_in_vc')) {
	function trx_addons_sc_content_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_content", "trx_addons_sc_content");
		vc_lean_map("trx_sc_content", 'trx_addons_sc_content_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Content extends WPBakeryShortCodesContainer {}
		
		add_shortcode("trx_sc_content_inner", "trx_addons_sc_content");
		vc_lean_map("trx_sc_content_inner", 'trx_addons_sc_content_inner_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Content_Inner extends WPBakeryShortCodesContainer {}
		
	}
	add_action('init', 'trx_addons_sc_content_add_in_vc', 20);
}

// Return params for 'section'
if (!function_exists('trx_addons_sc_content_inner_add_in_vc_params')) {
	function trx_addons_sc_content_inner_add_in_vc_params() {
		return trx_addons_sc_content_add_in_vc_params('content_inner');
	}
}

// Return params
if (!function_exists('trx_addons_sc_content_add_in_vc_params')) {
	function trx_addons_sc_content_add_in_vc_params($type='content') {
		$args = apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_content",
				"name" => esc_html__("Content area", 'trx_addons'),
				"description" => wp_kses_data( __("Limit content width inside the fullwide rows", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_content',
				"class" => "trx_sc_content",
				'content_element' => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_sc_content,trx_sc_content_inner'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'content')), 'trx_sc_content' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "size",	// Attention! Param 'width' is reserved by VC
							"heading" => esc_html__("Size", 'trx_addons'),
							"description" => wp_kses_data( __("Select size of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
					        'save_always' => true,
							"value" => apply_filters('trx_addons_sc_content_width', array(
								esc_html__('Default', 'trx_addons') => 'none',
								esc_html__('Full width', 'trx_addons') => '1_1',
								esc_html__('1/2 of page', 'trx_addons') => '1_2',
								esc_html__('1/3 of page', 'trx_addons') => '1_3',
								esc_html__('2/3 of page', 'trx_addons') => '2_3',
								esc_html__('1/4 of page', 'trx_addons') => '1_4',
								esc_html__('3/4 of page', 'trx_addons') => '3_4',
								esc_html__('100% of container', 'trx_addons')=> '100p',
								esc_html__('90% of container', 'trx_addons') => '90p',
								esc_html__('80% of container', 'trx_addons') => '80p',
								esc_html__('75% of container', 'trx_addons') => '75p',
								esc_html__('70% of container', 'trx_addons') => '70p',
								esc_html__('60% of container', 'trx_addons') => '60p',
								esc_html__('50% of container', 'trx_addons') => '50p',
								esc_html__('45% of container', 'trx_addons') => '45p',
								esc_html__('40% of container', 'trx_addons') => '40p',
								esc_html__('30% of container', 'trx_addons') => '30p',
								esc_html__('25% of container', 'trx_addons') => '25p',
								esc_html__('20% of container', 'trx_addons') => '20p',
								esc_html__('15% of container', 'trx_addons') => '15p',
								esc_html__('10% of container', 'trx_addons') => '10p'
							)),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "paddings",
							"heading" => esc_html__("Inner paddings", 'trx_addons'),
							"description" => wp_kses_data( __("Select paddings around of the inner text in the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array(
								esc_html__('None', 'trx_addons') => 'none',
								esc_html__('Tiny', 'trx_addons') => 'tiny',
								esc_html__('Small', 'trx_addons') => 'small',
								esc_html__('Medium', 'trx_addons') => 'medium',
								esc_html__('Large', 'trx_addons') => 'large'
							),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "margins",
							"heading" => esc_html__("Outer margin", 'trx_addons'),
							"description" => wp_kses_data( __("Select margin around of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array(
								esc_html__('None', 'trx_addons') => 'none',
								esc_html__('Tiny', 'trx_addons') => 'tiny',
								esc_html__('Small', 'trx_addons') => 'small',
								esc_html__('Medium', 'trx_addons') => 'medium',
								esc_html__('Large', 'trx_addons') => 'large'
							),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "float",
							"heading" => esc_html__("Block alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment (floating position) of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
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
							"param_name" => "align",
							"heading" => esc_html__("Text alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the inner text in the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"value" => array(
								esc_html__('None', 'trx_addons') => 'none',
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Center', 'trx_addons') => 'center',
								esc_html__('Right', 'trx_addons') => 'right',
								esc_html__('Justify', 'trx_addons') => 'justify'
							),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "push",
							"heading" => esc_html__("Push block up", 'trx_addons'),
							"description" => wp_kses_data( __("Push this block up, so that it partially covers the previous block", 'trx_addons') ),
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"value" => array(
								esc_html__('None', 'trx_addons') => 'none',
								esc_html__('Tiny', 'trx_addons') => 'tiny',
								esc_html__('Small', 'trx_addons') => 'small',
								esc_html__('Medium', 'trx_addons') => 'medium',
								esc_html__('Large', 'trx_addons') => 'large'
							),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "push_hide_on_tablet",
							"heading" => esc_html__("On tablet", 'trx_addons'),
							"description" => wp_kses_data( __("Disable push on the tablets", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'push',
								'value' => array('tiny', 'small', 'medium', 'large')
							),
							"value" => array(esc_html__("Disable on tablet", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "push_hide_on_mobile",
							"heading" => esc_html__("On mobile", 'trx_addons'),
							"description" => wp_kses_data( __("Disable push on the mobile", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'push',
								'value' => array('tiny', 'small', 'medium', 'large')
							),
							"value" => array(esc_html__("Disable on mobile", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "pull",
							"heading" => esc_html__("Pull next block up", 'trx_addons'),
							"description" => wp_kses_data( __("Pull next block up, so that it partially covers this block", 'trx_addons') ),
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"value" => array(
								esc_html__('None', 'trx_addons') => 'none',
								esc_html__('Tiny', 'trx_addons') => 'tiny',
								esc_html__('Small', 'trx_addons') => 'small',
								esc_html__('Medium', 'trx_addons') => 'medium',
								esc_html__('Large', 'trx_addons') => 'large'
							),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "pull_hide_on_tablet",
							"heading" => esc_html__("On tablet", 'trx_addons'),
							"description" => wp_kses_data( __("Disable pull on the tablets", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'pull',
								'value' => array('tiny', 'small', 'medium', 'large')
							),
							"value" => array(esc_html__("Disable on tablet", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "pull_hide_on_mobile",
							"heading" => esc_html__("On mobile", 'trx_addons'),
							"description" => wp_kses_data( __("Disable pull on the mobile", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-3',
							"group" => esc_html__('Push & Pull', 'trx_addons'),
							"std" => "0",
							'dependency' => array(
								'element' => 'pull',
								'value' => array('tiny', 'small', 'medium', 'large')
							),
							"value" => array(esc_html__("Disable on mobile", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "number",
							"heading" => esc_html__("Number", 'trx_addons'),
							"description" => wp_kses_data( __("Number to display in the corner of this area", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"group" => esc_html__('Number', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "number_position",
							"heading" => esc_html__("Number position", 'trx_addons'),
							"description" => wp_kses_data( __("Select position to display number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"group" => esc_html__('Number', 'trx_addons'),
							"std" => "br",
					        'save_always' => true,
							"value" => array(
								esc_html__('Top Left', 'trx_addons') => 'tl',
								esc_html__('Top Center', 'trx_addons') => 'tc',
								esc_html__('Top Right', 'trx_addons') => 'tr',
								esc_html__('Middle Left', 'trx_addons') => 'ml',
								esc_html__('Middle Center', 'trx_addons') => 'mc',
								esc_html__('Middle Right', 'trx_addons') => 'mr',
								esc_html__('Bottom Left', 'trx_addons') => 'bl',
								esc_html__('Bottom Center', 'trx_addons') => 'bc',
								esc_html__('Bottom Right', 'trx_addons') => 'br'
							),
							"type" => "dropdown"
						),
						array(
							'param_name' => 'number_color',
							'heading' => esc_html__( 'Color of the number', 'trx_addons' ),
							'description' => esc_html__( 'Select custom color of the number', 'trx_addons' ),
							"group" => esc_html__('Number', 'trx_addons'),
							'type' => 'colorpicker'
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_content' );
		if ($type == 'content_inner') {
			$args['base'] = 'trx_sc_content_inner';
			$args['name'] = esc_html__("Content area (inner)", 'trx_addons');
			$args['description'] = wp_kses_data( __("Inner content area (used inside other content area)", 'trx_addons') );
			$args['as_child'] = array('only' => 'trx_sc_content,vc_column_inner');
		}
		return $args;
	}
}
?>