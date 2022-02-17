<?php
/**
 * ThemeREX Addons Custom post type: Testimonials
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_TESTIMONIALS_PT') ) define('TRX_ADDONS_CPT_TESTIMONIALS_PT', trx_addons_cpt_param('testimonials', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY') ) define('TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY', trx_addons_cpt_param('testimonials', 'taxonomy'));


// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_testimonials_init')) {
	add_action( 'init', 'trx_addons_cpt_testimonials_init' );
	function trx_addons_cpt_testimonials_init() {

		// Add Testimonials to the Meta Box support
		global $TRX_ADDONS_STORAGE;	// Need to declare global, because this file included inside autoload function!
		$TRX_ADDONS_STORAGE['post_types'][] = TRX_ADDONS_CPT_TESTIMONIALS_PT;
		$TRX_ADDONS_STORAGE['meta_box_'.TRX_ADDONS_CPT_TESTIMONIALS_PT] = array(
			"subtitle" => array(
				"title" => esc_html__("Item's subtitle",  'trx_addons'),
				"desc" => wp_kses_data( __("Testimonial author's position or any other text", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			)
		);

		// Register post type and taxonomy
		register_post_type( TRX_ADDONS_CPT_TESTIMONIALS_PT, array(
			'label'               => esc_html__( 'Testimonial', 'trx_addons' ),
			'description'         => esc_html__( 'Testimonial Description', 'trx_addons' ),
			'labels'              => array(
				'name'                => esc_html__( 'Testimonials', 'trx_addons' ),
				'singular_name'       => esc_html__( 'Testimonial', 'trx_addons' ),
				'menu_name'           => esc_html__( 'Testimonials', 'trx_addons' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
				'all_items'           => esc_html__( 'All Testimonials', 'trx_addons' ),
				'view_item'           => esc_html__( 'View Testimonial', 'trx_addons' ),
				'add_new_item'        => esc_html__( 'Add New Testimonial', 'trx_addons' ),
				'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
				'edit_item'           => esc_html__( 'Edit Testimonial', 'trx_addons' ),
				'update_item'         => esc_html__( 'Update Testimonial', 'trx_addons' ),
				'search_items'        => esc_html__( 'Search Testimonial', 'trx_addons' ),
				'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
			),
			'taxonomies'          => array(TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY),
			'supports'            => trx_addons_cpt_param('testimonials', 'supports'),
			'public'              => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'can_export'          => true,
			'show_in_admin_bar'   => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => '54.0',
			'menu_icon'			  => 'dashicons-format-status',
			'capability_type'     => 'post',
			'rewrite'             => array( 'slug' => trx_addons_cpt_param('testimonials', 'post_type_slug') )
			)
		);

		register_taxonomy( TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY, TRX_ADDONS_CPT_TESTIMONIALS_PT, array(
			'post_type' 		=> TRX_ADDONS_CPT_TESTIMONIALS_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Testimonials Group', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Testimonial Group', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => trx_addons_cpt_param('testimonials', 'taxonomy_slug') )
			)
		);
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_testimonials_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_testimonials_load_scripts_front');
	function trx_addons_cpt_testimonials_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-cpt_testimonials', trx_addons_get_file_url('cpt/testimonials/testimonials.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_testimonials_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cpt_testimonials_merge_styles');
	function trx_addons_cpt_testimonials_merge_styles($list) {
		$list[] = 'cpt/testimonials/testimonials.css';
		return $list;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with testimonials categories in the admin filters area
if (!function_exists('trx_addons_cpt_testimonials_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_testimonials_admin_filters' );
	function trx_addons_cpt_testimonials_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_TESTIMONIALS_PT, TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_testimonials_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY, 'trx_addons_cpt_testimonials_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY, 'trx_addons_cpt_testimonials_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY, 'trx_addons_cpt_testimonials_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_testimonials_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY);
	}
}



// trx_sc_testimonials
//-------------------------------------------------------------
/*
[trx_sc_testimonials id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_testimonials' ) ) {
	function trx_addons_sc_testimonials($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_testimonials', $atts, array(
			// Individual params
			"type" => "default",
			"columns" => "",
			"cat" => "",
			"count" => 3,
			"offset" => 0,
			"orderby" => '',
			"order" => '',
			"ids" => '',
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_pagination_thumbs" => 0,
			"slider_controls" => "none",
			"slides_space" => 0,
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

		if (!empty($atts['ids'])) {
			$atts['ids'] = str_replace(array(';', ' '), array(',', ''), $atts['ids']);
			$atts['count'] = count(explode(',', $atts['ids']));
		}
		$atts['count'] = max(1, (int) $atts['count']);
		$atts['offset'] = max(0, (int) $atts['offset']);
		if (empty($atts['orderby'])) $atts['orderby'] = 'date';
		if (empty($atts['order'])) $atts['order'] = 'desc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';

		ob_start();
		trx_addons_get_template_part(array(
										'cpt/testimonials/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/testimonials/tpl.default.php'
										),
										'trx_addons_args_sc_testimonials',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_testimonials', $atts, $content);
	}
}


// Add [trx_sc_testimonials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_testimonials_add_in_vc')) {
	function trx_addons_sc_testimonials_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;

		add_shortcode("trx_sc_testimonials", "trx_addons_sc_testimonials");
		
		vc_lean_map("trx_sc_testimonials", 'trx_addons_sc_testimonials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Testimonials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_testimonials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_testimonials_add_in_vc_params')) {
	function trx_addons_sc_testimonials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_testimonials",
				"name" => esc_html__("Testimonials", 'trx_addons'),
				"description" => wp_kses_data( __("Display testimonials from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_testimonials',
				"class" => "trx_sc_testimonials",
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
					        'save_always' => true,
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('cpt', 'testimonials', 'sc')), 'trx_sc_testimonials' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Testimonials group", 'trx_addons') ),
							"value" => array_merge(array(esc_html__('- Select category -', 'trx_addons') => 0), array_flip(trx_addons_get_list_terms(false, TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY))),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					array(
						array(
							"param_name" => "slider_pagination_thumbs",
							"heading" => esc_html__("Slider pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Show thumbs as pagination bullets", 'trx_addons') ),
							'dependency' => array(
								'element' => 'slider_pagination',
								'value' => array('left', 'right', 'bottom')
							),
							"group" => esc_html__('Slider', 'trx_addons'),
							"std" => "0",
							"value" => array(esc_html__("Pagination thumbs", 'trx_addons') => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_testimonials' );
	}
}
?>