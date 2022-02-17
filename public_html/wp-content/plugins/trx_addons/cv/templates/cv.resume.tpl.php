<?php
/**
 * CV Card Templates: Resume
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
		
if (trx_addons_is_on(trx_addons_get_option('debug_mode')))
	wp_enqueue_script( 'trx_addons-sc_skills', trx_addons_get_file_url('shortcodes/skills/skills.js'), array('jquery'), null, true );
?>
<div class="trx_addons_cv_section trx_addons_cv_section_resume<?php if ($trx_addons_cv_ajax_loader) echo ' trx_addons_cv_section_ajax'; ?>" data-section="<?php echo esc_attr(trx_addons_cpt_param('resume', 'post_type_slug')); ?>">
	<h5 class="trx_addons_cv_section_title" <?php if (trx_addons_get_value_gp('section')==trx_addons_cpt_param('resume', 'post_type_slug')) echo ' data-active="true"'; ?>><?php 
		echo ($trx_addons_cv_section_title=trx_addons_get_option('cv_resume_title'))!='' ? esc_html($trx_addons_cv_section_title) : esc_html__('Resume', 'trx_addons');
		// Link to print
		?><a class="trx_addons_cv_section_title_icon trx_addons_cv_section_title_icon_print" href="<?php echo esc_url(trx_addons_add_to_url(home_url(), array('cv_prn'=>1))); ?>" target="_blank" title="<?php esc_html_e('Print Resume', 'trx_addons'); ?>"></a><?php
		// Link to download
		$download = trx_addons_get_option('cv_resume_download_version');
		?><a class="trx_addons_cv_section_title_icon trx_addons_cv_section_title_icon_download" target="_blank" 
				download="<?php echo esc_attr(empty($download) ? 'resume.html' : basename($download)); ?>" 
				href="<?php echo esc_url(empty($download) ? trx_addons_add_to_url(home_url(), array('cv_prn'=>1, 'cv_download'=>1)) : $download); ?>" 
				title="<?php esc_html_e('Download Resume', 'trx_addons'); ?>"></a><?php
	?></h5>
	<div class="trx_addons_cv_section_content">
		<?php
		$content = $content_start = $content_end = '';
		$section_content = true;
		$cv_content_mask = '%%CONTENT%%';
		if (($trx_addons_cv_section_page_id = trx_addons_get_option('cv_resume_page')) > 0) {
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'trx_addons_cv_resume_page itemscope'); ?> itemscope itemtype="http://schema.org/Article">
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
				$trx_addons_cv_types = $TRX_ADDONS_STORAGE['cpt_resume_types'];
				$trx_addons_cv_id = 'trx_addons_cv_resume_tabs_'.str_replace('.', '', mt_rand());
				$trx_addons_cv_tabs = array();
				$trx_addons_cv_active_tab = '';
				?>
				<ul class="trx_addons_tabs_titles">
					<?php
					$trx_addons_cv_resume_parts = trx_addons_get_option('cv_resume_parts');
					if (is_array($trx_addons_cv_resume_parts) && count($trx_addons_cv_resume_parts) > 0) {
						foreach ($trx_addons_cv_resume_parts as $trx_addons_cv_type => $trx_addons_cv_type_enable) {
							if ( (int)$trx_addons_cv_type_enable == 0 || empty($trx_addons_cv_types[$trx_addons_cv_type]) ) continue;
							$trx_addons_cv_id_tab = $trx_addons_cv_id.'_'.$trx_addons_cv_type;
							$trx_addons_cv_active = trx_addons_get_value_gp('section')==trx_addons_cpt_param('resume', 'post_type_slug') && trx_addons_get_value_gp('tab')==$trx_addons_cv_type 
										? ' data-active="true"' 
										: '';
							if ($trx_addons_cv_active) $trx_addons_cv_active_tab = $trx_addons_cv_type;
							$trx_addons_cv_tabs[] = $trx_addons_cv_type;
							?>
							<li<?php if ($trx_addons_cv_active) echo ' '.trim($trx_addons_cv_active); ?>><a href="<?php echo esc_url(trx_addons_get_hash_link('#'.$trx_addons_cv_id_tab.'_content')); ?>" data-tab="<?php echo esc_attr($trx_addons_cv_type); ?>"><?php echo esc_html($trx_addons_cv_types[$trx_addons_cv_type]); ?></a></li>
							<?php
						}
					}
					?>
				</ul>
				<?php
				foreach ($trx_addons_cv_tabs as $trx_addons_cv_slug) {
					$trx_addons_cv_id_tab = $trx_addons_cv_id.'_'.$trx_addons_cv_slug;
					if (empty($trx_addons_cv_active_tab)) $trx_addons_cv_active_tab = $trx_addons_cv_slug;
					$trx_addons_need_content = $trx_addons_cv_active_tab!=$trx_addons_cv_slug && $trx_addons_cv_ajax_loader;
					?>
					<div id="<?php echo esc_attr($trx_addons_cv_id_tab); ?>_content"
						class="trx_addons_tabs_content trx_addons_tabs_content_<?php echo esc_attr($trx_addons_cv_slug); ?><?php
							if (trx_addons_is_on(trx_addons_get_option('cv_resume_narrow_'.$trx_addons_cv_slug))) echo ' trx_addons_tabs_content_narrow'; 
							if (trx_addons_is_on(trx_addons_get_option('cv_resume_delimiter_'.$trx_addons_cv_slug))) echo ' trx_addons_tabs_content_delimiter'; 
						?>"
						data-tab="<?php echo esc_attr($trx_addons_cv_slug); ?>"
						data-need-content="<?php echo empty($trx_addons_need_content) ? 'false' : 'true'; ?>"
					>
					<?php
					if ($trx_addons_cv_active_tab==$trx_addons_cv_slug || !$trx_addons_cv_ajax_loader) 
							trx_addons_cv_resume_show_posts($trx_addons_cv_slug, $trx_addons_cv_active_tab==$trx_addons_cv_slug ? trx_addons_get_current_page() : 1); ?>
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