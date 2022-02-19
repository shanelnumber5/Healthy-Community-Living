<?php
/**
 * Shortcode: Display menu in the Layouts Builder
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */
	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_menu_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_menu_load_scripts_front');
	function trx_addons_sc_layouts_menu_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts_menu', trx_addons_get_file_url('cpt/layouts/shortcodes/menu/menu.css'), array(), null );
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_menu_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_menu_merge_styles');
	function trx_addons_sc_layouts_menu_merge_styles($list) {
		$list[] = 'cpt/layouts/shortcodes/menu/menu.css';
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_menu_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_menu_merge_scripts');
	function trx_addons_sc_layouts_menu_merge_scripts($list) {
		$list[] = 'cpt/layouts/shortcodes/menu/menu.js';
		$list[] = 'cpt/layouts/shortcodes/menu/jquery.slidemenu.js';
		return $list;
	}
}

	
// Add menu layout to the mobile menu
if ( !function_exists( 'trx_addons_sc_layouts_menu_add_to_mobile_menu' ) ) {
	function trx_addons_sc_layouts_menu_add_to_mobile_menu($menu) {
		global $TRX_ADDONS_STORAGE;
		$tmp = empty($TRX_ADDONS_STORAGE['menu_mobile']) ? '' : $TRX_ADDONS_STORAGE['menu_mobile'];
		if (!empty($tmp)) {
			// Add menu items to the mobile menu string
			$tmp_pos1 = strpos($menu, '<ul');
			$tmp_pos1 = strpos($menu, '>', $tmp_pos1) + 1;
			$tmp_pos2 = strrpos($menu, '</ul>');
			$menu = substr($menu, $tmp_pos1, $tmp_pos2 - $tmp_pos1);
			$tmp_pos2 = strrpos($tmp, '</ul>');
			$tmp = substr($tmp, 0, $tmp_pos2) . $menu . substr($tmp, $tmp_pos2);
		} else {
			// New mobile menu
			$tmp = $menu;
		}
		$TRX_ADDONS_STORAGE['menu_mobile'] = $tmp;
	}
}
	
// Return stored items as mobile menu
if ( !function_exists( 'trx_addons_sc_layouts_menu_get_mobile_menu' ) ) {
	add_filter("trx_addons_filter_get_mobile_menu", 'trx_addons_sc_layouts_menu_get_mobile_menu');
	function trx_addons_sc_layouts_menu_get_mobile_menu($menu) {
		global $TRX_ADDONS_STORAGE;
		return empty($TRX_ADDONS_STORAGE['menu_mobile']) 
					? '' 
					: str_replace('class="sc_layouts_menu_nav', 'class="', $TRX_ADDONS_STORAGE['menu_mobile']);
	}
}


// trx_sc_layouts_menu
//-------------------------------------------------------------
/*
[trx_sc_layouts_menu id="unique_id" menu="menu_id" location="menu_location" burger="0|1" mobile="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_menu' ) ) {
	function trx_addons_sc_layouts_menu($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_menu', $atts, array(
			// Individual params
			"type" => "default",
			"location" => "",
			"menu" => "",
			"mobile_menu" => "0",
			"mobile_button" => "0",
			"animation_in" => "",
			"animation_out" => "",
			"hover" => "fade",
			"hide_on_mobile" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (trx_addons_is_off($atts['menu'])) $atts['menu'] = '';
		if (trx_addons_is_off($atts['location'])) $atts['location'] = '';

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', trx_addons_get_file_url('cpt/layouts/shortcodes/menu/superfish.js'), array('jquery'), null, true );
		// Menu support
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-sc_layouts_menu', trx_addons_get_file_url('cpt/layouts/shortcodes/menu/menu.js'), array('jquery'), null, true );
			if ( in_array($atts['hover'], array('slide_line', 'slide_box')) )
				wp_enqueue_script( 'slidemenu', trx_addons_get_file_url('cpt/layouts/shortcodes/menu/jquery.slidemenu.js'), array('jquery'), null, true );
		}

		ob_start();
		trx_addons_get_template_part(array(
										'cpt/layouts/shortcodes/menu/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/layouts/shortcodes/menu/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_menu',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_menu', $atts, $content);
	}
}


// Add [trx_sc_layouts_menu] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_menu_add_in_vc')) {
	function trx_addons_sc_layouts_menu_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_layouts_menu", "trx_addons_sc_layouts_menu");
		
		vc_lean_map("trx_sc_layouts_menu", 'trx_addons_sc_layouts_menu_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Menu extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_menu_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_menu_add_in_vc_params')) {
	function trx_addons_sc_layouts_menu_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_menu",
				"name" => esc_html__("Layouts: Menu", 'trx_addons'),
				"description" => wp_kses_data( __("Insert any menu to the custom layout", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_menu',
				"class" => "trx_sc_layouts_menu",
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
								esc_html__('Default', 'trx_addons') => 'default',
								esc_html__('Burger', 'trx_addons') => 'burger',
							), 'trx_sc_layouts_menu' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "location",
							"heading" => esc_html__("Location", 'trx_addons'),
							"description" => wp_kses_data( __("Select menu location to insert to the layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_menu_locations()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "menu",
							"heading" => esc_html__("Menu", 'trx_addons'),
							"description" => wp_kses_data( __("Select menu to insert to the layout. If empty - use menu assigned in the field 'Location'", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'location',
								'value' => 'none'
							),
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_menus()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "hover",
							"heading" => esc_html__("Hover", 'trx_addons'),
							"description" => wp_kses_data( __("Select the menu items hover", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "fade",
							"value" => array_flip(trx_addons_get_list_menu_hover()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "animation_in",
							"heading" => esc_html__("Submenu animation in", 'trx_addons'),
							"description" => wp_kses_data( __("Select animation to show submenu", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "fadeIn",
							"value" => array_flip(trx_addons_get_list_animations_in()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "animation_out",
							"heading" => esc_html__("Submenu animation out", 'trx_addons'),
							"description" => wp_kses_data( __("Select animation to hide submenu", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "fadeOut",
							"value" => array_flip(trx_addons_get_list_animations_out()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "mobile_button",
							"heading" => esc_html__("Mobile button", 'trx_addons'),
							"description" => wp_kses_data( __("Add menu button instead menu on mobile devices. When it clicked - open menu", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array(esc_html__("Add button", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "mobile_menu",
							"heading" => esc_html__("Add to the mobile menu", 'trx_addons'),
							"description" => wp_kses_data( __("Use this menu items as mobile menu (if mobile menu not selected in the theme)", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Use as mobile menu", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "hide_on_mobile",
							"heading" => esc_html__("Hide on mobile devices", 'trx_addons'),
							"description" => wp_kses_data( __("Hide this item on mobile devices", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							"std" => "0",
							"value" => array(esc_html__("Hide on the mobile devices", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_menu');
	}
}
?>