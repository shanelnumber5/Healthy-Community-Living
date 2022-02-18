<?php
/**
 * Shortcode: Promo block
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_promo_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_promo_load_scripts_front');
	function trx_addons_sc_promo_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_promo', trx_addons_get_file_url('shortcodes/promo/promo.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_promo_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_promo_merge_styles');
	function trx_addons_sc_promo_merge_styles($list) {
		$list[] = 'shortcodes/promo/promo.css';
		return $list;
	}
}



// trx_sc_promo
//-------------------------------------------------------------
/*
[trx_sc_promo id="unique_id" title="Block title" 
subtitle="" link="#" link_text="Buy now"]Description[/trx_sc_promo]
*/
if (!function_exists('trx_addons_sc_promo')) {	
	function trx_addons_sc_promo($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_promo', $atts, array(
			// Individual params
			"type" => "default",
			"size" => "normal",
			"image" => "",
			"image_position" => "left",
			"image_width" => "50%",
			"image_cover" => 1,
			"image_bg_color" => '',
			"video_url" => '',
			"video_embed" => '',
			"video_in_popup" => 0,
			"text_margins" => '',
			"text_align" => "none",
			"text_paddings" => 0,
			"text_float" => "none",
			"text_width" => "none",
			"text_bg_color" => '',
			"full_height" => 0,
			"gap" => 0,
			"icon" => "",
			"icon_type" => '',
			"icon_fontawesome" => "",
			"icon_openiconic" => "",
			"icon_typicons" => "",
			"icon_entypo" => "",
			"icon_linecons" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"link2" => '',
			"link2_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
	
		$atts['content'] = $content;

		if (empty($atts['icon'])) {
			$atts['icon'] = isset( $atts['icon_' . $atts['icon_type']] ) && $atts['icon_' . $atts['icon_type']] != 'empty' 
								? $atts['icon_' . $atts['icon_type']] 
								: '';
			trx_addons_load_icons($atts['icon_type']);
		}

		if (strpos($atts['image'], ',')!==false)
			$atts['image'] = explode(',', $atts['image']);
		else
			$atts['image'] = trx_addons_get_attachment_url($atts['image'], 'full');
		
		
		$atts['gap'] = !empty($atts['gap']) ? trx_addons_prepare_css_value($atts['gap']) : '';
		if (empty($atts['image'])) {
			$atts['text_width'] = '100%';
			$atts['image_width'] = '0%';
			$atts['gap'] = '';
		} else if (empty($atts['title']) && empty($atts['subtitle']) && empty($atts['description']) && empty($atts['content']) 
				&& (empty($atts['link']) || empty($atts['link_text']))) {
			$atts['image_width'] = '100%';
			$atts['text_width'] = '0%';
			$atts['gap'] = '';
		} else {
			$atts['image_width'] = !empty($atts['image_width']) ? trx_addons_prepare_css_value($atts['image_width']) : '50%';
			$image_ed = strpos($atts['image_width'], '%')!==false ? '%' : substr($atts['image_width'], -2);
			if ($atts['gap']) {
				$gap_ed = strpos($atts['gap'], '%')!==false ? '%' : substr($atts['gap'], -2);
				if ($image_ed == $gap_ed) {
					$atts['text_width'] = $image_ed == '%'
									? (100 - ((float)str_replace('%', '', $atts['gap']))/2 - (float)str_replace('%', '', $atts['image_width'])).'%'
									: 'calc(100% - '.esc_attr($atts['gap']).'/2 - '.esc_attr($atts['image_width']).')';
					$atts['image_width'] = ((float)str_replace($image_ed, '', $atts['image_width']) - ((float)str_replace($gap_ed, '', $atts['gap'])) / 2) . $image_ed;
				} else {
					$atts['text_width'] = 'calc(100% - '.esc_attr($atts['gap']).'/2 - '.esc_attr($atts['image_width']).')';
					$atts['image_width'] = 'calc('.esc_attr($atts['image_width']).' - '.esc_attr($atts['gap']).'/2)';
				}
			} else {
				$atts['text_width'] = $image_ed=='%' 
								? (100 - (float)str_replace('%', '', $atts['image_width'])).'%'
								: 'calc(100% - '.esc_attr($atts['image_width']).')';
			}
		}

		ob_start();
		trx_addons_get_template_part(array(
										'shortcodes/promo/tpl.'.trx_addons_esc($atts['type']).'.php',
										'shortcodes/promo/tpl.default.php'
										),
                                        'trx_addons_args_sc_promo',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_promo', $atts, $content);
	}
}



// Add [trx_sc_promo] in the VC shortcodes list
if (!function_exists('trx_addons_sc_promo_add_in_vc')) {
	function trx_addons_sc_promo_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_promo", "trx_addons_sc_promo");
		
		vc_lean_map("trx_sc_promo", 'trx_addons_sc_promo_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Promo extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_promo_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_promo_add_in_vc_params')) {
	function trx_addons_sc_promo_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
			"base" => "trx_sc_promo",
			"name" => esc_html__("Promo", 'trx_addons'),
			"description" => wp_kses_data( __("Insert promo block", 'trx_addons') ),
			"category" => esc_html__('ThemeREX', 'trx_addons'),
			'icon' => 'icon_trx_sc_promo',
			"class" => "trx_sc_promo",
			'content_element' => true,
			'is_container' => true,
			'as_child' => array('except' => 'trx_sc_promo'),
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
						"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'promo')), 'trx_sc_promo' ),
						"type" => "dropdown"
					)
				),
				trx_addons_vc_add_icon_param(),
				trx_addons_vc_add_title_param(''),
				array(
					array(
						"param_name" => "link2",
						"heading" => esc_html__("Button 2 URL", 'trx_addons'),
						"description" => wp_kses_data( __("Link URL for the button (at the side of the image)", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => 'modern'
						),
						"type" => "textfield"
					),
					array(
						"param_name" => "link2_text",
						"heading" => esc_html__("Button 2 text", 'trx_addons'),
						"description" => wp_kses_data( __("Caption for the button 2 (at the side of the image)", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => 'modern'
						),
						"type" => "textfield"
					),
					array(
						'param_name' => 'text_bg_color',
						'heading' => esc_html__( 'Text bg color', 'trx_addons' ),
						'description' => esc_html__( 'Select custom color, used as background of the text area', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'type' => 'colorpicker',
					),
					array(
						"param_name" => "image",
						"heading" => esc_html__("Image", 'trx_addons'),
						"description" => wp_kses_data( __("Select the promo image from the library for this section. Show slider if you select 2+ images", 'trx_addons') ),
						"group" => esc_html__('Image', 'trx_addons'),
						"type" => "attach_images"
					),
					array(
						'param_name' => 'image_bg_color',
						'heading' => esc_html__( 'Image bg color', 'trx_addons' ),
						'description' => esc_html__( 'Select custom color, used as background of the image', 'trx_addons' ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'colorpicker',
					),
					array(
						"param_name" => "image_cover",
						"heading" => esc_html__("Image cover area", 'trx_addons'),
						"description" => wp_kses_data( __("Fit image into area or cover it", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"std" => "1",
						"value" => array(esc_html__("Image cover area", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "image_position",
						"heading" => esc_html__("Image position", 'trx_addons'),
						"description" => wp_kses_data( __("Place the image to the left or to the right from the text block", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"value" => array(
							esc_html__('Left', 'trx_addons') => 'left',
							esc_html__('Right', 'trx_addons') => 'right'
						),
				        'save_always' => true,
						"std" => "left",
						"type" => "dropdown"
					),
					array(
						"param_name" => "image_width",
						"heading" => esc_html__("Image width", 'trx_addons'),
						"description" => wp_kses_data( __("Width (in pixels or percents) of the block with image", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"value" => "50%",
						"type" => "textfield"
					),
					array(
						'param_name' => 'video_url',
						'heading' => esc_html__( 'Video URL', 'trx_addons' ),
						'description' => esc_html__( 'Enter link to the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'textfield'
					),
					array(
						'param_name' => 'video_embed',
						'heading' => esc_html__( 'Video embed code', 'trx_addons' ),
						'description' => esc_html__( 'or paste the HTML code to embed video in this block', 'trx_addons' ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						'type' => 'textarea'
					),
					array(
						"param_name" => "video_in_popup",
						"heading" => esc_html__("Video in the popup", 'trx_addons'),
						"description" => wp_kses_data( __("Open video in the popup window or insert it instead image", 'trx_addons') ),
						'dependency' => array(
							'element' => 'image',
							'not_empty' => true
						),
						"group" => esc_html__('Image', 'trx_addons'),
						"std" => "0",
						"value" => array(esc_html__("Video in the popup", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "size",
						"heading" => esc_html__("Size", 'trx_addons'),
						"description" => wp_kses_data( __("Size of the promo block: normal - one in the row, tiny - only image and title, small - insize two or greater columns, large - fullscreen height", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
				        'save_always' => true,
						"value" => array(
							esc_html__('Tiny', 'trx_addons')  => 'tiny',
							esc_html__('Small', 'trx_addons') => 'small',
							esc_html__('Normal', 'trx_addons') => 'normal',
							esc_html__('Large', 'trx_addons') => 'large'
						),
						"std" => "normal",
						"type" => "dropdown"
					),
					array(
						"param_name" => "full_height",
						"heading" => esc_html__("Full height", 'trx_addons'),
						"description" => wp_kses_data( __("Stretch the height of the element to the full screen's height", 'trx_addons') ),
						"admin_label" => true,
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-6',
						"std" => "0",
						"value" => array(esc_html__("Full Height", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "text_width",
						"heading" => esc_html__("Text width", 'trx_addons'),
						"description" => wp_kses_data( __("Select width of the text block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => apply_filters('trx_addons_sc_content_width', array(
							esc_html__('Default', 'trx_addons') => 'none',
							esc_html__('1/1', 'trx_addons') => '1_1',
							esc_html__('1/2', 'trx_addons') => '1_2',
							esc_html__('1/3', 'trx_addons') => '1_3',
							esc_html__('2/3', 'trx_addons') => '2_3',
							esc_html__('1/4', 'trx_addons') => '1_4',
							esc_html__('3/4', 'trx_addons') => '3_4'
						)),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "text_float",
						"heading" => esc_html__("Text block floating", 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment (floating position) of the text block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array(
							esc_html__('Default', 'trx_addons') => 'none',
							esc_html__('Left', 'trx_addons') => 'left',
							esc_html__('Center', 'trx_addons') => 'center',
							esc_html__('Right', 'trx_addons') => 'right'
						),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "text_align",
						"heading" => esc_html__("Text alignment", 'trx_addons'),
						"description" => wp_kses_data( __("Align text to the left or to the right side inside the block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array(
							esc_html__('Default', 'trx_addons') => 'none',
							esc_html__('Left', 'trx_addons') => 'left',
							esc_html__('Center', 'trx_addons') => 'center',
							esc_html__('Right', 'trx_addons') => 'right'
						),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "text_paddings",
						"heading" => esc_html__("Text paddings", 'trx_addons'),
						"description" => wp_kses_data( __("Add horizontal paddings from the text block", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "0",
						"value" => array(esc_html__("With paddings", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "text_margins",
						"heading" => esc_html__("Text margins", 'trx_addons'),
						"description" => wp_kses_data( __("Margins for the all sides of the text block (Example: 30px 10px 40px 30px = top right botton left OR 30px = equal for all sides)", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "gap",
						"heading" => esc_html__("Gap", 'trx_addons'),
						"description" => wp_kses_data( __("Gap between text and image (in percent)", 'trx_addons') ),
						"group" => esc_html__('Dimensions', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"type" => "textfield"
					)
				),
				trx_addons_vc_add_id_param()
			)

		), 'trx_sc_promo' );
	}
}
?>