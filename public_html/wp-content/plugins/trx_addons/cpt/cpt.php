<?php
/**
 * ThemeREX Addons Custom post types
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// Include files with CPT
if (!function_exists('trx_addons_cpt_load')) {
	add_action( 'after_setup_theme', 'trx_addons_cpt_load', 2 );
	add_action( 'trx_addons_action_save_options', 'trx_addons_cpt_load', 2 );
	function trx_addons_cpt_load() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['cpt_resume_types'] = apply_filters('trx_addons_cpt_resume_types', array(
			'skills' => esc_html__('Skills', 'trx_addons'),
			'work' => esc_html__('Work experience', 'trx_addons'),
			'education' => esc_html__('Education', 'trx_addons'),
			'services' => esc_html__('Services', 'trx_addons')
		) );
		$TRX_ADDONS_STORAGE['cpt_list'] = apply_filters('trx_addons_cpt_list', array(
			'cars' => array(
				'title' => esc_html__('Cars', 'trx_addons'),
				'post_type' => 'cpt_cars',
				'post_type_slug' => 'cars',
				'taxonomy_type' => 'cpt_cars_type',
				'taxonomy_type_slug' => 'cars_type',
				'taxonomy_status' => 'cpt_cars_status',
				'taxonomy_status_slug' => 'cars_status',
				'taxonomy_maker' => 'cpt_cars_maker',
				'taxonomy_maker_slug' => 'cars_maker',
				'taxonomy_model' => 'cpt_cars_model',
				'taxonomy_model_slug' => 'cars_model',
				'taxonomy_features' => 'cpt_cars_features',
				'taxonomy_features_slug' => 'cars_features',
				'taxonomy_labels' => 'cpt_cars_labels',
				'taxonomy_labels_slug' => 'cars_labels',
				'taxonomy_city' => 'cpt_cars_city',
				'taxonomy_city_slug' => 'cars_city',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'layouts_arh' => array(
					'default_1' => esc_html__('Default /1 column/', 'trx_addons'),
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
					),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons'),
					'slider' => esc_html__('Slider', 'trx_addons')
					)
				),
				'cars_agents' => array(
					'slave' => true,	// Additional post type for the 'cars'
					'title' => esc_html__('Cars Agents', 'trx_addons'),
					'post_type' => 'cpt_cars_agents',
					'post_type_slug' => 'cars_agents',
					'taxonomy' => 'cpt_cars_agency',
					'taxonomy_slug' => 'cars_agency',
					'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
					),
			'certificates' => array(
				'title' => esc_html__('Certificates', 'trx_addons'),
				'post_type' => 'cpt_certificates',
				'post_type_slug' => 'certificates',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt')
				),
			'courses' => array(
				'title' => esc_html__('Courses', 'trx_addons'),
				'post_type' => 'cpt_courses',
				'post_type_slug' => 'courses',
				'taxonomy' => 'cpt_courses_group',
				'taxonomy_slug' => 'courses_group',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'layouts_arh' => array(
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
					),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons')
					)
				),
			'dishes' => array(
				'title' => esc_html__('Dishes', 'trx_addons'),
				'post_type' => 'cpt_dishes',
				'post_type_slug' => 'dishes',
				'taxonomy' => 'cpt_dishes_group',
				'taxonomy_slug' => 'dishes_group',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'layouts_arh' => array(
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
					),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons'),
					'float' => esc_html__('Float', 'trx_addons'),
					'compact' => esc_html__('Compact', 'trx_addons')
					)
				),
			'layouts' => array(
				'title' => esc_html__('Layouts', 'trx_addons'),
				'post_type' => 'cpt_layouts',
				'post_type_slug' => 'layouts',
				'taxonomy' => 'cpt_layouts_group',
				'taxonomy_slug' => 'layouts_group',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail')
				),
			'portfolio' => array(
				'title' => esc_html__('Portfolio', 'trx_addons'),
				'post_type' => 'cpt_portfolio',
				'post_type_slug' => 'portfolio',
				'taxonomy' => 'cpt_portfolio_group',
				'taxonomy_slug' => 'portfolio_group',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'layouts_arh' => array(
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
					),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons'),
					'simple' => esc_html__('Simple', 'trx_addons')
					)
				),
			'post' => array(
				'title' => esc_html__('Post', 'trx_addons')
				),
			'properties' => array(
				'title' => esc_html__('Properties', 'trx_addons'),
				'post_type' => 'cpt_properties',
				'post_type_slug' => 'properties',
				'taxonomy_type' => 'cpt_properties_type',
				'taxonomy_type_slug' => 'properties_type',
				'taxonomy_status' => 'cpt_properties_status',
				'taxonomy_status_slug' => 'properties_status',
				'taxonomy_features' => 'cpt_properties_features',
				'taxonomy_features_slug' => 'properties_features',
				'taxonomy_labels' => 'cpt_properties_labels',
				'taxonomy_labels_slug' => 'properties_labels',
				'taxonomy_country' => 'cpt_properties_country',
				'taxonomy_country_slug' => 'properties_country',
				'taxonomy_state' => 'cpt_properties_state',
				'taxonomy_state_slug' => 'properties_state',
				'taxonomy_city' => 'cpt_properties_city',
				'taxonomy_city_slug' => 'properties_city',
				'taxonomy_neighborhood' => 'cpt_properties_neighborhood',
				'taxonomy_neighborhood_slug' => 'properties_neighborhood',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'layouts_arh' => array(
					'default_1' => esc_html__('Default /1 column/', 'trx_addons'),
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
					),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons'),
					'slider' => esc_html__('Slider', 'trx_addons'),
					'googlemap' => esc_html__('Google map', 'trx_addons')
					)
				),
				'agents' => array(
					'slave' => true,	// Additional post type for the 'properties'
					'title' => esc_html__('Agents', 'trx_addons'),
					'post_type' => 'cpt_agents',
					'post_type_slug' => 'agents',
					'taxonomy' => 'cpt_agency',
					'taxonomy_slug' => 'agency',
					'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
					),
			'resume' => array(
				'title' => esc_html__('Resume', 'trx_addons'),
				'post_type' => 'cpt_resume',
				'post_type_slug' => 'resume',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt')
				),
			'services' => array(
				'title' => esc_html__('Services', 'trx_addons'),
				'post_type' => 'cpt_services',
				'post_type_slug' => 'services',
				'taxonomy' => 'cpt_services_group',
				'taxonomy_slug' => 'services_group',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'layouts_arh' => array(
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons'),
					'light_2'   => esc_html__('Light /2 columns/', 'trx_addons'),
					'light_3'   => esc_html__('Light /3 columns/', 'trx_addons'),
					'callouts_2'=> esc_html__('Callouts /2 columns/', 'trx_addons'),
					'callouts_3'=> esc_html__('Callouts /3 columns/', 'trx_addons'),
					'chess_1'   => esc_html__('Chess /2 columns/', 'trx_addons'),
					'chess_2'   => esc_html__('Chess /4 columns/', 'trx_addons'),
					'chess_3'   => esc_html__('Chess /6 columns/', 'trx_addons'),
					'hover_2'   => esc_html__('Hover /2 columns/', 'trx_addons'),
					'hover_3'   => esc_html__('Hover /3 columns/', 'trx_addons'),
					'iconed_2'  => esc_html__('Iconed /2 columns/', 'trx_addons'),
					'iconed_3'  => esc_html__('Iconed /3 columns/', 'trx_addons')
					),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons'),
					'light' => esc_html__('Light', 'trx_addons'),
					'iconed' => esc_html__('Iconed', 'trx_addons'),
					'callouts' => esc_html__('Callouts', 'trx_addons'),
					'list' => esc_html__('List', 'trx_addons'),
					'hover' => esc_html__('Hover', 'trx_addons'),
					'chess' => esc_html__('Chess', 'trx_addons'),
					'timeline' => esc_html__('Timeline', 'trx_addons'),
					'tabs' => esc_html__('Tabs', 'trx_addons'),
					'tabs_simple' => esc_html__('Tabs (simple)', 'trx_addons')
					)
				),
			'sport' => array(
				'title' => esc_html__('Sport Reviews', 'trx_addons'),
				'layouts_arh' => array(
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
					)
				),
				'competitions' => array(
					'slave' => true,	// Additional post type for the 'sport'
					'title' => esc_html__('Competitions', 'trx_addons'),
					'post_type' => 'cpt_competitions',
					'post_type_slug' => 'competitions',
					'taxonomy' => 'cpt_competitions_sports',
					'taxonomy_slug' => 'sports',
					'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
					),
				'rounds' => array(
					'slave' => true,	// Additional post type for the 'sport'
					'title' => esc_html__('Rounds', 'trx_addons'),
					'post_type' => 'cpt_rounds',
					'post_type_slug' => 'rounds',
					'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
					),
				'matches' => array(
					'slave' => true,	// Additional post type for the 'sport'
					'title' => esc_html__('Matches', 'trx_addons'),
					'post_type' => 'cpt_matches',
					'post_type_slug' => 'matches',
					'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
					),
				'players' => array(
					'slave' => true,	// Additional post type for the 'sport'
					'title' => esc_html__('Players', 'trx_addons'),
					'post_type' => 'cpt_players',
					'post_type_slug' => 'players',
					'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields')
					),
			'team' => array(
				'title' => esc_html__('Team', 'trx_addons'),
				'post_type' => 'cpt_team',
				'post_type_slug' => 'team',
				'taxonomy' => 'cpt_team_group',
				'taxonomy_slug' => 'team_group',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'),
				'layouts_arh' => array(
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
					),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons'),
					'short' => esc_html__('Short', 'trx_addons'),
					'featured' => esc_html__('Featured', 'trx_addons')
					)
				),
			'testimonials' => array(
				'title' => esc_html__('Testimonials', 'trx_addons'),
				'post_type' => 'cpt_testimonials',
				'post_type_slug' => 'testimonials',
				'taxonomy' => 'cpt_testimonials_group',
				'taxonomy_slug' => 'testimonials_group',
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'),
				'layouts_sc' => array(
					'default' => esc_html__('Default', 'trx_addons'),
					'simple' => esc_html__('Simple', 'trx_addons')
					)
				)
			)
		);
		if (is_array($TRX_ADDONS_STORAGE['cpt_list']) && count($TRX_ADDONS_STORAGE['cpt_list']) > 0) {
			foreach ($TRX_ADDONS_STORAGE['cpt_list'] as $cpt => $params) {
				if ( empty($params['slave']) 
					&& trx_addons_components_is_allowed('cpt', $cpt)
					&& ($fdir = trx_addons_get_file_dir("cpt/{$cpt}/{$cpt}.php")) != '') { 
					include_once $fdir;
				}
			}
		}
	}
}

// Add 'CPT' section in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_options')) {
	add_action( 'trx_addons_filter_options', 'trx_addons_cpt_options');
	function trx_addons_cpt_options($options) {

		trx_addons_array_insert_before($options, 'api_section', array(
			// Section 'CPT' - main options
			'cpt_section' => array(
				"title" => esc_html__('CPT', 'trx_addons'),
				"desc" => wp_kses_data( __('CPT (Custom Post Types) options', 'trx_addons') ),
				"type" => "section"
			)
		));
		return $options;
	}
}

// Return list of the allowed CPT
if (!function_exists('trx_addons_get_cpt_list')) {
	function trx_addons_get_cpt_list() {
		global $TRX_ADDONS_STORAGE;
		$list = array();
		if (is_array($TRX_ADDONS_STORAGE['cpt_list']) && count($TRX_ADDONS_STORAGE['cpt_list']) > 0) {
			foreach ($TRX_ADDONS_STORAGE['cpt_list'] as $cpt => $params) {
				if (!empty($params['post_type'])) $list[$params['post_type']] = $params['title'];
			}
		}
		return $list;
	}
}

// Return param's value from the CPT definition
if (!function_exists('trx_addons_cpt_param')) {
	function trx_addons_cpt_param($cpt='', $param='') {
		global $TRX_ADDONS_STORAGE;
		$rez = '';
		if (!empty($TRX_ADDONS_STORAGE['cpt_list'][$cpt]))
			$rez = $TRX_ADDONS_STORAGE['cpt_list'][$cpt][$param];
		else {
			foreach ($TRX_ADDONS_STORAGE['cpt_list'] as $slug => $params) {
				if (!empty($params['post_type']) && $params['post_type'] == $cpt) {
					$rez = $params[$param];
					break;
				}
			}
		}
		return $rez;
	}
}
?>