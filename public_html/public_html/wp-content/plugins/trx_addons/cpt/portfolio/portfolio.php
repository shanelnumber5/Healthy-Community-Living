<?php
/**
 * ThemeREX Addons Custom post type: Portfolio
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_PORTFOLIO_PT') ) define('TRX_ADDONS_CPT_PORTFOLIO_PT', trx_addons_cpt_param('portfolio', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY') ) define('TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY', trx_addons_cpt_param('portfolio', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_portfolio_init')) {
	add_action( 'init', 'trx_addons_cpt_portfolio_init' );
	function trx_addons_cpt_portfolio_init() {
		
		// Add Services parameters to the Meta Box support
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['post_types'][] = TRX_ADDONS_CPT_PORTFOLIO_PT;
		$TRX_ADDONS_STORAGE['meta_box_'.TRX_ADDONS_CPT_PORTFOLIO_PT] = array(
			"subtitle" => array(
				"title" => esc_html__("Item's subtitle",  'trx_addons'),
				"desc" => wp_kses_data( __("Portfolio item author's name or position or any other text", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"alter_link" => array(
				"title" => esc_html__("Item's link",  'trx_addons'),
				"desc" => wp_kses_data( __("Alternative link to the portfolio item's site. If empty - use this post's permalink", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			)
		);
		
		// Register post type and taxonomy
		register_post_type( TRX_ADDONS_CPT_PORTFOLIO_PT, array(
			'label'               => esc_html__( 'Portfolio', 'trx_addons' ),
			'description'         => esc_html__( 'Portfolio Description', 'trx_addons' ),
			'labels'              => array(
				'name'                => esc_html__( 'Portfolio', 'trx_addons' ),
				'singular_name'       => esc_html__( 'Portfolio', 'trx_addons' ),
				'menu_name'           => esc_html__( 'Portfolio', 'trx_addons' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
				'all_items'           => esc_html__( 'All Portfolio items', 'trx_addons' ),
				'view_item'           => esc_html__( 'View Portfolio item', 'trx_addons' ),
				'add_new_item'        => esc_html__( 'Add New Portfolio item', 'trx_addons' ),
				'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
				'edit_item'           => esc_html__( 'Edit Portfolio item', 'trx_addons' ),
				'update_item'         => esc_html__( 'Update Portfolio item', 'trx_addons' ),
				'search_items'        => esc_html__( 'Search Portfolio items', 'trx_addons' ),
				'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
			),
			'taxonomies'          => array(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY),
			'supports'            => trx_addons_cpt_param('portfolio', 'supports'),
			'public'              => true,
			'hierarchical'        => false,
			'has_archive'         => true,
			'can_export'          => true,
			'show_in_admin_bar'   => true,
			'show_in_menu'        => true,
			'menu_position'       => '53.2',
			'menu_icon'			  => 'dashicons-images-alt',
			'capability_type'     => 'post',
			'rewrite'             => array( 'slug' => trx_addons_cpt_param('portfolio', 'post_type_slug') )
			)
		);

		register_taxonomy( TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY, TRX_ADDONS_CPT_PORTFOLIO_PT, array(
			'post_type' 		=> TRX_ADDONS_CPT_PORTFOLIO_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Portfolio Group', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Portfolio Groups', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => trx_addons_cpt_param('portfolio', 'taxonomy_slug') )
			)
		);
	}
}

// Add 'Portfolio' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_portfolio_options')) {
	add_action( 'trx_addons_filter_options', 'trx_addons_cpt_portfolio_options');
	function trx_addons_cpt_portfolio_options($options) {

		trx_addons_array_insert_after($options, 'cpt_section', array(
			// Portfolio settings
			'portfolio_info' => array(
				"title" => esc_html__('Portfolio', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the portfolio archive', 'trx_addons') ),
				"type" => "info"
			),
			'portfolio_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the portfolio archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'portfolio', 'arh'),
											TRX_ADDONS_CPT_PORTFOLIO_PT),
				"type" => "select"
			)
		));
		return $options;
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_portfolio_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_portfolio_load_scripts_front');
	function trx_addons_cpt_portfolio_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-cpt_portfolio', trx_addons_get_file_url('cpt/portfolio/portfolio.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_portfolio_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cpt_portfolio_merge_styles');
	function trx_addons_cpt_portfolio_merge_styles($list) {
		$list[] = 'cpt/portfolio/portfolio.css';
		return $list;
	}
}


// Return true if it's portfolio page
if ( !function_exists( 'trx_addons_is_portfolio_page' ) ) {
	function trx_addons_is_portfolio_page() {
		return defined('TRX_ADDONS_CPT_PORTFOLIO_PT') 
					&& !is_search()
					&& (
						(is_single() && get_post_type()==TRX_ADDONS_CPT_PORTFOLIO_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_PORTFOLIO_PT)
						|| is_tax(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY)
						);
	}
}


// Return taxonomy for the current post type
if ( !function_exists( 'trx_addons_cpt_portfolio_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_cpt_portfolio_post_type_taxonomy', 10, 2 );
	function trx_addons_cpt_portfolio_post_type_taxonomy($tax='', $post_type='') {
		if ( defined('TRX_ADDONS_CPT_PORTFOLIO_PT') && $post_type == TRX_ADDONS_CPT_PORTFOLIO_PT )
			$tax = TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY;
		return $tax;
	}
}


// Return link to the all posts for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_portfolio_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_portfolio_get_blog_all_posts_link', 10, 2);
	function trx_addons_cpt_portfolio_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_portfolio_page() && !is_post_type_archive(TRX_ADDONS_CPT_PORTFOLIO_PT))
				$link = '<a href="'.esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_PORTFOLIO_PT )).'">'.esc_html__('All Portfolio Items', 'trx_addons').'</a>';
		}
		return $link;
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_portfolio_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_portfolio_get_blog_title');
	function trx_addons_cpt_portfolio_get_blog_title($title='') {
		if ( defined('TRX_ADDONS_CPT_PORTFOLIO_PT') ) {
			if ( is_post_type_archive(TRX_ADDONS_CPT_PORTFOLIO_PT) )
				$title = esc_html__('All Portfolio Items', 'trx_addons');

		}
		return $title;
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for services posts
if ( !function_exists( 'trx_addons_cpt_portfolio_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_portfolio_single_template');
	function trx_addons_cpt_portfolio_single_template($template) {
		global $post;
		if (is_single() && $post->post_type == TRX_ADDONS_CPT_PORTFOLIO_PT)
			$template = trx_addons_get_file_dir('cpt/portfolio/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for services posts
if ( !function_exists( 'trx_addons_cpt_portfolio_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_portfolio_archive_template');
	function trx_addons_cpt_portfolio_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_PORTFOLIO_PT) )
			$template = trx_addons_get_file_dir('cpt/portfolio/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for services categories (groups)
if ( !function_exists( 'trx_addons_cpt_portfolio_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_portfolio_taxonomy_template');
	function trx_addons_cpt_portfolio_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY) )
			$template = trx_addons_get_file_dir('cpt/portfolio/tpl.archive.php');
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with portfolio categories in the admin filters area
if (!function_exists('trx_addons_cpt_portfolio_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_portfolio_admin_filters' );
	function trx_addons_cpt_portfolio_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_PORTFOLIO_PT, TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_portfolio_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY, 'trx_addons_cpt_portfolio_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY, 'trx_addons_cpt_portfolio_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY, 'trx_addons_cpt_portfolio_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_portfolio_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY);
	}
}


// trx_sc_portfolio
//-------------------------------------------------------------
/*
[trx_sc_portfolio id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_portfolio' ) ) {
	function trx_addons_sc_portfolio($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_portfolio', $atts, array(
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
		if (empty($atts['orderby'])) $atts['orderby'] = 'title';
		if (empty($atts['order'])) $atts['order'] = 'asc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';

		ob_start();
		trx_addons_get_template_part(array(
										'cpt/portfolio/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/portfolio/tpl.default.php'
										), 
										'trx_addons_args_sc_portfolio',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_portfolio', $atts, $content);
	}
}


// Add [trx_sc_portfolio] in the VC shortcodes list
if (!function_exists('trx_addons_sc_portfolio_add_in_vc')) {
	function trx_addons_sc_portfolio_add_in_vc() {

		if (!trx_addons_exists_visual_composer()) return;
	
		add_shortcode("trx_sc_portfolio", "trx_addons_sc_portfolio");
		
		vc_lean_map("trx_sc_portfolio", 'trx_addons_sc_portfolio_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Portfolio extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_portfolio_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_portfolio_add_in_vc_params')) {
	function trx_addons_sc_portfolio_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_portfolio",
				"name" => esc_html__("Portfolio", 'trx_addons'),
				"description" => wp_kses_data( __("Display portfolio items from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_portfolio',
				"class" => "trx_sc_portfolio",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('cpt', 'portfolio', 'sc')), 'trx_sc_portfolio' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Portfolio group", 'trx_addons') ),
							"value" => array_merge(array(esc_html__('- Select category -', 'trx_addons') => 0), 
													array_flip(trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY))),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_portfolio' );
	}
}
?>