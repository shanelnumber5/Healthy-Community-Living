<?php
/**
 * Shortcode: Google Map
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_googlemap_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_googlemap_load_scripts_front');
	function trx_addons_sc_googlemap_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_googlemap', trx_addons_get_file_url('shortcodes/googlemap/googlemap.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_googlemap_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_googlemap_merge_styles');
	function trx_addons_sc_googlemap_merge_styles($list) {
		$list[] = 'shortcodes/googlemap/googlemap.css';
		return $list;
	}
}

	
// Merge googlemap specific scripts into single file
if ( !function_exists( 'trx_addons_sc_googlemap_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_googlemap_merge_scripts');
	function trx_addons_sc_googlemap_merge_scripts($list) {
		$list[] = 'shortcodes/googlemap/googlemap.js';
		$list[] = 'shortcodes/googlemap/cluster/markerclusterer.min.js';
		return $list;
	}
}

	
// Add messages for JS
if ( !function_exists( 'trx_addons_sc_googlemap_localize_script' ) ) {
	add_filter("trx_addons_localize_script", 'trx_addons_sc_googlemap_localize_script');
	function trx_addons_sc_googlemap_localize_script($storage) {
		$storage['msg_sc_googlemap_not_avail'] = esc_html__('Googlemap service is not available', 'trx_addons');
		$storage['msg_sc_googlemap_geocoder_error'] = esc_html__('Error while geocode address', 'trx_addons');
		return $storage;
	}
}


// trx_sc_googlemap
//-------------------------------------------------------------
/*
[trx_sc_googlemap id="unique_id" style="grey" zoom="16" markers="encoded json data"]
*/
if ( !function_exists( 'trx_addons_sc_googlemap' ) ) {
	function trx_addons_sc_googlemap($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_googlemap', $atts, array(
			// Individual params
			"type" => "default",
			"zoom" => 16,
			"style" => 'default',
			"address" => '',
			"markers" => '',
			"cluster" => '',
			"width" => "100%",
			"height" => "400",
			"title" => '',
			"subtitle" => '',
			"description" => '',
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
		
		if (!is_array($atts['markers']) && function_exists('vc_param_group_parse_atts'))
			$atts['markers'] = (array) vc_param_group_parse_atts( $atts['markers'] );
		if ((!is_array($atts['markers']) || count($atts['markers']) == 0) && empty($atts['address'])) return '';

		if (!empty($atts['address'])) {
			$atts['markers'] = array(
									array(
										'title' => '',
										'description' => '',
										'address' => $atts['address'],
										'latlng' => '',
										'icon' => ''
									)
								);
		} else {
			foreach ($atts['markers'] as $k=>$v)
				if (!empty($v['description'])) $atts['markers'][$k]['description'] = trim( vc_value_from_safe( $v['description'] ) );
		}
		
		$atts['zoom'] = max(0, min(21, $atts['zoom']));

		if (count($atts['markers']) > 1) {
			if (empty($atts['cluster'])) $atts['cluster'] = trx_addons_get_option('api_google_cluster');
			if (empty($atts['cluster'])) $atts['cluster'] = trx_addons_get_file_url('shortcodes/googlemap/cluster/cluster-icon.png');;
		} else if ($atts['zoom'] == 0)
			$atts['zoom'] = 16;

		$atts['css'] .= trx_addons_get_css_dimensions_from_values($atts['width'], $atts['height']);
		if (empty($atts['style'])) $atts['style'] = 'default';


		$atts['content'] = do_shortcode($content);

        if (trx_addons_get_option('api_google') != '') {
            trx_addons_enqueue_googlemap();
            if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
                wp_enqueue_script('trx_addons-sc_googlemap', trx_addons_get_file_url('shortcodes/googlemap/googlemap.js'), array('jquery'), null, true);
                if (count($atts['markers']) > 1)
                    wp_enqueue_script('markerclusterer', trx_addons_get_file_url('shortcodes/googlemap/cluster/markerclusterer.min.js'), array('jquery'), null, true);
            }
        }

		ob_start();
		trx_addons_get_template_part(array(
										'shortcodes/googlemap/tpl.'.trx_addons_esc($atts['type']).'.php',
										'shortcodes/googlemap/tpl.default.php'
										),
                                        'trx_addons_args_sc_googlemap', 
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_googlemap', $atts, $content);
	}
}


