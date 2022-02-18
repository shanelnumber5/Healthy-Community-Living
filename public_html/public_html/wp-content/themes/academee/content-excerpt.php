<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_post_format = get_post_format();
$academee_post_format = empty($academee_post_format) ? 'standard' : str_replace('post-format-', '', $academee_post_format);
$academee_animation = academee_get_theme_option('blog_animation');

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_excerpt post_format_'.esc_attr($academee_post_format) ); ?>
	<?php echo (!academee_is_off($academee_animation) ? ' data-animation="'.esc_attr(academee_get_animation_classes($academee_animation)).'"' : ''); ?>
	><?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	academee_show_post_featured(array( 'thumb_size' => academee_get_thumb_size( strpos(academee_get_theme_option('body_style'), 'full')!==false ? 'full' : 'big' ) ));

	// Title and post meta
	if (get_the_title() != '') {
		?>
		<div class="post_header entry-header">
			<?php
			do_action('academee_action_before_post_title'); 

			// Post title
			the_title( sprintf( '<h2 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );

			do_action('academee_action_before_post_meta'); 

			// Post meta
			academee_show_post_meta(apply_filters('academee_filter_post_meta_args', array(
				'components' => 'categories,date,counters,edit',
				'counters' => 'comments',
				'seo' => false
				), 'excerpt', 1)
			);
			?>
		</div><!-- .post_header --><?php
	}
	
	// Post content
	?><div class="post_content entry-content"><?php
		if (academee_get_theme_option('blog_content') == 'fullpost') {
			// Post content area
			?><div class="post_content_inner"><?php
				the_content( '' );
			?></div><?php
			// Inner pages
			wp_link_pages( array(
				'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'academee' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'academee' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

		} else {

			$academee_show_learn_more = !in_array($academee_post_format, array('link', 'aside', 'status', 'quote'));

			// Post content area
			?><div class="post_content_inner"><?php
				if (has_excerpt()) {
					the_excerpt();
				} else if (strpos(get_the_content('!--more'), '!--more')!==false) {
					the_content( '' );
				} else if (in_array($academee_post_format, array('link', 'aside', 'status'))) {
					the_content();
				} else if ($academee_post_format == 'quote') {
					if (($quote = academee_get_tag(get_the_content(), '<blockquote>', '</blockquote>'))!='')
						academee_show_layout(wpautop($quote));
					else
						the_excerpt();
				} else if (substr(get_the_content(), 0, 1)!='[') {
					the_excerpt();
				}
			?></div><?php
			// More button
			if ( $academee_show_learn_more ) {
				?><p><a class="more-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more', 'academee'); ?></a></p><?php
			}

		}
	?></div><!-- .entry-content -->
</article>