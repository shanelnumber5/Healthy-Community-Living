<?php
/**
 * Shortcode: Price block
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_price_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_price_load_scripts_front');
	function trx_addons_sc_price_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_price', trx_addons_get_file_url('shortcodes/price/price.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_price_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_price_merge_styles');
	function trx_addons_sc_price_merge_styles($list) {
		$list[] = 'shortcodes/price/price.css';
		return $list;
	}
}



// trx_sc_price
//-------------------------------------------------------------
/*
[trx_sc_price id="unique_id" period="Monthly" price="89.25" currency="$" link="#" link_text="Buy now"]Description[/trx_sc_price]
*/
if ( !function_exists( 'trx_addons_sc_price' ) ) {
	function trx_addons_sc_price($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_price', $atts, array(
			// Individual params
			"type" => 'default',
			"icon" => "",
			"icon_type" => '',
			"icon_fontawesome" => "",
			"icon_openiconic" => "",
			"icon_typicons" => "",
			"icon_entypo" => "",
			"icon_linecons" => "",
			"image" => "",
			"subtitle" => "",
			"title" => "",
			"description" => "",
			"label" => "",
			"price" => "",
			"link" => '#',
			"link_text" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		
		$atts['content'] = do_shortcode($content);
		
		if (empty($atts['icon'])) {
			$atts['icon'] = isset( $atts['icon_' . $atts['icon_type']] ) && $atts['icon_' . $atts['icon_type']] != 'empty' 
								? $atts['icon_' . $atts['icon_type']] 
								: '';
			trx_addons_load_icons($atts['icon_type']);
		}

		ob_start();
		trx_addons_get_template_part(array(
										'shortcodes/price/tpl.'.trx_addons_esc($atts['type']).'.php',
										'shortcodes/price/tpl.default.php'
										),
                                        'trx_addons_args_sc_price',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_price', $atts, $content);
	}
}


// Add [trx_sc_price] in the VC shortcodes list
if (!function_exists('trx_addons_sc_price_add_in_vc')) {
	function trx_addons_sc_price_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_price", "trx_addons_sc_price");
		
		vc_lean_map("trx_sc_price", 'trx_addons_sc_price_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Price extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_price_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_price_add_in_vc_params')) {
	function trx_addons_sc_price_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_price",
				"name" => esc_html__("Price block", 'trx_addons'),
				"description" => wp_kses_data( __("Add block with price, period and short description", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_price',
				"class" => "trx_sc_price",
				"content_element" => true,
				"is_container" => true,
				'as_child' => array('except' => 'trx_sc_price'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'price')), 'trx_sc_price' ),
							"type" => "dropdown"
						),
						array(
							'param_name' => 'title',
							'heading' => esc_html__( 'Title', 'trx_addons' ),
							'description' => esc_html__( 'Title of the price', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label' => true,
							'type' => 'textfield',
						),
						array(
							'param_name' => 'subtitle',
							'heading' => esc_html__( 'Subtitle', 'trx_addons' ),
							'description' => esc_html__( 'Subtitle of the price', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'description',
							'heading' => esc_html__( 'Description', 'trx_addons' ),
							'description' => esc_html__( 'Price description', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'price',
							'heading' => esc_html__( 'Price', 'trx_addons' ),
							'description' => esc_html__( 'Price value', 'trx_addons' ),
							'admin_label' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'label',
							'heading' => esc_html__( 'Label', 'trx_addons' ),
							'description' => esc_html__( 'If not empty - colored band with this text is showed at the top corner of price block', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-6',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'link',
							'heading' => esc_html__( 'Link', 'trx_addons' ),
							'description' => esc_html__( 'Specify URL for the button under decription', 'trx_addons' ),
							'admin_label' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'link_text',
							'heading' => esc_html__( 'Link text', 'trx_addons' ),
							'description' => esc_html__( 'Specify text for the button under decription', 'trx_addons' ),
							'dependency' => array(
								'element' => 'link',
								'not_empty' => true,
							),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							'type' => 'textfield',
						),
						array(
							"param_name" => "image",
							"heading" => esc_html__("Image", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or specify URL from other site", 'trx_addons') ),
							"type" => "attach_image"
						)
					),
					trx_addons_vc_add_icon_param(''),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_price' );
	}
}
?>