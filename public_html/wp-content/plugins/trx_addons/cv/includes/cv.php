<?php
/**
 * CV Card functions and handlers
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.4.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if current page is CV homepage
if (!function_exists('trx_addons_is_cv_page')) {
	function trx_addons_is_cv_page() {
		global $post;
		$cv = trx_addons_get_value_gp('cv');
		return trx_addons_is_on(trx_addons_get_option('cv_enable')) && ($cv=='' || $cv!=0)
					&& apply_filters('trx_addons_filter_is_cv_page', $cv==1
							|| (trx_addons_is_on(trx_addons_get_option('cv_home')) 
									&& (trx_addons_is_on(trx_addons_get_option('cv_hide_blog')) 
											|| (is_front_page() && (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], home_url())===false))
										)
								)
							);
	}
}


// Return link of the CV homepage
if (!function_exists('trx_addons_get_cv_page_link')) {
	function trx_addons_get_cv_page_link($params=array()) {
		$params['cv'] = 1;
		return trx_addons_add_to_url(home_url(), $params);
	}
}


// Redirect to the CV template if user navigate to the CV homepage
if (!function_exists('trx_addons_cv_get_single_template')) {
	add_filter('home_template', 'trx_addons_cv_get_template');
	add_filter('page_template', 'trx_addons_cv_get_template');
	add_filter('single_template', 'trx_addons_cv_get_template');
	function trx_addons_cv_get_template($template) {
		if (trx_addons_get_value_gp('cv_prn')==1 )
			$template = trx_addons_get_file_dir('cv/templates/cv.print.tpl.php');
		else if (trx_addons_is_cv_page())
			$template = trx_addons_get_file_dir('cv/templates/cv.tpl.php');
		return $template;
	}
}

	
// Add buttons 'Blog' and 'CV' in the blog
if ( !function_exists( 'trx_addons_cv_add_buttons' ) ) {
	add_action("wp_footer", 'trx_addons_cv_add_buttons', 100);
	function trx_addons_cv_add_buttons() {
		// If CV disabled - don't show buttons
		if (trx_addons_is_off(trx_addons_get_option('cv_enable'))) return;
		// If blog is not used - don't show buttons
		if (trx_addons_is_on(trx_addons_get_option('cv_home')) && trx_addons_is_on(trx_addons_get_option('cv_hide_blog'))) return;
		// If is print version - don't show buttons
		if (trx_addons_get_value_gp('cv_prn')==1 ) return;
		// If is CV screen - show "Blog" button
		if (trx_addons_is_cv_page()) {
			if (($bt = trx_addons_get_option('cv_button_blog'))=='')
				$bt = trx_addons_get_file_url('cv/images/button_blog.png');
			if ($bt) {
				$is = trx_addons_getimagesize($bt);
				echo '<a href="'.esc_url(trx_addons_add_to_url(home_url(), array('cv'=>0))).'" class="trx_addons_cv_button trx_addons_cv_button_blog"><img src="'.esc_url($bt).'" '.(!empty($is[3]) ? $is[3] : '').' alt="'.esc_html__('Go to Blog ...', 'trx_addons').'"></a>';
			}
		// If is Blog screen - show "VCard" button
		} else {
			if (($bt = trx_addons_get_option('cv_button_cv'))=='')
				$bt = trx_addons_get_file_url('cv/images/button_cv.png');
			if ($bt) {
				$is = trx_addons_getimagesize($bt);
				echo '<a href="'.esc_url(trx_addons_get_cv_page_link()).'" class="trx_addons_cv_button trx_addons_cv_button_cv"><img src="'.esc_url($bt).'" '.(!empty($is[3]) ? $is[3] : '').' alt="'.esc_html__('Go to VCard ...', 'trx_addons').'"></a>';
			}
		}
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cv_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cv_load_scripts_front');
	function trx_addons_cv_load_scripts_front() {
		if (trx_addons_get_value_gp('cv_prn')==1 ) {
			wp_enqueue_style( 'trx_addons-cv.print', trx_addons_get_file_url('cv/css/cv.print.css'), array(), null );
		} else {
			if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
				if (trx_addons_is_cv_page()) {
					wp_enqueue_style( 'trx_addons-cv', trx_addons_get_file_url('cv/css/cv.css'), array(), null );
					wp_enqueue_script( 'trx_addons-cv', trx_addons_get_file_url('cv/js/cv.js'), array('jquery'), null, true );
				}
			}
			if (trx_addons_is_cv_page()) {
				global $wp_styles;
				wp_enqueue_style( 'trx_addons-cv-ie9', trx_addons_get_file_url('cv/css/cv.ie9.css'), array(), null );
				$wp_styles->add_data( 'trx_addons-cv-ie9', 'conditional', 'lte IE 9' );
				if (!is_customize_preview())
					wp_enqueue_script('jquery-ui-tabs', false, array('jquery', 'jquery-ui-core'), null, true);
				wp_enqueue_script('jquery-ui-accordion', false, array('jquery', 'jquery-ui-core'), null, true);
			}
			wp_enqueue_style( 'trx_addons-cv_buttons', trx_addons_get_file_url('cv/css/cv.buttons.css'), array(), null );
		}
	}
}

	
// Load responsive styles after all other styles
if ( !function_exists( 'trx_addons_cv_load_scripts_front100' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cv_load_scripts_front100', 100);
	function trx_addons_cv_load_scripts_front100() {
		if (trx_addons_get_value_gp('cv_prn')!=1 && trx_addons_is_on(trx_addons_get_option('debug_mode')) && trx_addons_is_cv_page())
			wp_enqueue_style( 'trx_addons-cv.responsive', trx_addons_get_file_url('cv/css/cv.responsive.css'), array(), null );
	}
}

	
// Merge CV specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cv_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cv_merge_styles');
	function trx_addons_cv_merge_styles($list) {
		$list[] = 'cv/css/cv.css';
		return $list;
	}
}

	
// Merge CV specific styles into single stylesheet (add responsive.css after all other css)
if ( !function_exists( 'trx_addons_cv_merge_styles100' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cv_merge_styles100', 100);
	function trx_addons_cv_merge_styles100($list) {
		$list[] = 'cv/css/cv.responsive.css';
		return $list;
	}
}

	
// Merge CV specific scripts into single file
if ( !function_exists( 'trx_addons_cv_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_cv_merge_scripts');
	function trx_addons_cv_merge_scripts($list) {
		$list[] = 'cv/js/cv.js';
		return $list;
	}
}

// AJAX handler for the trx_addons_ajax_get_posts action
if ( !function_exists( 'trx_addons_cv_ajax_get_posts' ) ) {
	add_action('wp_ajax_trx_addons_ajax_get_posts',			'trx_addons_cv_ajax_get_posts');
	add_action('wp_ajax_nopriv_trx_addons_ajax_get_posts',	'trx_addons_cv_ajax_get_posts');
	function trx_addons_cv_ajax_get_posts() {
		if ( !wp_verify_nonce( trx_addons_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = apply_filters('trx_addons_cv_filter_ajax_get_posts', array('error'=>'', 'data' => ''));

		if ($response['data']=='' && $response['error']=='') $response['error'] = esc_html__('Invalid query parameters!', 'trx_addons');
		
		echo json_encode($response);
		die();
	}
}
?>