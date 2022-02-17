<?php
/**
 * CV Card: Portfolio
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Return true if current screen is a portfolio post
if ( !function_exists( 'trx_addons_cv_is_portfolio_page' ) ) {
	add_filter('trx_addons_filter_is_cv_page', 'trx_addons_cv_is_portfolio_page');
	function trx_addons_cv_is_portfolio_page($cv = false) {
		global $post;
		return $cv || (is_single() && $post->post_type==TRX_ADDONS_CPT_PORTFOLIO_PT);
	}
}


// Return taxonomy slug by custom post_type
if (!function_exists('trx_addons_cv_portfolio_get_taxonomy')) {
	add_filter('trx_addons_filter_cv_get_taxonomy', 'trx_addons_cv_portfolio_get_taxonomy', 10, 2);
	function trx_addons_cv_portfolio_get_taxonomy($taxonomy='', $post_type='') {
		if ($post_type == TRX_ADDONS_CPT_PORTFOLIO_PT)
			$taxonomy = TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY;
		return $taxonomy;
	}
}



// -----------------------------------------------------------------
// -- Load scripts and styles
// -----------------------------------------------------------------

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cv_portfolio_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cv_portfolio_load_scripts_front');
	function trx_addons_cv_portfolio_load_scripts_front() {
		if (trx_addons_get_value_gp('cv_prn')=='' && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			if (trx_addons_is_cv_page()) {
				wp_enqueue_style( 'trx_addons-cv.portfolio', trx_addons_get_file_url('cv/css/cv.portfolio.css'), array(), null );
			}
		}
	}
}

	
// Merge CV specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cv_portfolio_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_cv_portfolio_merge_styles');
	function trx_addons_cv_portfolio_merge_styles($list) {
		$list[] = 'cv/css/cv.portfolio.css';
		return $list;
	}
}



// -----------------------------------------------------------------
// -- Posts utilities
// -----------------------------------------------------------------

// Display portfolio posts from specified category
if (!function_exists('trx_addons_cv_portfolio_show_posts')) {
	function trx_addons_cv_portfolio_show_posts($slug, $page=1) {
		$page  = max(1, (int) $page);
		$style = max(1, (int) trx_addons_get_option('cv_portfolio_style'));
		$count = max(1, (int) trx_addons_get_option('cv_portfolio_count'));
		$args = array(
			'post_type' => TRX_ADDONS_CPT_PORTFOLIO_PT,
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'offset' => max(0, ($page-1)*$count),
			'ignore_sticky_posts' => true,
			'orderby' => 'date',
			'order' => 'desc'
		);
		if ($slug != 'all') {
			$args['tax_query'] = array();
			$args['tax_query']['relation'] = 'AND';
			$args['tax_query'][] = array(
				'taxonomy' => TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY,
				'include_children' => true,
				'field'    => 'slug',
				'terms'    => array($slug)
			);
		}
		$query = new WP_Query( $args );
		if ($query->found_posts > 0) {
			if ($count > $query->found_posts) $count = $query->found_posts;
			$columns = max(1, min(12, (int) trx_addons_get_option('cv_portfolio_columns')));
			//if ($columns > $count) $columns = $count;

			$masonry = $style==3 && $columns > 1;

			$slider = trx_addons_is_on(trx_addons_get_option('cv_portfolio_slider')) && $count > $columns && !$masonry;
			$slides_space = max(0, (int) trx_addons_get_option('cv_portfolio_slides_space'));
			?>
			<div class="trx_addons_cv_portfolio trx_addons_cv_portfolio_style_<?php echo esc_attr($style); ?><?php if ($slider) echo ' swiper-slider-container slider_swiper slider_noresize slider_controls slider_controls_bottom'; ?>"
					<?php
					echo ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
						. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
						. ' data-slides-min-width="150"';
					?>
					>
				<?php
				if ($slider) {
					?><div class="slides swiper-wrapper"><?php
				} else if ($columns > 1) {
					?><div class="trx_addons_cv_portfolio_columns <?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?> columns_padding_bottom"><?php
				}	
				
				if ($masonry) {
					$column_number = 0;
					$columns_content = array();
				}
				while ( $query->have_posts() ) { 
					$query->the_post();
					if ($masonry) {
						ob_start();
					}
					trx_addons_get_template_part('cv/templates/cv.portfolio.style-'.trx_addons_esc($style).'.tpl.php',
												'trx_addons_args_cv_portfolio',
												array(
													'columns' => $columns,
													'slider' => $slider
													)
												);
					if ($masonry) {
						$columns_content[$column_number] = (empty($columns_content[$column_number]) ? '' : $columns_content[$column_number]) . ob_get_contents();
						ob_end_clean();
						$column_number = ($column_number + 1) % $columns;
					}
				}

				if ($masonry) {
					for ($i=0; $i<$columns; $i++) {
						if (!empty($columns_content[$i])) {
							?><div class="trx_addons_cv_portfolio_column <?php echo esc_attr(trx_addons_get_column_class(1, $columns)); ?>"><?php
								trx_addons_show_layout($columns_content[$i]);
							?></div><?php
						}
					}
				}
	
				wp_reset_postdata();
		
				if ($slider) {
					?>
					</div>
					<div class="slider_controls_wrap"><a class="slider_prev swiper-button-prev" href="#"></a><a class="slider_next swiper-button-next" href="#"></a></div>
					<?php
				} else if ($columns > 1) {
					?></div><?php
				}
				?>
			</div><!-- /.trx_addons_cv_portfolio -->
			<?php
			trx_addons_pagination(array(
				'total_posts' => $query->found_posts,
				'posts_per_page' => $count,
				'cur_page' => $page,
				'base_link' => trx_addons_get_cv_page_link(array('section'=>TRX_ADDONS_CPT_PORTFOLIO_PT, 'tab'=>$slug))
			));
		}
	}
}

// AJAX handler for the trx_addons_ajax_get_posts action
if ( !function_exists( 'trx_addons_cv_portfolio_ajax_get_posts' ) ) {
	add_filter('trx_addons_cv_filter_ajax_get_posts', 'trx_addons_cv_portfolio_ajax_get_posts');
	function trx_addons_cv_portfolio_ajax_get_posts($response) {
		
		$section = $_REQUEST['section'];
		$tab = $_REQUEST['tab'];
		$page = $_REQUEST['page'];
	
		if ($section == trx_addons_cpt_param('portfolio', 'post_type_slug')) {
			if (!empty($tab) && $page > 0) {
				ob_start();
				trx_addons_cv_portfolio_show_posts($tab, $page);
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
?>