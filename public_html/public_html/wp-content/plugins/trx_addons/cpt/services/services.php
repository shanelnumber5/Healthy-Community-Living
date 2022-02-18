<?php
/**
 * ThemeREX Addons Custom post type: Services
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_SERVICES_PT') ) define('TRX_ADDONS_CPT_SERVICES_PT', trx_addons_cpt_param('services', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_SERVICES_TAXONOMY') ) define('TRX_ADDONS_CPT_SERVICES_TAXONOMY', trx_addons_cpt_param('services', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_services_init')) {
	add_action( 'init', 'trx_addons_cpt_services_init' );
	function trx_addons_cpt_services_init() {
		
		// Add Services parameters to the Meta Box support
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['post_types'][] = TRX_ADDONS_CPT_SERVICES_PT;
		$TRX_ADDONS_STORAGE['meta_box_'.TRX_ADDONS_CPT_SERVICES_PT] = array(
			"icon" => array(
				"title" => esc_html__("Item's icon", 'trx_addons'),
				"desc" => wp_kses_data( __('Select icon for the current service item', 'trx_addons') ),
				"std" => '',
				"options" => array(),
				"style" => trx_addons_get_setting('icons_type'),
				"type" => "icons"
			)
		);
		
		// Register post type and taxonomy
		register_post_type( TRX_ADDONS_CPT_SERVICES_PT, array(
			'label'               => esc_html__( 'Services', 'trx_addons' ),
			'description'         => esc_html__( 'Service Description', 'trx_addons' ),
			'labels'              => array(
				'name'                => esc_html__( 'Services', 'trx_addons' ),
				'singular_name'       => esc_html__( 'Service', 'trx_addons' ),
				'menu_name'           => esc_html__( 'Services', 'trx_addons' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
				'all_items'           => esc_html__( 'All Services', 'trx_addons' ),
				'view_item'           => esc_html__( 'View Service', 'trx_addons' ),
				'add_new_item'        => esc_html__( 'Add New Service', 'trx_addons' ),
				'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
				'edit_item'           => esc_html__( 'Edit Service', 'trx_addons' ),
				'update_item'         => esc_html__( 'Update Service', 'trx_addons' ),
				'search_items'        => esc_html__( 'Search Service', 'trx_addons' ),
				'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
			),
			'taxonomies'          => array(TRX_ADDONS_CPT_SERVICES_TAXONOMY),
			'supports'            => trx_addons_cpt_param('services', 'supports'),
			'public'              => true,
			'hierarchical'        => false,
			'has_archive'         => true,
			'can_export'          => true,
			'show_in_admin_bar'   => true,
			'show_in_menu'        => true,
			'menu_position'       => '53.6',
			'menu_icon'			  => 'dashicons-hammer',
			'capability_type'     => 'post',
			'rewrite'             => array( 'slug' => trx_addons_cpt_param('services', 'post_type_slug') )
			)
		);

		register_taxonomy( TRX_ADDONS_CPT_SERVICES_TAXONOMY, TRX_ADDONS_CPT_SERVICES_PT, array(
			'post_type' 		=> TRX_ADDONS_CPT_SERVICES_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Services Group', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Services Groups', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => trx_addons_cpt_param('services', 'taxonomy_slug') )
			)
		);
	}
}

// Add 'Services' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_services_options')) {
	add_action( 'trx_addons_filter_options', 'trx_addons_cpt_services_options');
	function trx_addons_cpt_services_options($options) {

		trx_addons_array_insert_after($options, 'cpt_section', array(
			// services settings
			'services_info' => array(
				"title" => esc_html__('Services', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the services archive', 'trx_addons') ),
				"type" => "info"
			),
			'services_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the services archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles', 
											trx_addons_components_get_allowed_layouts('cpt', 'services', 'arh'),
											TRX_ADDONS_CPT_SERVICES_PT),
				"type" => "select"
			)
		));
		return $options;
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_services_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_services_load_scripts_front');
	function trx_addons_cpt_services_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-cpt_services', trx_addons_get_file_url('cpt/services/services.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_services_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cpt_services_merge_styles');
	function trx_addons_cpt_services_merge_styles($list) {
		$list[] = 'cpt/services/services.css';
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_cpt_services_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_cpt_services_merge_scripts');
	function trx_addons_cpt_services_merge_scripts($list) {
		$list[] = 'cpt/services/services.js';
		return $list;
	}
}


// Return true if it's services page
if ( !function_exists( 'trx_addons_is_services_page' ) ) {
	function trx_addons_is_services_page() {
		return defined('TRX_ADDONS_CPT_SERVICES_PT') 
					&& !is_search()
					&& (
						(is_single() && get_post_type()==TRX_ADDONS_CPT_SERVICES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_SERVICES_PT)
						|| is_tax(TRX_ADDONS_CPT_SERVICES_TAXONOMY)
						);
	}
}


// Return taxonomy for the current post type
if ( !function_exists( 'trx_addons_cpt_services_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_cpt_services_post_type_taxonomy', 10, 2 );
	function trx_addons_cpt_services_post_type_taxonomy($tax='', $post_type='') {
		if ( defined('TRX_ADDONS_CPT_SERVICES_PT') && $post_type == TRX_ADDONS_CPT_SERVICES_PT )
			$tax = TRX_ADDONS_CPT_SERVICES_TAXONOMY;
		return $tax;
	}
}


// Return link to the all posts for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_services_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_services_get_blog_all_posts_link', 10, 2);
	function trx_addons_cpt_services_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_services_page() && !is_post_type_archive(TRX_ADDONS_CPT_SERVICES_PT))
				$link = '<a href="'.esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_SERVICES_PT )).'">'
							. esc_html__('All Services', 'trx_addons')
						. '</a>';
		}
		return $link;
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_services_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_services_get_blog_title');
	function trx_addons_cpt_services_get_blog_title($title='') {
		if ( defined('TRX_ADDONS_CPT_SERVICES_PT') ) {
			if ( is_post_type_archive(TRX_ADDONS_CPT_SERVICES_PT) )
				$title = esc_html__('All Services', 'trx_addons');

		}
		return $title;
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for services posts
if ( !function_exists( 'trx_addons_cpt_services_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_services_single_template');
	function trx_addons_cpt_services_single_template($template) {
		global $post;
		if (is_single() && $post->post_type == TRX_ADDONS_CPT_SERVICES_PT)
			$template = trx_addons_get_file_dir('cpt/services/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for services posts
if ( !function_exists( 'trx_addons_cpt_services_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_services_archive_template');
	function trx_addons_cpt_services_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_SERVICES_PT) )
			$template = trx_addons_get_file_dir('cpt/services/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for services categories (groups)
if ( !function_exists( 'trx_addons_cpt_services_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_services_taxonomy_template');
	function trx_addons_cpt_services_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_SERVICES_TAXONOMY) )
			$template = trx_addons_get_file_dir('cpt/services/tpl.archive.php');
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with services categories in the admin filters area
if (!function_exists('trx_addons_cpt_services_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_services_admin_filters' );
	function trx_addons_cpt_services_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_SERVICES_PT, TRX_ADDONS_CPT_SERVICES_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_services_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_SERVICES_TAXONOMY, 'trx_addons_cpt_services_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_SERVICES_TAXONOMY, 'trx_addons_cpt_services_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_SERVICES_TAXONOMY, 'trx_addons_cpt_services_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_services_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_SERVICES_TAXONOMY);
	}
}


// trx_sc_services
//-------------------------------------------------------------
/*
[trx_sc_services id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_services' ) ) {
	function trx_addons_sc_services($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_services', $atts, array(
			// Individual params
			"type" => "default",
			"featured" => "image",
			"featured_position" => "top",
			"tabs_effect" => "fade",
			"hide_excerpt" => 0,
			"hide_bg_image" => 0,
			"icons_animation" => 0,
			"columns" => '',
			"no_margin" => 0,
			'post_type' => TRX_ADDONS_CPT_SERVICES_PT,
			'taxonomy' => TRX_ADDONS_CPT_SERVICES_TAXONOMY,
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

		if (in_array($atts['type'], array('tabs', 'tabs_simple')) && trx_addons_is_on(trx_addons_get_option('debug_mode')))
			wp_enqueue_script( 'trx_addons-cpt_services', trx_addons_get_file_url('cpt/services/services.js'), array('jquery'), null, true );

		if ($atts['type'] == 'chess')
			$atts['columns'] = max(1, min(3, (int) $atts['columns']));
		else if ($atts['type'] == 'timeline') {
			$atts['no_margin'] = 1;
			if (in_array($atts['featured_position'], array('left', 'right')))
				$atts['columns'] = 1;
		}
		if ($atts['featured_position'] == 'bottom' && !in_array($atts['type'], array('callouts', 'timeline')))
			$atts['featured_position'] = 'top';
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
										'cpt/services/tpl.'.trx_addons_esc($atts['type']).'.php',
										'cpt/services/tpl.default.php'
										),
                                        'trx_addons_args_sc_services',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_services', $atts, $content);
	}
}


// Add [trx_sc_services] in the VC shortcodes list
if (!function_exists('trx_addons_sc_services_add_in_vc')) {
	function trx_addons_sc_services_add_in_vc() {
		
		if (!trx_addons_exists_visual_composer()) return;
		
		add_shortcode("trx_sc_services", "trx_addons_sc_services");
		
		vc_lean_map("trx_sc_services", 'trx_addons_sc_services_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Services extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_services_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_services_add_in_vc_params')) {
	function trx_addons_sc_services_add_in_vc_params() {
		// If open params in VC Editor
		$vc_edit = is_admin() && trx_addons_get_value_gp('action')=='vc_edit_form' && trx_addons_get_value_gp('tag') == 'trx_sc_services';
		$vc_params = $vc_edit && isset($_POST['params']) ? $_POST['params'] : array();
		// Prepare lists
		$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : TRX_ADDONS_CPT_SERVICES_PT;
		$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : TRX_ADDONS_CPT_SERVICES_TAXONOMY;
		$taxonomies_objects = get_object_taxonomies($post_type, 'objects');
		$taxonomies = array();
		if (is_array($taxonomies_objects)) {
			foreach ($taxonomies_objects as $slug=>$taxonomy_obj) {
				$taxonomies[$slug] = $taxonomy_obj->label;
			}
		}
		$tax_obj = get_taxonomy($taxonomy);
		$params = array_merge(
				array(
					array(
						"param_name" => "type",
						"heading" => esc_html__("Layout", 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "default",
				        'save_always' => true,
						"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('cpt', 'services', 'sc')), 'trx_sc_services' ),
						"type" => "dropdown"
					),
					array(
						"param_name" => "featured",
						"heading" => esc_html__("Featured", 'trx_addons'),
						"description" => wp_kses_data( __("What to use as featured element?", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "image",
				        'save_always' => true,
						"value" => array(
							esc_html__('Image', 'trx_addons') => 'image',
							esc_html__('Icon', 'trx_addons') => 'icon',
							esc_html__('Number', 'trx_addons') => 'number'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "featured_position",
						"heading" => esc_html__("Featured position", 'trx_addons'),
						"description" => wp_kses_data( __("Select the position of the featured element. Attention! Use 'Bottom' only with 'Callouts' or 'Timeline'", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => array('default', 'callouts', 'light', 'list', 'tabs_simple', 'timeline')
						),
						"std" => "top",
				        'save_always' => true,
						"value" => array(
							esc_html__('Top', 'trx_addons') => 'top',
							esc_html__('Bottom', 'trx_addons') => 'bottom',
							esc_html__('Left', 'trx_addons') => 'left',
							esc_html__('Right', 'trx_addons') => 'right'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "tabs_effect",
						"heading" => esc_html__("Tabs change effect", 'trx_addons'),
						"description" => wp_kses_data( __("Select the tabs change effect", 'trx_addons') ),
						'dependency' => array(
							'element' => 'type',
							'value' => 'tabs'
						),
						"std" => "fade",
				        'save_always' => true,
						"value" => array(
							esc_html__('Fade', 'trx_addons') => 'fade',
							esc_html__('Slide', 'trx_addons') => 'slide',
							esc_html__('Page flip', 'trx_addons') => 'flip'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "hide_excerpt",
						"heading" => esc_html__("Excerpt", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want hide excerpt", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => "0",
						"value" => array(esc_html__("Hide excerpt", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "no_margin",
						"heading" => esc_html__("Remove margin", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want remove spaces between columns", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "0",
						"value" => array(esc_html__("Remove margin between columns", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "icons_animation",
						"heading" => esc_html__("Animation", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want animate icons. Attention! Animation enabled only if in your theme exists .SVG icon with same name as selected icon", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => array('default', 'callouts', 'light', 'list', 'iconed', 'tabs', 'tabs_simple','timeline')
						),
						"std" => "0",
						"value" => array(esc_html__("Animate icons", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_bg_image",
						"heading" => esc_html__("Hide bg image", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want hide background image on the front item", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'type',
							'value' => 'hover'
						),
						"std" => "0",
						"value" => array(esc_html__("Hide bg image", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "post_type",
						"heading" => esc_html__("Post type", 'trx_addons'),
						"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => TRX_ADDONS_CPT_SERVICES_PT,
						"value" => array_flip(trx_addons_get_list_posts_types()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "taxonomy",
						"heading" => esc_html__("Taxonomy", 'trx_addons'),
						"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => TRX_ADDONS_CPT_SERVICES_TAXONOMY,
						"value" => array_flip($taxonomies),
						"type" => "dropdown"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Group", 'trx_addons'),
						"description" => wp_kses_data( __("Services group", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_flip(trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
																		 $taxonomy == 'category' 
																			? trx_addons_get_list_categories() 
																			: trx_addons_get_list_terms(false, $taxonomy)
																		)),
						"std" => "0",
						"type" => "dropdown"
					)
				),
				trx_addons_vc_add_query_param(''),
				trx_addons_vc_add_slider_param(),
				trx_addons_vc_add_title_param(),
				trx_addons_vc_add_id_param()
		);
		
		// Add dependencies to params
		$params = trx_addons_vc_add_param_option($params, 'columns', array( 
																	'dependency' => array(
																		'element' => 'type',
																		'value' => array('default','callouts','light','list','iconed','hover','chess','timeline')
																		)
																	)
												);
		$params = trx_addons_vc_add_param_option($params, 'slider', array( 
																	'dependency' => array(
																		'element' => 'type',
																		'value' => array('default','callouts','light','list','iconed','hover','chess','timeline')
																		)
																	)
												);
												
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_services",
				"name" => esc_html__("Services", 'trx_addons'),
				"description" => wp_kses_data( __("Display services from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_services',
				"class" => "trx_sc_services",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_sc_services' );
	}
}
?>