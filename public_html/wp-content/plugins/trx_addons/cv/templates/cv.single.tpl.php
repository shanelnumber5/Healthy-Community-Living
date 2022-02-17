<?php
/**
 * CV Card Templates: Template for the single post (testimonial, portfolio or resume item)
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

while (have_posts() ) { the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'trx_addons_cv_single post_type_'.esc_attr(get_post_type()).' itemscope'); ?> itemscope itemtype="http://schema.org/Article">
		<?php
		$trx_addons_cv_post_type = get_post_type();
		$trx_addons_cv_post_type_obj = get_post_type_object($trx_addons_cv_post_type);
		$trx_addons_cv_last_link = array(
			'link' => trx_addons_get_cv_page_link(array('section'=>trx_addons_cpt_param($trx_addons_cv_post_type, 'post_type_slug'))),
			'title' => $trx_addons_cv_post_type_obj->labels->name
		);
		?>
		<div class="trx_addons_cv_breadcrumbs">
			<a href="<?php echo esc_url(trx_addons_get_cv_page_link()); ?>" class="trx_addons_cv_breadcrumbs_item"><?php esc_html_e('VCard', 'trx_addons'); ?></a>
			<a href="<?php echo esc_url(trx_addons_get_cv_page_link(array('section'=>trx_addons_cpt_param($trx_addons_cv_post_type, 'post_type_slug')))); ?>" class="trx_addons_cv_breadcrumbs_item"><?php echo esc_html($trx_addons_cv_post_type_obj->labels->name); ?></a>
			<?php
			if (($trx_addons_cv_taxonomy = apply_filters('trx_addons_filter_cv_get_taxonomy', '', $trx_addons_cv_post_type)) != '') {
				$trx_addons_cv_terms = get_the_terms(get_the_ID(), $trx_addons_cv_taxonomy);
				if ($trx_addons_cv_terms && !is_wp_error($trx_addons_cv_terms)) { 
					foreach ($trx_addons_cv_terms as $trx_addons_cv_term) {
						$trx_addons_cv_last_link['link'] = trx_addons_get_cv_page_link(array('section'=>trx_addons_cpt_param($trx_addons_cv_post_type, 'post_type_slug'), 'tab'=>$trx_addons_cv_term->slug));
						$trx_addons_cv_last_link['title'] = $trx_addons_cv_term->name;
						?>
						<a href="<?php echo esc_url(trx_addons_get_cv_page_link(array('section'=>trx_addons_cpt_param($trx_addons_cv_post_type, 'post_type_slug'), 'tab'=>$trx_addons_cv_term->slug))); ?>" class="trx_addons_cv_breadcrumbs_item"><?php echo esc_html($trx_addons_cv_term->name); ?></a>
						<?php
					}
				}
			} else {
				do_action('trx_addons_cv_action_show_breadcrumbs_terms');
				$trx_addons_cv_last_link = apply_filters('trx_addons_cv_filter_get_back_link', $trx_addons_cv_last_link);
			}
			?>
			<span class="trx_addons_cv_breadcrumbs_item"><?php echo esc_html(get_the_title()); ?></span>
		</div>
		<?php
		if (has_post_thumbnail()) {
			?>
			<div class="trx_addons_cv_single_thumb">
				<?php the_post_thumbnail( trx_addons_get_thumb_size('full'), array('alt' => get_the_title()) ); ?>
			</div>
			<?php
		}
		$trx_addons_cv_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
		?>
		<div class="trx_addons_cv_single_header entry-header">
			<h1 class="trx_addons_cv_single_title entry-title" itemprop="headline"><?php the_title(); ?></h1>
			<h6 class="trx_addons_cv_single_subtitle"><?php echo esc_html($trx_addons_cv_meta['subtitle']);?></h6>
		</div><!-- .entry-header -->
		<div class="trx_addons_cv_single_content entry-content" itemprop="articleBody">
			<?php
			the_content( );

			wp_link_pages( array(
				'before'      => '<div class="trx_addons_cv_single_page_links"><span class="trx_addons_cv_single_page_links_title">' . esc_html__( 'Pages:', 'trx_addons' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'trx_addons' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );
			?>
		</div><!-- .entry-content -->
		<div class="trx_addons_cv_single_footer entry-footer">
			<a href="<?php echo esc_url($trx_addons_cv_last_link['link']); ?>" class="trx_addons_cv_back_link"><?php echo sprintf(esc_html__('Back to %s', 'trx_addons'), $trx_addons_cv_last_link['title']); ?></a>
		</div><!-- .entry-header -->
	</article><!-- .trx_addons_cv_single -->
	<?php
}
?>