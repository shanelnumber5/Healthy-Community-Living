<?php
/**
 * ThemeREX Shortcodes
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Define list with shortcodes
if (!function_exists('trx_addons_sc_setup')) {
	add_action( 'after_setup_theme', 'trx_addons_sc_setup', 2 );
	add_action( 'trx_addons_action_save_options', 'trx_addons_sc_setup', 2 );
	function trx_addons_sc_setup() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['sc_list'] = apply_filters('trx_addons_sc_list', array(
			'action' => array(
							'title' => __('Actions', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'simple' => esc_html__('Simple', 'trx_addons'),
								'event' => esc_html__('Event', 'trx_addons')
							)
						),
			'anchor' => array(
							'title' => __('Anchor', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons')
							)
						),
			'blogger' => array(
							'title' => __('Blogger', 'trx_addons'),
							'layouts_sc' => array(
/*
								'default' => esc_html__('Default', 'trx_addons'),
								'classic' => esc_html__('Classic', 'trx_addons'),
								'plain' => esc_html__('Plain', 'trx_addons')
*/
								'default' => trx_addons_get_file_url('shortcodes/blogger/type-default.png'),
								'classic' => trx_addons_get_file_url('shortcodes/blogger/type-classic.png'),
								'plain' => trx_addons_get_file_url('shortcodes/blogger/type-plain.png')
							)
						),
			'button' => array(
							'title' => __('Button', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'bordered' => esc_html__('Bordered', 'trx_addons'),
                                'default2' => esc_html__('Default Style 2', 'trx_addons'),
								'simple' => esc_html__('Simple', 'trx_addons')
							)
						),
			'content' => array(
							'title' => __('Content', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'countdown' => array(
							'title' => __('Countdown', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'circle' => esc_html__('Circle', 'trx_addons')
							)
						),
			'form' => array(
							'title' => __('Forms', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'modern' => esc_html__('Modern', 'trx_addons'),
								'detailed' => esc_html__('Detailed', 'trx_addons')
							)
						),
			'googlemap' => array(
							'title' => __('Google map', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'detailed' => esc_html__('Detailed', 'trx_addons')
							)
						),
			'icons' => array(
							'title' => __('Icons', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'modern' => esc_html__('Modern', 'trx_addons')
							)
						),
			'popup' => array(
							'title' => __('Popup', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'price' => array(
							'title' => __('Price block', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'promo' => array(
							'title' => __('Promo', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'modern' => esc_html__('Modern', 'trx_addons'),
								'blockquote' => esc_html__('Blockquote', 'trx_addons')
							)
						),
			'skills' => array(
							'title' => __('Skills', 'trx_addons'),
							'layouts_sc' => array(
								'pie' => esc_html__('Pie', 'trx_addons'),
								'counter' => esc_html__('Counter', 'trx_addons')
							)
						),
			'socials' => array(
							'title' => __('Socials', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Only icons', 'trx_addons'),
								'names' => esc_html__('Only names', 'trx_addons'),
								'icons_names' => esc_html__('Icon + name', 'trx_addons')
							)
						),
			'table' => array(
							'title' => __('Table', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'title' => array(
							'title' => __('Title', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'shadow' => esc_html__('Shadow', 'trx_addons')
							)
						)
			)
		);
	}
}

// Include files with shortcodes
if (!function_exists('trx_addons_sc_load')) {
	add_action( 'after_setup_theme', 'trx_addons_sc_load', 6 );
	add_action( 'trx_addons_action_save_options', 'trx_addons_sc_load', 6 );
	function trx_addons_sc_load() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		if (is_array($TRX_ADDONS_STORAGE['sc_list']) && count($TRX_ADDONS_STORAGE['sc_list']) > 0) {
			foreach ($TRX_ADDONS_STORAGE['sc_list'] as $sc=>$params) {
				if (trx_addons_components_is_allowed('sc', $sc)
					&& ($fdir = trx_addons_get_file_dir("shortcodes/{$sc}/{$sc}.php")) != '') { 
					include_once $fdir;
				}
			}
		}
	}
}


	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_load_scripts_front');
	function trx_addons_sc_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc', trx_addons_get_file_url('shortcodes/shortcodes.css'), array(), null );
			wp_enqueue_script( 'trx_addons-sc', trx_addons_get_file_url('shortcodes/shortcodes.js'), array('jquery'), null, true );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_merge_styles');
	function trx_addons_sc_merge_styles($list) {
		$list[] = 'shortcodes/shortcodes.css';
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_merge_scripts');
	function trx_addons_sc_merge_scripts($list) {
		$list[] = 'shortcodes/shortcodes.js';
		return $list;
	}
}

// Check if shortcode name is in the stack
if (!function_exists('trx_addons_sc_stack_check')) {
    function trx_addons_sc_stack_check($sc=false) {
        global $TRX_ADDONS_STORAGE;
        return is_array( $TRX_ADDONS_STORAGE['sc_stack'] )
            ? ( ! empty( $sc )
                ? in_array( $sc, $TRX_ADDONS_STORAGE['sc_stack'] )
                : count( $TRX_ADDONS_STORAGE['sc_stack'] ) > 0
            )
            : false;
    }
}


// Shortcodes parts
//---------------------------------------

// Prepare Id, custom CSS and other parameters in the shortcode's atts
if (!function_exists('trx_addons_sc_prepare_atts')) {
	function trx_addons_sc_prepare_atts($sc, $atts, $defa) {
		// Merge atts with default values
		$atts = trx_addons_html_decode(shortcode_atts(apply_filters('trx_addons_sc_atts', $defa, $sc), $atts));
		// Unsafe item description
		if (!empty($atts['description']))
			$atts['description'] = trim( vc_value_from_safe( $atts['description'] ) );
		// Generate id (if empty)
        if (empty($atts['id']))
        	$atts['id'] = str_replace('trx_', '', $sc) . '_' . str_replace('.', '', mt_rand());
        // Add custom CSS class
        if (!empty($atts['css'])
            && (trx_addons_sc_stack_check('show_layout_vc') || strpos($atts['css'], '.vc_custom_') !== false)
            && defined('VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG')
            && function_exists('vc_shortcode_custom_css_class')
        ) {
            $atts['class'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
                (!empty($atts['class']) ? $atts['class'] . ' ' : '') . vc_shortcode_custom_css_class( $atts['css'], ' ' ),
                $sc,
                $atts);
            $atts['css'] = '';
        }
 		return apply_filters('trx_addons_filter_sc_prepare_atts', $atts, $sc);
	}
}

// Enqueue iconed fonts
if (!function_exists('trx_addons_load_icons')) {
	function trx_addons_load_icons($list='') {
		if (!empty($list) && function_exists('vc_icon_element_fonts_enqueue')) {
			$list = explode(',', $list);
			foreach ($list as $icon_type)
				vc_icon_element_fonts_enqueue($icon_type);
		}
	}
}

// Display title, subtitle and description for some shortcodes
if (!function_exists('trx_addons_sc_show_titles')) {
	function trx_addons_sc_show_titles($sc, $args, $size='') {
		trx_addons_get_template_part('templates/tpl.sc_titles.php',
										'trx_addons_args_sc_show_titles',
										compact('sc', 'args', 'size')
									);
	}
}

// Display link button or image for some shortcodes
if (!function_exists('trx_addons_sc_show_links')) {
	function trx_addons_sc_show_links($sc, $args) {
		trx_addons_get_template_part('templates/tpl.sc_links.php',
										'trx_addons_args_sc_show_links',
										compact('sc', 'args')
									);
	}
}

// Show post meta block: post date, author, categories, counters, etc.
if ( !function_exists('trx_addons_sc_show_post_meta') ) {
	function trx_addons_sc_show_post_meta($sc, $args=array()) {
		$args = array_merge(array(
			'components' => '',	//categories,tags,date,author,counters,share,edit
			'counters' => '',
			'seo' => false,
			'echo' => true
			), $args);
		if (($meta = apply_filters('trx_addons_filter_post_meta', '', array_merge($args, array('echo'=>false)))) != '') {
			if (!empty($args['echo'])) trx_addons_show_layout($meta);
			else return $meta;
		} else {
			if (empty($args['echo'])) ob_start();
			trx_addons_get_template_part('templates/tpl.sc_post_meta.php',
											'trx_addons_args_sc_show_post_meta',
											compact('sc', 'args')
										);
			if (empty($args['echo'])) {
				$meta = ob_get_contents();
				ob_end_clean();
				return $meta;
			}
		}
	}
}

// Display begin of the slider layout for some shortcodes
if (!function_exists('trx_addons_sc_show_slider_wrap_start')) {
	function trx_addons_sc_show_slider_wrap_start($sc, $args) {
		trx_addons_get_template_part('templates/tpl.sc_slider_start.php',
										'trx_addons_args_sc_show_slider_wrap',
										compact('sc', 'args')
									);
	}
}

// Display end of the slider layout for some shortcodes
if (!function_exists('trx_addons_sc_show_slider_wrap_end')) {
	function trx_addons_sc_show_slider_wrap_end($sc, $args) {
		trx_addons_get_template_part('templates/tpl.sc_slider_end.php',
										'trx_addons_args_sc_show_slider_wrap', 
										compact('sc', 'args')
									);
	}
}


// Shortcode's common params
//---------------------------------------------------------

// Return ID, Class, CSS params for the VC
if ( !function_exists( 'trx_addons_vc_add_id_param' ) ) {
	function trx_addons_vc_add_id_param($group=false) {
		$params = array(
					// Common VC parameters
					array(
						"param_name" => "id",
						"heading" => esc_html__("Element ID", 'trx_addons'),
						"description" => wp_kses_data( __("ID for current element", 'trx_addons') ),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "class",
						"heading" => esc_html__("Element CSS class", 'trx_addons'),
						"description" => wp_kses_data( __("CSS class for current element", 'trx_addons') ),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						'param_name' => 'css',
						'heading' => __( 'CSS box', 'trx_addons' ),
						'group' => __( 'Design Options', 'trx_addons' ),
						'type' => 'css_editor'
					)
				);

		// Add param 'group' if not empty
		if ($group===false)
			$group = esc_html__('ID &amp; Class', 'trx_addons');
		
		if (!empty($group)) {
			$params[0]['group'] = $group;
			$params[1]['group'] = $group;
		}

		return $params;
	}
}

// Return slider params for the VC
if ( !function_exists( 'trx_addons_vc_add_slider_param' ) ) {
	function trx_addons_vc_add_slider_param($group=false) {
		$params = array(
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", 'trx_addons'),
						"description" => wp_kses_data( __("Show items as slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6 vc_new_row',
						"admin_label" => true,
						"std" => "0",
						"value" => array(esc_html__("Slider", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space", 'trx_addons'),
						"description" => wp_kses_data( __("Space between slides", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "slider_controls",
						"heading" => esc_html__("Slider controls", 'trx_addons'),
						"description" => wp_kses_data( __("Show arrows in the slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6 vc_new_row',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "none",
						"value" => array(
							esc_html__('None', 'trx_addons') => 'none',
							esc_html__('Side', 'trx_addons') => 'side',
							esc_html__('Top', 'trx_addons') => 'top',
							esc_html__('Bottom', 'trx_addons') => 'bottom'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider_pagination",
						"heading" => esc_html__("Slider pagination", 'trx_addons'),
						"description" => wp_kses_data( __("Show bullets in the slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "none",
						"value" => array(
							esc_html__('None', 'trx_addons') => 'none',
							esc_html__('Bottom', 'trx_addons') => 'bottom',
							esc_html__('Left', 'trx_addons') => 'left',
							esc_html__('Right', 'trx_addons') => 'right'
						),
						"type" => "dropdown"
					)
				);

		// Add param 'group' if not empty
		if ($group===false)
			$group = esc_html__('Slider', 'trx_addons');
		if (!empty($group))
			foreach ($params as $k=>$v)
				$params[$k]['group'] = $group;

		return $params;
	}
}

// Return title params for the VC
if ( !function_exists( 'trx_addons_vc_add_title_param' ) ) {
	function trx_addons_vc_add_title_param($group=false, $button=true) {
		$params = array(
					array(
						"param_name" => "title_style",
						"heading" => esc_html__("Title style", 'trx_addons'),
						"description" => wp_kses_data( __("Select style of the title and subtitle", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "default",
				        'save_always' => true,
						"value" => apply_filters('trx_addons_sc_type', array_flip(trx_addons_components_get_allowed_layouts('sc', 'title')), 'trx_sc_title' ),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title_tag",
						"heading" => esc_html__("Title tag", 'trx_addons'),
						"description" => wp_kses_data( __("Select tag (level) of the title", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => "none",
						"value" => array(
							esc_html__('Default', 'trx_addons') => 'none',
							esc_html__('Heading 1', 'trx_addons') => 'h1',
							esc_html__('Heading 2', 'trx_addons') => 'h2',
							esc_html__('Heading 3', 'trx_addons') => 'h3',
							esc_html__('Heading 4', 'trx_addons') => 'h4',
							esc_html__('Heading 5', 'trx_addons') => 'h5',
							esc_html__('Heading 6', 'trx_addons') => 'h6'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title_align",
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
						"param_name" => "title",
						"heading" => esc_html__("Title", 'trx_addons'),
						"description" => wp_kses_data( __("Title of the block. Enclose any words in {{ and }} to accent them", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-6',
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", 'trx_addons'),
						"description" => wp_kses_data( __("Subtitle of the block", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6',
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", 'trx_addons'),
						"description" => wp_kses_data( __("Description of the block", 'trx_addons') ),
						"type" => "textarea_safe"
					),
				);
		
		// Add button's params
		if ($button)
			$params = array_merge($params, array(
					array(
						"param_name" => "link",
						"heading" => esc_html__("Button URL", 'trx_addons'),
						"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "link_text",
						"heading" => esc_html__("Button's text", 'trx_addons'),
						"description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "link_image",
						"heading" => esc_html__("Button's image", 'trx_addons'),
						"description" => wp_kses_data( __("Select the promo image from the library for this button", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "attach_image"
					)
				)
			);

		// Add param 'group' if not empty
		if ($group===false)
			$group = esc_html__('Titles', 'trx_addons');
		if (!empty($group))
			foreach ($params as $k=>$v)
				$params[$k]['group'] = $group;

		return $params;
	}
}

// Return query params for the VC
if ( !function_exists( 'trx_addons_vc_add_query_param' ) ) {
	function trx_addons_vc_add_query_param($group=false) {
		$params = array(
					array(
						"param_name" => "ids",
						"heading" => esc_html__("IDs to show", 'trx_addons'),
						"description" => wp_kses_data( __("Comma separated IDs list to show. If not empty - parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Count", 'trx_addons'),
						"description" => wp_kses_data( __("Specify number of items to display", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'ids',
							'is_empty' => true
						),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", 'trx_addons'),
						"description" => wp_kses_data( __("Specify number of columns. If empty - auto detect by items number", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset", 'trx_addons'),
						"description" => wp_kses_data( __("Specify number of items to skip before showed items", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'ids',
							'is_empty' => true
						),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", 'trx_addons'),
						"description" => wp_kses_data( __("Select how to sort the posts", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6 vc_new_row',
						"admin_label" => true,
				        'save_always' => true,
						"value" => array(
							esc_html__('None', 'trx_addons') => 'none',
							esc_html__('Post ID', 'trx_addons') => 'ID',
							esc_html__('Date', 'trx_addons') => 'post_date',
							esc_html__('Title', 'trx_addons') => 'title',
							esc_html__('Random', 'trx_addons') => 'rand'
						),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", 'trx_addons'),
						"description" => wp_kses_data( __("Select sort order", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6',
						"value" => array(
							esc_html__('Descending', 'trx_addons') => 'desc',
							esc_html__('Ascending', 'trx_addons') => 'asc'
						),
						'save_always' => true,
						"std" => "asc",
						"type" => "dropdown"
					)
				);

		// Add param 'group' if not empty
		if ($group===false)
			$group = esc_html__('Query', 'trx_addons');
		if (!empty($group))
			foreach ($params as $k=>$v)
				$params[$k]['group'] = $group;

		return $params;
	}
}

// Return hide_on_mobile param for the VC
if ( !function_exists( 'trx_addons_vc_add_hide_param' ) ) {
	function trx_addons_vc_add_hide_param($group=false) {
		$params = array(
					array(
						"param_name" => "hide_on_tablet",
						"heading" => esc_html__("Hide on tablets", 'trx_addons'),
						"description" => wp_kses_data( __("Hide this item on tablets", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array(esc_html__("Hide on tablets", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_on_mobile",
						"heading" => esc_html__("Hide on mobile devices", 'trx_addons'),
						"description" => wp_kses_data( __("Hide this item on mobile devices", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array(esc_html__("Hide on mobile devices", 'trx_addons') => "1"),
						"type" => "checkbox"
					)
				);

		// Add param 'group' if not empty
		if (!empty($group))
			foreach ($params as $k=>$v)
				$params[$k]['group'] = $group;

		return $params;
	}
}

// Return icon params for the VC
if ( !function_exists( 'trx_addons_vc_add_icon_param' ) ) {
	function trx_addons_vc_add_icon_param($group=false, $only_socials=false) {
		if (trx_addons_get_setting('icons_selector') == 'vc') {
			
			// Standard VC icons selector
			$params = array(
						array(
							'type' => 'dropdown',
							'heading' => __( 'Icon library', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'value' => array(
								__( 'Font Awesome', 'trx_addons' ) => 'fontawesome',
	/*
								__( 'Open Iconic', 'trx_addons' ) => 'openiconic',
								__( 'Typicons', 'trx_addons' ) => 'typicons',
								__( 'Entypo', 'trx_addons' ) => 'entypo',
								__( 'Linecons', 'trx_addons' ) => 'linecons'
	*/
							),
							'std' => 'fontswesome',
							'param_name' => 'icon_type',
							'description' => __( 'Select icon library.', 'trx_addons' ),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-8',
							'param_name' => 'icon_fontawesome',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'fontawesome'
	
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'fontawesome',
							),
						),
	/*
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_openiconic',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'openiconic'
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'openiconic',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_typicons',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'typicons',
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'typicons',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_entypo',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'entypo',
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'entypo',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_linecons',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'linecons',
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'linecons',
							),
						)
	*/					
					);

		} else {

			// Internal popup with icons list
			$style = $only_socials ? trx_addons_get_setting('socials_type') : trx_addons_get_setting('icons_type');
			$params = array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", 'trx_addons'),
					"description" => wp_kses_data( __("Select icon", 'trx_addons') ),
					"value" => $style == 'icons' 
									? trx_addons_array_from_list(trx_addons_get_list_icons()) 
									: trx_addons_get_list_files($only_socials ? 'css/socials' : 'css/icons.png', 'png'),
					"style" => $style,
					"type" => "icons"
				)
			);
		}
		
		// Add param 'group' if not empty
		if ($group===false)
			$group = esc_html__('Icons', 'trx_addons');
		if (!empty($group))
			foreach ($params as $k=>$v)
				$params[$k]['group'] = $group;

		return $params;
	}
}
?>