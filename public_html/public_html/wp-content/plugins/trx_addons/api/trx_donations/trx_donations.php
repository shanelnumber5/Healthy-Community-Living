<?php
/**
 * Plugin support: ThemeREX Donations
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_trx_donations' ) ) {
	function trx_addons_exists_trx_donations() {
		return class_exists('TRX_DONATIONS');
	}
}

// Return true, if current page is any trx_donations page
if ( !function_exists( 'trx_addons_is_trx_donations_page' ) ) {
	function trx_addons_is_trx_donations_page() {
		$rez = false;
		if (trx_addons_exists_trx_donations()) {
			$rez = (is_single() && get_query_var('post_type') == TRX_DONATIONS::POST_TYPE) 
					|| is_post_type_archive(TRX_DONATIONS::POST_TYPE) 
					|| is_tax(TRX_DONATIONS::TAXONOMY);
		}
		return $rez;
	}
}

// Return taxonomy for current post type
if ( !function_exists( 'trx_addons_trx_donations_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_trx_donations_post_type_taxonomy', 10, 2 );
	function trx_addons_trx_donations_post_type_taxonomy($tax='', $post_type='') {
		if (trx_addons_exists_trx_donations() && $post_type == TRX_DONATIONS::POST_TYPE)
			$tax = TRX_DONATIONS::TAXONOMY;
		return $tax;
	}
}

// Return link to the all donations page for the breadcrumbs
if ( !function_exists( 'trx_addons_trx_donations_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_trx_donations_get_blog_all_posts_link', 10, 2);
	function trx_addons_trx_donations_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_trx_donations_page() && !is_post_type_archive(TRX_DONATIONS::POST_TYPE))
				$link = '<a href="'.esc_url(get_post_type_archive_link( TRX_DONATIONS::POST_TYPE )).'">'.esc_html__('All Donations', 'trx_addons').'</a>';
		}
		return $link;
	}
}

// Return current page title
if ( !function_exists( 'trx_addons_trx_donations_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_trx_donations_get_blog_title');
	function trx_addons_trx_donations_get_blog_title($title='') {
		if ( trx_addons_exists_trx_donations() && is_post_type_archive(TRX_DONATIONS::POST_TYPE) )
			$title = esc_html__('All Donations', 'trx_addons');
		return $title;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_trx_donations_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_trx_donations_importer_required_plugins', 10, 2 );
	function trx_addons_trx_donations_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'trx_donations')!==false && !trx_addons_exists_trx_donations() )
			$not_installed .= '<br>' . esc_html__('trx_donations', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_trx_donations_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_trx_donations_importer_set_options' );
	function trx_addons_trx_donations_importer_set_options($options=array()) {
		if ( trx_addons_exists_trx_donations() && in_array('trx_donations', $options['required_plugins']) ) {
			$options['additional_options'][] = 'trx_donations_options';
		}
		return $options;
	}
}

// Check if the row will be imported
if ( !function_exists( 'trx_addons_trx_donations_importer_check_row' ) ) {
	if (is_admin()) add_filter('trx_addons_filter_importer_import_row', 'trx_addons_trx_donations_importer_check_row', 9, 4);
	function trx_addons_trx_donations_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'trx_donations')===false) return $flag;
		if ( trx_addons_exists_trx_donations() ) {
			if ($table == 'posts')
				$flag = $row['post_type']==TRX_DONATIONS::POST_TYPE;
		}
		return $flag;
	}
}


// VC support
//------------------------------------------------------------------------

// Add [trx_donations_form] and [trx_donations_list] in the VC shortcodes list
if (!function_exists('trx_addons_sc_trx_donations_add_in_vc')) {
	function trx_addons_sc_trx_donations_add_in_vc() {
	
		if (!trx_addons_exists_visual_composer() || !trx_addons_exists_trx_donations()) return;

		vc_lean_map( "trx_donations_form", 'trx_addons_sc_trx_donations_add_in_vc_params_df');
		class WPBakeryShortCode_Trx_Donations_Form extends WPBakeryShortCode {}

		vc_lean_map( "trx_donations_list", 'trx_addons_sc_trx_donations_add_in_vc_params_dl');
		class WPBakeryShortCode_Trx_Donations_List extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_trx_donations_add_in_vc', 20);
}

// Return params for Donations form
if (!function_exists('trx_addons_sc_trx_donations_add_in_vc_params_df')) {
	function trx_addons_sc_trx_donations_add_in_vc_params_df() {
		$donations = TRX_DONATIONS::get_instance();
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_donations_form",
				"name" => esc_html__("Donations form", "trx_addons"),
				"description" => esc_html__("Insert form to allow visitors make donations", "trx_addons"),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				'icon' => 'icon_trx_sc_donations_form',
				"class" => "trx_sc_single trx_sc_donations_form",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the form", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "subtitle",
							"heading" => esc_html__("Subtitle", 'trx_addons'),
							"description" => wp_kses_data( __("Subtitle of the form", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Title alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the title, subtitle and description", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
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
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Description of the form", 'trx_addons') ),
							"type" => "textarea_safe"
						),
						array(
							"param_name" => "account",
							"heading" => esc_html__("PayPal account", 'trx_addons'),
							"description" => wp_kses_data( __("E-mail, used for registration PayPal account. If empty - used value from ThemeREX Donations options", 'trx_addons') ),
							"group" => esc_html__('PayPal', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "currency",
							"heading" => esc_html__("Currency", 'trx_addons'),
							"description" => wp_kses_data( __("Default currency for donation. If empty - used value from ThemeREX Donations options", 'trx_addons') ),
							"group" => esc_html__('PayPal', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "USD",
					        'save_always' => true,
							"value" => array_flip($donations->currency_codes),
							"type" => "dropdown"
						),
						array(
							"param_name" => "amount",
							"heading" => esc_html__("Default amount", 'trx_addons'),
							"description" => wp_kses_data( __("Specify default amount to make donation. If empty - used value from ThemeREX Donations options", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__('PayPal', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "sandbox",
							"heading" => esc_html__("Sandbox", 'trx_addons'),
							"description" => wp_kses_data( __("Enable sandbox mode to testing your payments without real money transfer", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__('PayPal', 'trx_addons'),
							"std" => "",
							"value" => array(
								esc_html__('Inherit', 'trx_addons') => '',
								esc_html__('On', 'trx_addons') => 'on',
								esc_html__('Off', 'trx_addons') => 'off'
							),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_donations_form' );
	}
}

// Return params for Donations list
if (!function_exists('trx_addons_sc_trx_donations_add_in_vc_params_dl')) {
	function trx_addons_sc_trx_donations_add_in_vc_params_dl() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_donations_list",
				"name" => esc_html__("Donations list", "trx_addons"),
				"description" => esc_html__("Insert list of donations", "trx_addons"),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				'icon' => 'icon_trx_sc_donations_list',
				"class" => "trx_sc_single trx_sc_donations_list",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Donations category", 'trx_addons') ),
							"value" => array_merge(array(esc_html__('- Select category -', 'trx_addons') => 0), array_flip(trx_addons_get_list_terms(false, TRX_DONATIONS::TAXONOMY))),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "subtitle",
							"heading" => esc_html__("Subtitle", 'trx_addons'),
							"description" => wp_kses_data( __("Subtitle of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Description of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							"type" => "textarea_safe"
						),
						array(
							"param_name" => "link",
							"heading" => esc_html__("Link URL", 'trx_addons'),
							"description" => wp_kses_data( __("Specify URL for the button below list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "link_caption",
							"heading" => esc_html__("Link text", 'trx_addons'),
							"description" => wp_kses_data( __("Specify text for the button below list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_donations_list' );
	}
}
?>