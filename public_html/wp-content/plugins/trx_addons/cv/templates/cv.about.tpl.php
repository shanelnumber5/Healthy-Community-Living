<?php
/**
 * CV Card Templates: Template for the About page
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

if (($trx_addons_cv_about_page_id = trx_addons_get_option('cv_about_page')) > 0) {
	$trx_addons_cv_about_page = get_post($trx_addons_cv_about_page_id);
	if ($trx_addons_cv_about_page instanceof WP_Post) {
		global $post;
		$post = $trx_addons_cv_about_page;
		setup_postdata($post);
		?>
		<div class="trx_addons_cv_section trx_addons_cv_section_about" data-section="about">
			<h5 class="trx_addons_cv_section_title"<?php if (trx_addons_get_value_gp('section')=='about') echo ' data-active="true"'; ?>><?php echo ($trx_addons_cv_section_title=trx_addons_get_option('cv_about_title'))!='' ? esc_html($trx_addons_cv_section_title) : esc_html__('About', 'trx_addons'); ?></h5>
			<div class="trx_addons_cv_section_content">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'trx_addons_cv_about_page itemscope'); ?> itemscope itemtype="http://schema.org/Article">
					<?php
					if (has_post_thumbnail()) {
						?>
						<div class="trx_addons_cv_single_thumb">
							<?php the_post_thumbnail( trx_addons_get_thumb_size('full'), array('alt' => get_the_title()) ); ?>
						</div>
						<?php
					}
					?>
					<div class="trx_addons_cv_single_header entry-header">
						<h1 class="trx_addons_cv_single_title entry-title" itemprop="headline"><?php the_title(); ?></h1>
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
				</article><!-- .trx_addons_cv_single -->
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}
}
?>