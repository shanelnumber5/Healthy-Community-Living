<?php
/**
 * The template for homepage posts with "Portfolio" style
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

academee_storage_set('blog_archive', true);

// Load scripts for both 'Gallery' and 'Portfolio' layouts!
wp_enqueue_script( 'imagesloaded' );
wp_enqueue_script( 'masonry' );
wp_enqueue_script( 'classie', academee_get_file_url('js/theme.gallery/classie.min.js'), array(), null, true );
wp_enqueue_script( 'academee-gallery-script', academee_get_file_url('js/theme.gallery/theme.gallery.js'), array(), null, true );

get_header(); 

if (have_posts()) {

	echo get_query_var('blog_archive_start');

	$academee_stickies = is_home() ? get_option( 'sticky_posts' ) : false;
	$academee_sticky_out = academee_get_theme_option('sticky_style')=='columns' 
							&& is_array($academee_stickies) && count($academee_stickies) > 0 && get_query_var( 'paged' ) < 1;
	
	// Show filters
	$academee_cat = academee_get_theme_option('parent_cat');
	$academee_post_type = academee_get_theme_option('post_type');
	$academee_taxonomy = academee_get_post_type_taxonomy($academee_post_type);
	$academee_show_filters = academee_get_theme_option('show_filters');
	$academee_tabs = array();
	if (!academee_is_off($academee_show_filters)) {
		$academee_args = array(
			'type'			=> $academee_post_type,
			'child_of'		=> $academee_cat,
			'orderby'		=> 'name',
			'order'			=> 'ASC',
			'hide_empty'	=> 1,
			'hierarchical'	=> 0,
			'exclude'		=> '',
			'include'		=> '',
			'number'		=> '',
			'taxonomy'		=> $academee_taxonomy,
			'pad_counts'	=> false
		);
		$academee_portfolio_list = get_terms($academee_args);
		if (is_array($academee_portfolio_list) && count($academee_portfolio_list) > 0) {
			$academee_tabs[$academee_cat] = esc_html__('All', 'academee');
			foreach ($academee_portfolio_list as $academee_term) {
				if (isset($academee_term->term_id)) $academee_tabs[$academee_term->term_id] = $academee_term->name;
			}
		}
	}
	if (count($academee_tabs) > 0) {
		$academee_portfolio_filters_ajax = true;
		$academee_portfolio_filters_active = $academee_cat;
		$academee_portfolio_filters_id = 'portfolio_filters';
		if (!is_customize_preview())
			wp_enqueue_script('jquery-ui-tabs', false, array('jquery', 'jquery-ui-core'), null, true);
		?>
		<div class="portfolio_filters academee_tabs academee_tabs_ajax">
			<ul class="portfolio_titles academee_tabs_titles">
				<?php
				foreach ($academee_tabs as $academee_id=>$academee_title) {
					?><li><a href="<?php echo esc_url(academee_get_hash_link(sprintf('#%s_%s_content', $academee_portfolio_filters_id, $academee_id))); ?>" data-tab="<?php echo esc_attr($academee_id); ?>"><?php echo esc_html($academee_title); ?></a></li><?php
				}
				?>
			</ul>
			<?php
			$academee_ppp = academee_get_theme_option('posts_per_page');
			if (academee_is_inherit($academee_ppp)) $academee_ppp = '';
			foreach ($academee_tabs as $academee_id=>$academee_title) {
				$academee_portfolio_need_content = $academee_id==$academee_portfolio_filters_active || !$academee_portfolio_filters_ajax;
				?>
				<div id="<?php echo esc_attr(sprintf('%s_%s_content', $academee_portfolio_filters_id, $academee_id)); ?>"
					class="portfolio_content academee_tabs_content"
					data-blog-template="<?php echo esc_attr(academee_storage_get('blog_template')); ?>"
					data-blog-style="<?php echo esc_attr(academee_get_theme_option('blog_style')); ?>"
					data-posts-per-page="<?php echo esc_attr($academee_ppp); ?>"
					data-post-type="<?php echo esc_attr($academee_post_type); ?>"
					data-taxonomy="<?php echo esc_attr($academee_taxonomy); ?>"
					data-cat="<?php echo esc_attr($academee_id); ?>"
					data-parent-cat="<?php echo esc_attr($academee_cat); ?>"
					data-need-content="<?php echo (false===$academee_portfolio_need_content ? 'true' : 'false'); ?>"
				>
					<?php
					if ($academee_portfolio_need_content) 
						academee_show_portfolio_posts(array(
							'cat' => $academee_id,
							'parent_cat' => $academee_cat,
							'taxonomy' => $academee_taxonomy,
							'post_type' => $academee_post_type,
							'page' => 1,
							'sticky' => $academee_sticky_out
							)
						);
					?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	} else {
		academee_show_portfolio_posts(array(
			'cat' => $academee_cat,
			'parent_cat' => $academee_cat,
			'taxonomy' => $academee_taxonomy,
			'post_type' => $academee_post_type,
			'page' => 1,
			'sticky' => $academee_sticky_out
			)
		);
	}

	echo get_query_var('blog_archive_end');

} else {

	if ( is_search() )
		get_template_part( 'content', 'none-search' );
	else
		get_template_part( 'content', 'none-archive' );

}

get_footer();
?>