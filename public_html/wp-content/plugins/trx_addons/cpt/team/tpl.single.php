<?php
/**
 * The template to display the team member's page
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

global $TRX_ADDONS_STORAGE;

get_header();

while ( have_posts() ) { the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'team_member_page itemscope' ); ?>
		itemscope itemtype="http://schema.org/Article">
		
		<section class="team_member_header">	

			<?php
			$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);

			// Image
			if ( !trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ) {
				?><div class="team_member_featured">
					<div class="team_member_avatar">
						<?php
						the_post_thumbnail( trx_addons_get_thumb_size('big-avatar'), array(
									'alt' => get_the_title(),
									'itemprop' => 'image'
									)
								);
						?>
					</div>

				</div>
				<?php
			}
			
			// Title and Description
			?><div class="team_member_description"><?php
				if ( !trx_addons_sc_layouts_showed('title') ) {
					?><h2 class="team_member_title"><?php the_title(); ?></h2><?php
				}
				?>
				<h6 class="team_member_position"><?php echo esc_html($meta['subtitle']); ?></h6>
				<div class="team_member_details">
					<?php
					$meta_box = apply_filters('trx_addons_filter_override_options_fields', $TRX_ADDONS_STORAGE['meta_box_'.get_post_type()], get_post_type());
					foreach ($meta_box as $k=>$v) {
						if (!empty($v['details']) && !empty($meta[$k])) {
							?><div class="team_member_details_<?php echo esc_attr($k); ?>"><span class="team_member_details_label"><?php echo esc_html($v['title']); ?>: </span><span class="team_member_details_value"><a href="mailto:<?php echo antispambot($meta[$k]); ?>"><?php echo esc_html($meta[$k]); ?></a></span></div><?php
						}
					}
					?>
				</div>
				<?php
				if (!empty($meta['brief_info'])) {
					?>
					<div class="team_member_brief_info">

						<div class="team_member_brief_info_text"><?php echo wpautop($meta['brief_info']); ?></div>
					</div>
					<?php
				}
				?>
                <?php
                if (!empty($meta['socials'])) {
                    ?><div class="team_member_socials"><?php trx_addons_show_layout(trx_addons_get_socials_links_custom($meta['socials'])); ?></div><?php
                }
                ?>
			</div>

		</section>
		<?php

		// Post content
		?><section class="team_member_content entry-content" itemprop="articleBody"><?php
			the_content( );
		?></section><!-- .entry-content --><?php

	?></article><?php

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
?>