// Add [trx_sc_googlemap] in the VC shortcodes list
if (!function_exists('trx_addons_sc_googlemap_add_in_vc')) {
	function trx_addons_sc_googlemap_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_googlemap", "trx_addons_sc_googlemap");

		vc_lean_map("trx_sc_googlemap", 'trx_addons_sc_googlemap_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Googlemap extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_googlemap_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_googlemap_add_in_vc_params')) {
	function trx_addons_sc_googlemap_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_googlemap",
				"name" => esc_html__("Google Map", 'trx_addons'),
				"description" => wp_kses_data( __("Google map with custom styles and several markers", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_googlemap',
				"class" => "trx_sc_googlemap",
				'content_element' => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_sc_googlemap'),
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
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'googlemap')), 'trx_sc_googlemap' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Map custom style", 'trx_addons') ),
							"admin_label" => true,
					        'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => apply_filters('trx_addons_filter_sc_googlemap_styles', array(
								esc_html__('Default', 'trx_addons') => 'default',
								esc_html__('Greyscale', 'trx_addons') => 'greyscale',
								esc_html__('Inverse', 'trx_addons') => 'inverse',
								esc_html__('Simple', 'trx_addons') => 'simple'
							)),
							"std" => "default",
							"type" => "dropdown"
						),
						array(
							"param_name" => "zoom",
							"heading" => esc_html__("Zoom", 'trx_addons'),
							"description" => wp_kses_data( __("Map zoom factor from 1 to 20. If 0 or empty - fit bounds to markers", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => "16",
							"type" => "textfield"
						),
						array(
							"param_name" => "width",
							"heading" => esc_html__("Width", 'trx_addons'),
							"description" => wp_kses_data( __("Width of the element", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => '100%',
							"type" => "textfield"
						),
						array(
							"param_name" => "height",
							"heading" => esc_html__("Height", 'trx_addons'),
							"description" => wp_kses_data( __("Height of the element", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => 350,
							"type" => "textfield"
						),
						array(
							"param_name" => "address",
							"heading" => esc_html__("Address", 'trx_addons'),
							"description" => wp_kses_data( __("Specify address in this field if you don't need unique marker, title or latlng coordinates. Otherwise, leave this field empty and fill markers below", 'trx_addons') ),
							"value" => '',
							"type" => "textfield"
						),
						array(
							"param_name" => "cluster",
							"heading" => esc_html__("Cluster icon", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image for markers clusterer", 'trx_addons') ),
							"value" => "",
							"type" => "attach_image"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'markers',
							'heading' => esc_html__( 'Markers', 'trx_addons' ),
							"description" => wp_kses_data( __("Add markers into this map", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'title' => esc_html__( 'One', 'trx_addons' ),
									'description' => '',
									'address' => '',
									'latlng' => '',
									'icon' => ''
								),
							), 'trx_sc_googlemap') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array(
								array(
									"param_name" => "address",
									"heading" => esc_html__("Address", 'trx_addons'),
									"description" => wp_kses_data( __("Address of this marker", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4',
									"admin_label" => true,
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "latlng",
									"heading" => esc_html__("Latitude and Longitude", 'trx_addons'),
									"description" => wp_kses_data( __("Comma separated coorditanes of the marker (instead Address)", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4',
									"admin_label" => true,
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "icon",
									"heading" => esc_html__("Marker image", 'trx_addons'),
									"description" => wp_kses_data( __("Select or upload image or write URL from other site for this marker", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-4',
									"value" => "",
									"type" => "attach_image"
								),
								array(
									"param_name" => "title",
									"heading" => esc_html__("Title", 'trx_addons'),
									"description" => wp_kses_data( __("Title for this marker", 'trx_addons') ),
									"admin_label" => true,
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "description",
									"heading" => esc_html__("Description", 'trx_addons'),
									"description" => wp_kses_data( __("Description of this marker", 'trx_addons') ),
									"value" => "",
									"type" => "textarea_safe"
								)
							), 'trx_sc_googlemap')
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
				
			), 'trx_sc_googlemap' );
	}
}
?>