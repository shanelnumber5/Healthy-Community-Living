<?php
/**
 * CV Card Templates: Portfolio
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}
$trx_addons_cv_ajax_loader = trx_addons_is_on(trx_addons_get_option('cv_ajax_loader')) && !is_customize_preview();
?>
<div class="trx_addons_cv_section trx_addons_cv_section_portfolio<?php if ($trx_addons_cv_ajax_loader) echo ' trx_addons_cv_section_ajax'; ?>" data-section="<?php echo esc_attr(trx_addons_cpt_param('portfolio', 'post_type_slug')); ?>">
	<h5 class="trx_addons_cv_section_title" <?php if (trx_addons_get_value_gp('section')==trx_addons_cpt_param('portfolio', 'post_type_slug')) echo ' data-active="true"'; ?>><?php echo ($trx_addons_cv_section_title=trx_addons_get_option('cv_portfolio_title'))!='' ? esc_html($trx_addons_cv_section_title) : esc_html__('Portfolio', 'trx_addons'); ?></h5>
	<div class="trx_addons_cv_section_content">
		<?php
		$content = $content_start = $content_end = '';
		$section_content = true;
		$cv_content_mask = '%%CONTENT%%';
		if (($trx_addons_cv_section_page_id = trx_addons_get_option('cv_portfolio_page')) > 0) {
			$trx_addons_cv_sectione_page = get_post($trx_addons_cv_section_page_id);
			if ($trx_addons_cv_sectione_page instanceof WP_Post) {
				global $post;
				$post = $trx_addons_cv_sectione_page;
				setup_postdata($post);
				if (($content = apply_filters('the_content', get_the_content())) != '') {
					if (strpos($content, $cv_content_mask)!==false) {
						$content = explode($cv_content_mask, $content);
						$content_start = $content[0];
						$content_end = !empty($content[1]) ? $content[1] : '';
					} else
						$section_content = false;
				}
			}
		}
		
		// If need content
		if (!empty($content)) {
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'trx_addons_cv_portfolio_page itemscope'); ?> itemscope itemtype="http://schema.org/Article">
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
					trx_addons_show_layout($content_start);
		}

		if ($section_content) {
			?>
			<div class="trx_addons_tabs">
				<?php
				$trx_addons_cv_terms = get_terms(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY);
				$trx_addons_cv_id = 'trx_addons_cv_portfolio_tabs_'.str_replace('.', '', mt_rand());
				$trx_addons_cv_id_tab = $trx_addons_cv_id.'_all';
				$trx_addons_cv_tabs = array('all');
				$trx_addons_cv_active_tab = 'all';
				?>
				<ul class="trx_addons_tabs_titles">
					<li><a href="<?php echo esc_url(trx_addons_get_hash_link('#'.$trx_addons_cv_id_tab.'_content')); ?>" data-tab="all"><?php esc_html_e('All', 'trx_addons'); ?></a></li>
					<?php
					if (is_array($trx_addons_cv_terms) && count($trx_addons_cv_terms) > 0) {
						foreach ($trx_addons_cv_terms as $trx_addons_cv_term) {
							$trx_addons_cv_id_tab = $trx_addons_cv_id.'_'.$trx_addons_cv_term->slug;
							$trx_addons_cv_active = trx_addons_get_value_gp('section')==trx_addons_cpt_param('portfolio', 'post_type_slug') && trx_addons_get_value_gp('tab')==$trx_addons_cv_term->slug 
										? ' data-active="true"' 
										: '';
							if ($trx_addons_cv_active) $trx_addons_cv_active_tab = $trx_addons_cv_term->slug;
							$trx_addons_cv_tabs[] = $trx_addons_cv_term->slug;
							?>
							<li<?php if ($trx_addons_cv_active) echo ' '.trim($trx_addons_cv_active); ?>><a href="<?php echo esc_url(trx_addons_get_hash_link('#'.$trx_addons_cv_id_tab.'_content')); ?>" data-tab="<?php echo esc_attr($trx_addons_cv_term->slug); ?>"><?php echo esc_html($trx_addons_cv_term->name); ?></a></li>
							<?php
						}
					}
					?>
				</ul>
				<?php
				foreach ($trx_addons_cv_tabs as $trx_addons_cv_slug) {
					$trx_addons_cv_id_tab = $trx_addons_cv_id.'_'.$trx_addons_cv_slug;
					$trx_addons_need_content = $trx_addons_cv_active_tab!=$trx_addons_cv_slug && $trx_addons_cv_ajax_loader;
					?>
					<div id="<?php echo esc_attr($trx_addons_cv_id_tab); ?>_content"
						class="trx_addons_tabs_content"
						data-tab="<?php echo esc_attr($trx_addons_cv_slug); ?>"
						data-need-content="<?php echo empty($trx_addons_need_content) ? 'false' : 'true'; ?>"
					>
					<?php
					if ($trx_addons_cv_active_tab==$trx_addons_cv_slug || !$trx_addons_cv_ajax_loader) 
						trx_addons_cv_portfolio_show_posts($trx_addons_cv_slug, $trx_addons_cv_active_tab==$trx_addons_cv_slug ? trx_addons_get_current_page() : 1);
					?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}

		if (!empty($content)) {
					trx_addons_show_layout($content_end);
					
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
			<?php
		}
		?>
	</div>
</div>