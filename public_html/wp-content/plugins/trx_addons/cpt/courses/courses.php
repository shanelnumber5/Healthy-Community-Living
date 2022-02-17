<?php
/**
 * ThemeREX Addons Custom post type: Courses
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
if ( ! defined('TRX_ADDONS_CPT_COURSES_PT') ) define('TRX_ADDONS_CPT_COURSES_PT', trx_addons_cpt_param('courses', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_COURSES_TAXONOMY') ) define('TRX_ADDONS_CPT_COURSES_TAXONOMY', trx_addons_cpt_param('courses', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_courses_init')) {
	add_action( 'init', 'trx_addons_cpt_courses_init' );
	function trx_addons_cpt_courses_init() {

		// Add Courses parameters to the Meta Box support
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['post_types'][] = TRX_ADDONS_CPT_COURSES_PT;
		$TRX_ADDONS_STORAGE['meta_box_'.TRX_ADDONS_CPT_COURSES_PT] = array(
			"date" => array(
				"title" => esc_html__("Date",  'trx_addons'),
				"desc" => wp_kses_data( __("Start date in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => "",
				"type" => "date"
			),
			"time" => array(
				"title" => esc_html__("Time",  'trx_addons'),
				"desc" => wp_kses_data( __("The time for start of classes. For example: 7.00pm - 9.00pm, 16:00 - 18:00, etc.", 'trx_addons') ),
				"std" => "",
				"type" => "time"
			),
			"duration" => array(
				"title" => esc_html__("Duration",  'trx_addons'),
				"desc" => wp_kses_data( __("The duration of course", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"price" => array(
				"title" => esc_html__("Price",  'trx_addons'),
				"desc" => wp_kses_data( __("The price of course. For example: $99.90, $100.00/month, etc.", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"product" => array(
				"title" => __('Link to course product',  'trx_addons'),
				"desc" => __("Link to product page for this course", 'trx_addons'),
				"std" => '',
				"options" => is_admin() ? trx_addons_get_list_posts(false, 'product') : array(),
				"type" => "select2")
		);
		
		// Register post type and taxonomy
		register_post_type( TRX_ADDONS_CPT_COURSES_PT, array(
			'label'               => esc_html__( 'Courses', 'trx_addons' ),
			'description'         => esc_html__( 'Course Description', 'trx_addons' ),
			'labels'              => array(
				'name'                => esc_html__( 'Courses', 'trx_addons' ),
				'singular_name'       => esc_html__( 'Course', 'trx_addons' ),
				'menu_name'           => esc_html__( 'Courses', 'trx_addons' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
				'all_items'           => esc_html__( 'All Courses', 'trx_addons' ),
				'view_item'           => esc_html__( 'View Course', 'trx_addons' ),
				'add_new_item'        => esc_html__( 'Add New Course', 'trx_addons' ),
				'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
				'edit_item'           => esc_html__( 'Edit Course', 'trx_addons' ),
				'update_item'         => esc_html__( 'Update Course', 'trx_addons' ),
				'search_items'        => esc_html__( 'Search Courses', 'trx_addons' ),
				'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
			),
			'taxonomies'          => array(TRX_ADDONS_CPT_COURSES_TAXONOMY),
			'supports'            => trx_addons_cpt_param('courses', 'supports'),
			'public'              => true,
			'hierarchical'        => false,
			'has_archive'         => true,
			'can_export'          => true,
			'show_in_admin_bar'   => true,
			'show_in_menu'        => true,
			'menu_position'       => '52.4',
			'menu_icon'			  => 'dashicons-welcome-learn-more',
			'capability_type'     => 'post',
			'rewrite'             => array( 'slug' => trx_addons_cpt_param('courses', 'post_type_slug') )
			)
		);

		register_taxonomy( TRX_ADDONS_CPT_COURSES_TAXONOMY, TRX_ADDONS_CPT_COURSES_PT, array(
			'post_type' 		=> TRX_ADDONS_CPT_COURSES_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Courses Group', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Courses Groups', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => trx_addons_cpt_param('courses', 'taxonomy_slug') )
			)
		);
	}
}

// Add 'Courses' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_courses_options')) {
	add_action( 'trx_addons_filter_options', 'trx_addons_cpt_courses_options');
	function trx_addons_cpt_courses_options($options) {

		trx_addons_array_insert_after($options, 'cpt_section', array(
			// Courses settings
			'courses_info' => array(
				"title" => esc_html__('Courses', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the courses archive', 'trx_addons') ),
				"type" => "info"
			),
			'courses_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the courses archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'courses', 'arh'),
											TRX_ADDONS_CPT_COURSES_PT),
				"type" => "select"
			)
		));
		return $options;
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_courses_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_courses_load_scripts_front');
	function trx_addons_cpt_courses_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-cpt_courses', trx_addons_get_file_url('cpt/courses/courses.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_courses_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cpt_courses_merge_styles');
	function trx_addons_cpt_courses_merge_styles($list) {
		$list[] = 'cpt/courses/courses.css';
		return $list;
	}
}

	
// Add sort in the query for the courses
if ( !function_exists( 'trx_addons_cpt_courses_add_sort_order' ) ) {
	add_filter('trx_addons_filter_add_sort_order',	'trx_addons_cpt_courses_add_sort_order', 10, 3);
	function trx_addons_cpt_courses_add_sort_order($q, $orderby, $order='desc') {
		if ($orderby == 'courses_date') {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = 'trx_addons_courses_date';
		}
		return $q;
	}
}


// Save courses date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_courses_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_courses_save_post_options', 10, 3);
	function trx_addons_cpt_courses_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_COURSES_PT) {
			$tm = explode('-', str_replace(' ', '', strtoupper($options['time'])));
			$tm_add = strpos($tm[0], 'PM')!==false ? 12 : 0;
			$tm = explode(':', str_replace(array('.', 'AM', 'PM', ' '), array(':', '', '', ''), $tm[0]));
			update_post_meta($post_id, 'trx_addons_courses_date', $options['date'].' '.(!empty($tm[1]) ? ($tm[0]+$tm_add).':'.$tm[1] : $tm[0]));
			update_post_meta($post_id, 'trx_addons_courses_price', $options['price']);
		}
		return $options;
	}
}


// Return true if it's courses page
if ( !function_exists( 'trx_addons_is_courses_page' ) ) {
	function trx_addons_is_courses_page() {
		return defined('TRX_ADDONS_CPT_COURSES_PT') 
					&& !is_search()
					&& (
						(is_single() && get_post_type()==TRX_ADDONS_CPT_COURSES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT)
						|| is_tax(TRX_ADDONS_CPT_COURSES_TAXONOMY)
						);
	}
}


// Return taxonomy for the current post type
if ( !function_exists( 'trx_addons_cpt_courses_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_cpt_courses_post_type_taxonomy', 10, 2 );
	function trx_addons_cpt_courses_post_type_taxonomy($tax='', $post_type='') {
		if ( defined('TRX_ADDONS_CPT_COURSES_PT') && $post_type == TRX_ADDONS_CPT_COURSES_PT )
			$tax = TRX_ADDONS_CPT_COURSES_TAXONOMY;
		return $tax;
	}
}


// Return link to the all posts for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_courses_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_courses_get_blog_all_posts_link', 10, 2);
	function trx_addons_cpt_courses_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_courses_page() && !is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT))
				$link = '<a href="'.esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_COURSES_PT )).'">'.esc_html__('All Courses', 'trx_addons').'</a>';
		}
		return $link;
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_courses_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_courses_get_blog_title');
	function trx_addons_cpt_courses_get_blog_title($title='') {
		if ( defined('TRX_ADDONS_CPT_COURSES_PT') ) {
			if (is_single() && get_post_type()==TRX_ADDONS_CPT_COURSES_PT) {
				$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
				$title = array(
					'text' => get_the_title(),
					'class' => 'courses_page_title'
				);
				if (!empty($meta['product']) && (int) $meta['product'] > 0) {
					$title['link'] = get_permalink($meta['product']);
					$title['link_text'] = esc_html__('Join the Course', 'trx_addons');
				}
			} else if ( is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT) )
				$title = esc_html__('All Courses', 'trx_addons');
		}
		return $title;
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for the courses posts
if ( !function_exists( 'trx_addons_cpt_courses_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_courses_single_template');
	function trx_addons_cpt_courses_single_template($template) {
		global $post;
		if (is_single() && $post->post_type == TRX_ADDONS_CPT_COURSES_PT)
			$template = trx_addons_get_file_dir('cpt/courses/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for the courses posts
if ( !function_exists( 'trx_addons_cpt_courses_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_courses_archive_template');
	function trx_addons_cpt_courses_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT) )
			$template = trx_addons_get_file_dir('cpt/courses/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for the courses categories (groups)
if ( !function_exists( 'trx_addons_cpt_courses_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_courses_taxonomy_template');
	function trx_addons_cpt_courses_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_COURSES_TAXONOMY) )
			$template = trx_addons_get_file_dir('cpt/courses/tpl.archive.php');
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with courses categories in the admin filters area
if (!function_exists('trx_addons_cpt_courses_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_courses_admin_filters' );
	function trx_addons_cpt_courses_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_COURSES_PT, TRX_ADDONS_CPT_COURSES_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_courses_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_courses_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_COURSES_TAXONOMY);
	}
}


// trx_sc_courses
//-------------------------------------------------------------
/*
[trx_sc_courses id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_courses' ) ) {
	function trx_addons_sc_courses($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_courses', $atts, array(
			// Individual params
			"type" => "default",
			"columns" => '',
			"cat" => '',
			"count" => 3,
			"offset" => 0,
			"orderby" => '',
			"order" => '',
			"ids" => '',
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_controls" => "none",
			"slides_space" => 0,
			"past" => "0",
			"all" =>"0",
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
		if (empty($atts['orderby'])) $atts['orderby'] = 'courses_date';
		if (empty($atts['order'])) $atts['order'] = 'desc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';

		ob_start();
		trx_addons_get_template_part(array(
										'cpt/courses/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/courses/tpl.default.php'
										),
									'trx_addons_args_sc_courses',
									$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_courses', $atts, $content);
	}
}


// Add [trx_sc_courses] in the VC shortcodes list
if (!function_exists('trx_addons_sc_courses_add_in_vc')) {
	function trx_addons_sc_courses_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;

		add_shortcode("trx_sc_courses", "trx_addons_sc_courses");

		vc_lean_map( "trx_sc_courses", 'trx_addons_sc_courses_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Courses extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_courses_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_courses_add_in_vc_params')) {
	function trx_addons_sc_courses_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_courses",
				"name" => esc_html__("Courses", 'trx_addons'),
				"description" => wp_kses_data( __("Display courses from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_courses',
				"class" => "trx_sc_courses",
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
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('cpt', 'courses', 'sc')), 'trx_sc_courses' ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "past",
							"heading" => esc_html__("Past courses", 'trx_addons'),
							"description" => wp_kses_data( __("Show the past courses if checked, else - show upcoming courses", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Show past courses", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
								"param_name" => "all",
								"heading" => esc_html__("All courses", 'trx_addons'),
								"description" => wp_kses_data( __("Show the all courses if checked", 'trx_addons') ),
								"admin_label" => true,
								'edit_field_class' => 'vc_col-sm-6',
								"std" => "0",
								"value" => array(esc_html__("Show all courses", 'trx_addons') => "1" ),
								"type" => "checkbox"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Courses group", 'trx_addons') ),
							"value" => array_merge(array(esc_html__('- Select category -', 'trx_addons') => 0), array_flip(trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COURSES_TAXONOMY))),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_courses' );
	}
}
?>