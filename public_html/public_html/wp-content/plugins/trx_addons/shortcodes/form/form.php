<?php
/**
 * Shortcode: Form
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_form_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_form_load_scripts_front');
	function trx_addons_sc_form_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_form', trx_addons_get_file_url('shortcodes/form/form.css'), array(), null );
			// Load this script always, because it used for the comments and other forms also
			wp_enqueue_script('trx_addons-sc_form', trx_addons_get_file_url('shortcodes/form/form.js'), array('jquery'), null, true );
		}
	}
}
	
// Merge contact form specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_form_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_form_merge_styles');
	function trx_addons_sc_form_merge_styles($list) {
		$list[] = 'shortcodes/form/form.css';
		return $list;
	}
}

	
// Merge contact form specific scripts into single file
if ( !function_exists( 'trx_addons_sc_form_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_form_merge_scripts');
	function trx_addons_sc_form_merge_scripts($list) {
		$list[] = 'shortcodes/form/form.js';
		return $list;
	}
}


// AJAX handler for the send_form action
if ( !function_exists( 'trx_addons_sc_form_ajax_send_sc_form' ) ) {
	add_action('wp_ajax_send_sc_form',			'trx_addons_sc_form_ajax_send_sc_form');
	add_action('wp_ajax_nopriv_send_sc_form',	'trx_addons_sc_form_ajax_send_sc_form');
	function trx_addons_sc_form_ajax_send_sc_form() {

		if ( !wp_verify_nonce( trx_addons_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = array('error'=>'');
		
		if (!($contact_email = get_option('admin_email'))) 
			$response['error'] = esc_html__('Unknown admin email!', 'trx_addons');
		else {
			parse_str($_POST['data'], $post_data);
			$user_name	= !empty($post_data['name']) ? stripslashes($post_data['name']) : '';
			$user_email	= !empty($post_data['email']) ? stripslashes($post_data['email']) : '';
			$user_phone	= !empty($post_data['phone']) ? stripslashes($post_data['phone']) : '';
			$user_msg	= !empty($post_data['message']) ? stripslashes($post_data['message']) : '';
			
			// Attention! Strings below not need html-escaping, because mail is a plain text
			$subj = sprintf(__('Site %s - Contact form message from %s', 'trx_addons'), get_bloginfo('site_name'), $user_name);
			$msg = (!empty($user_name)	? "\n".sprintf(__('Name: %s', 'trx_addons'), $user_name) : '')
				.  (!empty($user_email) ? "\n".sprintf(__('E-mail: %s', 'trx_addons'), $user_email) : '')
				.  (!empty($user_phone) ? "\n".sprintf(__('Phone: %s', 'trx_addons'), $user_phone) : '')
				.  (!empty($user_msg)	? "\n\n".trim($user_msg) : '')
				.  "\n\n............. " . get_bloginfo('site_name') . " (" . esc_url(home_url('/')) . ") ............";

			if (!wp_mail($contact_email, $subj, $msg)) {
				$response['error'] = esc_html__('Error send message!', 'trx_addons');
			}
		
			echo json_encode($response);
			die();
		}
	}
}



// trx_sc_form
//-------------------------------------------------------------
/*
[trx_sc_form id="unique_id" style="default"]
*/
if ( !function_exists( 'trx_addons_sc_form' ) ) {
	function trx_addons_sc_form($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_form', $atts, array(
			// Individual params
			"type" => "default",
			"style" => "inherit",
			"align" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			"labels" => 0,
			"phone" => "",
			"email" => "",
			"address" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		ob_start();
		trx_addons_get_template_part(array(
										'shortcodes/form/tpl.'.trx_addons_esc($atts['type']).'.php',
										'shortcodes/form/tpl.default.php'
										),
										'trx_addons_args_sc_form', 
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_form', $atts, $content);
	}
}


// Add [trx_sc_form] in the VC shortcodes list
if (!function_exists('trx_addons_sc_form_add_in_vc')) {
	function trx_addons_sc_form_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_form", "trx_addons_sc_form");
		
		vc_lean_map("trx_sc_form", 'trx_addons_sc_form_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Form extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_form_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_form_add_in_vc_params')) {
	function trx_addons_sc_form_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_form",
				"name" => esc_html__("Form", 'trx_addons'),
				"description" => wp_kses_data( __("Insert simple or detailed form", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_form',
				"class" => "trx_sc_form",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select form's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
					        'save_always' => true,
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'form')), 'trx_sc_form' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Select input's style", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "inherit",
							"value" => array_flip(trx_addons_get_list_input_hover(true)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Fields alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the field's text", 'trx_addons') ),
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
							"param_name" => "labels",
							"heading" => esc_html__("Field labels", 'trx_addons'),
							"description" => wp_kses_data( __("Show field's labels", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Show labels", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							'param_name' => 'phone',
							'heading' => esc_html__( 'Your phone', 'trx_addons' ),
							'description' => esc_html__( 'Specify your phone for the detailed form', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => array('modern', 'detailed')
							),
							'type' => 'textfield',
						),
						array(
							'param_name' => 'email',
							'heading' => esc_html__( 'Your E-mail', 'trx_addons' ),
							'description' => esc_html__( 'Specify your E-mail for the detailed form', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => array('modern', 'detailed')
							),
							'type' => 'textfield',
						),
						array(
							'param_name' => 'address',
							'heading' => esc_html__( 'Your address', 'trx_addons' ),
							'description' => esc_html__( 'Specify your address for the detailed form', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => array('modern', 'detailed')
							),
							'type' => 'textfield',
						)
					),
					trx_addons_vc_add_title_param(false, false),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_form' );
	}
}
?>