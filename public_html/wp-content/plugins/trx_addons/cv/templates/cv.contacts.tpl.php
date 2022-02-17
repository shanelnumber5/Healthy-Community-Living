<?php
/**
 * CV Card Templates: Contacts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}
?>
<div class="trx_addons_cv_section trx_addons_cv_section_contacts" data-section="contacts">
	<h5 class="trx_addons_cv_section_title"<?php if (trx_addons_get_value_gp('section')=='contacts') echo ' data-active="true"'; ?>><?php echo ($trx_addons_cv_section_title=trx_addons_get_option('cv_contacts_title'))!='' ? esc_html($trx_addons_cv_section_title) : esc_html__('Contacts', 'trx_addons'); ?></h5>
	<div class="trx_addons_cv_section_content">
		<?php
		$content = $content_start = $content_end = '';
		$section_content = true;
		$cv_content_mask = '%%CONTENT%%';
		if (($trx_addons_cv_section_page_id = trx_addons_get_option('cv_contacts_page')) > 0) {
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'trx_addons_cv_contacts_page itemscope'); ?> itemscope itemtype="http://schema.org/Article">
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
			if (function_exists('trx_addons_sc_form')) {
				echo trx_addons_sc_form(array(
					'type'  => 'detailed',
					'title' => esc_html__('Send me a message', 'trx_addons'),
					'phone' => trx_addons_get_option('contacts_phone'),
					'email' => trx_addons_get_option('contacts_email'),
					'address' => trx_addons_get_option('contacts_address'),
				));
			}
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
