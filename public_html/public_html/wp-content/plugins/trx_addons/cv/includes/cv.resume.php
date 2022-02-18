<?php
/**
 * CV Card: Resume
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Return true if current screen is a resume post
if ( !function_exists( 'trx_addons_cv_is_resume_page' ) ) {
	add_filter('trx_addons_filter_is_cv_page', 'trx_addons_cv_is_resume_page');
	function trx_addons_cv_is_resume_page($cv = false) {
		global $post;
		return $cv || (is_single() && $post->post_type==TRX_ADDONS_CPT_RESUME_PT);
	}
}



// -----------------------------------------------------------------
// -- Load scripts and styles
// -----------------------------------------------------------------

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cv_resume_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cv_resume_load_scripts_front');
	function trx_addons_cv_resume_load_scripts_front() {
		if (trx_addons_get_value_gp('cv_prn')=='' && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			if (trx_addons_is_cv_page()) {
				wp_enqueue_style( 'trx_addons-cv.resume', trx_addons_get_file_url('cv/css/cv.resume.css'), array(), null );
			}
		}
	}
}

	
// Merge CV specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cv_resume_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cv_resume_merge_styles');
	function trx_addons_cv_resume_merge_styles($list) {
		$list[] = 'cv/css/cv.resume.css';
		return $list;
	}
}

// Posts utilities
// -----------------------------------------------------------------

// Display resume posts from specified type
if (!function_exists('trx_addons_cv_resume_show_posts')) {
	function trx_addons_cv_resume_show_posts($slug, $page=1) {
		global $TRX_ADDONS_STORAGE;
		
		$style = $slug;
		$prn = trx_addons_get_value_gp('cv_prn')==1 ? '_prn' : '';
		$all_posts = (int) $page == -1;
		$page  = max(1, (int) $page);
		$count = max(1, (int) trx_addons_get_option('cv_resume_count_'.$style));
		$args = array(
			'post_type' => TRX_ADDONS_CPT_RESUME_PT,
			'post_status' => 'publish',
			'posts_per_page' => $all_posts ? -1 : $count,
			'offset' => $all_posts ? 0 : max(0, ($page-1)*$count),
			'ignore_sticky_posts' => true,
			'orderby' => 'date',
			'order' => 'asc'
		);
		if ($slug != 'all') {
			$args['meta_query'] = array();
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
				'key' => 'trx_addons_options_resume_type',
				'value' => $slug,
				'compare' => '='
			);
		}
		$query = new WP_Query( $args );
		if ($query->found_posts > 0) {
			if ($count > $query->found_posts) $count = $query->found_posts;
			$delimiter = max(0, min(1, (int) trx_addons_get_option('cv_resume_delimiter_'.$style)));
			if ( empty($prn) ) {								// Not Print version
				$columns = max(1, min(12, (int) trx_addons_get_option('cv_resume_columns_'.$slug)));
				//if ($columns > $count) $columns = $count;
				
				$slider = trx_addons_is_on(trx_addons_get_option('cv_resume_slider_'.$slug)) && $count > $columns;
				$slides_space = max(0, (int) trx_addons_get_option('cv_resume_slides_space_'.$slug));
				if ($delimiter > 0) $slides_space = 0;
	
			} else {											// Print version
				$columns = 1;
				$slider = false;
				$slides_space = 0;
			}
			?>
			<div class="trx_addons_cv<?php echo esc_attr($prn); ?>_resume trx_addons_cv<?php echo esc_attr($prn); ?>_resume_style_<?php echo esc_attr($style); ?><?php if ($slider) echo ' swiper-slider-container slider_swiper slider_noresize slider_controls slider_controls_bottom'; ?><?php if (trx_addons_is_on(trx_addons_get_option('cv_resume_print_full'))) echo ' trx_addons_cv'.esc_attr($prn).'_resume_text_full'; ?>"
					<?php
					if ( empty($prn) ) {						// Not Print version
						echo ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
							. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
							. ' data-slides-min-width="150"';
					}
					?>
					>
				<?php
				if ( empty($prn) ) {							// Not Print version
					if ($slider) {
						?><div class="slides swiper-wrapper"><?php
					} else if ($columns > 1) {
						?><div class="trx_addons_cv_resume_columns trx_addons_cv_resume_columns_<?php echo esc_attr($columns); ?> <?php echo esc_attr(trx_addons_get_columns_wrap_class($style=='skills')); ?> <?php echo intval($delimiter) == 0 ? 'columns_padding_bottom' : 'no_margin'; ?>"><?php
					}	
				}
				
				$number = 0;
				while ( $query->have_posts() ) { 
					$query->the_post();
					$number++;
					trx_addons_get_template_part('cv/templates/cv.resume.style-'.trx_addons_esc($style).'.tpl.php',
													'trx_addons_args_cv_resume',
													array(
														'page' => $page,
														'count' => $count,
														'number' => $number,
														'columns' => $columns,
														'slider' => $slider
														)
												);
				}
	
				wp_reset_postdata();
		
				if ( empty($prn) ) {							// Not Print version
					if ($slider) {
						?>
						</div>
						<div class="slider_controls_wrap"><a class="slider_prev swiper-button-prev" href="#"></a><a class="slider_next swiper-button-next" href="#"></a></div>
						<?php
					} else if ($columns > 1) {
						?></div><?php
					}
				}
				?>
			</div><!-- /.trx_addons_cv_resume -->
			<?php
			if ( empty($prn) ) {								// Not Print version
				trx_addons_pagination(array(
					'total_posts' => $query->found_posts,
					'posts_per_page' => $count,
					'cur_page' => $page,
					'base_link' => trx_addons_get_cv_page_link(array('section'=>TRX_ADDONS_CPT_RESUME_PT, 'tab'=>$slug))
				));
			}
		}
	}
}

// AJAX handler for the trx_addons_ajax_get_posts action
if ( !function_exists( 'trx_addons_cv_resume_ajax_get_posts' ) ) {
	add_filter('trx_addons_cv_filter_ajax_get_posts', 'trx_addons_cv_resume_ajax_get_posts');
	function trx_addons_cv_resume_ajax_get_posts($response) {
		
		$section = $_REQUEST['section'];
		$tab = $_REQUEST['tab'];
		$page = $_REQUEST['page'];
	
		if ($section == trx_addons_cpt_param('resume', 'post_type_slug')) {
			if (!empty($tab) && $page > 0) {
				ob_start();
				trx_addons_cv_resume_show_posts($tab, $page);
				$response['data'] = ob_get_contents();
				ob_end_clean();
				if (empty($response['data'])) {
					$response['error'] = esc_html__('Sorry, but nothing matched your search criteria.', 'trx_addons');
				}
			} else {
				$response['error'] = esc_html__('Invalid query parameters!', 'trx_addons');
			}
		}
		
		return $response;
	}
}

// Show links on the tab in the breadcrumbs
if ( !function_exists( 'trx_addons_cv_resume_show_breadcrumbs_terms' ) ) {
	add_action('trx_addons_cv_action_show_breadcrumbs_terms', 'trx_addons_cv_resume_show_breadcrumbs_terms');
	function trx_addons_cv_resume_show_breadcrumbs_terms() {
		global $TRX_ADDONS_STORAGE;
		if (get_post_type()==TRX_ADDONS_CPT_RESUME_PT && ($type = get_post_meta(get_the_ID(), 'trx_addons_options_resume_type', true))!='' && !empty($TRX_ADDONS_STORAGE['cpt_resume_types'][$type])) {
			?>
			<a href="<?php echo esc_url(trx_addons_get_cv_page_link(array('section'=>TRX_ADDONS_CPT_RESUME_PT, 'tab'=>$type))); ?>" class="trx_addons_cv_breadcrumbs_item"><?php echo esc_html($TRX_ADDONS_STORAGE['cpt_resume_types'][$type]); ?></a>
			<?php
		}
	}
}

// Return link and title for the back link
if ( !function_exists( 'trx_addons_cv_resume_get_back_link' ) ) {
	add_filter('trx_addons_cv_filter_get_back_link', 'trx_addons_cv_resume_get_back_link');
	function trx_addons_cv_resume_get_back_link($link=array()) {
		global $TRX_ADDONS_STORAGE;
		if (get_post_type()==TRX_ADDONS_CPT_RESUME_PT && ($type = get_post_meta(get_the_ID(), 'trx_addons_options_resume_type', true))!='' && !empty($TRX_ADDONS_STORAGE['cpt_resume_types'][$type])) {
			$link['link'] = trx_addons_get_cv_page_link(array('section'=>TRX_ADDONS_CPT_RESUME_PT, 'tab'=>$type));
			$link['title'] = $TRX_ADDONS_STORAGE['cpt_resume_types'][$type];
		}
		return $link;
	}
}
?>