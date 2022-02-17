<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_blog_style = explode('_', academee_get_theme_option('blog_style'));
$academee_columns = empty($academee_blog_style[1]) ? 2 : max(2, $academee_blog_style[1]);
$academee_expanded = !academee_sidebar_present() && academee_is_on(academee_get_theme_option('expand_content'));
$academee_post_format = get_post_format();
$academee_post_format = empty($academee_post_format) ? 'standard' : str_replace('post-format-', '', $academee_post_format);
$academee_animation = academee_get_theme_option('blog_animation');

?><div class="<?php echo trim($academee_blog_style[0]) == 'classic' ? 'column' : 'masonry_item masonry_item'; ?>-1_<?php echo esc_attr($academee_columns); ?>"><article id="post-<?php the_ID(); ?>"
	<?php post_class( 'post_item post_format_'.esc_attr($academee_post_format)
					. ' post_layout_classic post_layout_classic_'.esc_attr($academee_columns)
					. ' post_layout_'.esc_attr($academee_blog_style[0]) 
					. ' post_layout_'.esc_attr($academee_blog_style[0]).'_'.esc_attr($academee_columns)
					); ?>
	<?php echo (!academee_is_off($academee_animation) ? ' data-animation="'.esc_attr(academee_get_animation_classes($academee_animation)).'"' : ''); ?>>
	<?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	academee_show_post_featured( array( 'thumb_size' => academee_get_thumb_size($academee_blog_style[0] == 'classic'
													? (strpos(academee_get_theme_option('body_style'), 'full')!==false 
															? ( $academee_columns > 2 ? 'big' : 'huge' )
															: (	$academee_columns > 2
																? ($academee_expanded ? 'med' : 'small')
																: ($academee_expanded ? 'big' : 'med')
																)
														)
													: (strpos(academee_get_theme_option('body_style'), 'full')!==false 
															? ( $academee_columns > 2 ? 'masonry-big' : 'full' )
															: (	$academee_columns <= 2 && $academee_expanded ? 'masonry-big' : 'masonry')
														)
								) ) );

	if ( !in_array($academee_post_format, array('link', 'aside', 'status', 'quote')) ) {
		?>
		<div class="post_header entry-header">
			<?php 
			do_action('academee_action_before_post_title'); 

			// Post title
			the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );

			do_action('academee_action_before_post_meta'); 

			// Post meta
			academee_show_post_meta(apply_filters('academee_filter_post_meta_args', array(
				'components' => 'categories,date,counters'.($academee_columns < 3 ? ',edit' : ''),
				'counters' => 'comments',
				'seo' => false
				), $academee_blog_style[0], $academee_columns)
			);

			do_action('academee_action_after_post_meta'); 
			?>
		</div><!-- .entry-header -->
		<?php
	}		
	?>

	<div class="post_content entry-content">
		<div class="post_content_inner">
			<?php
			$academee_show_learn_more = false; 
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
			?>
		</div>
		<?php
		// Post meta
		if (in_array($academee_post_format, array('link', 'aside', 'status', 'quote'))) {
			academee_show_post_meta(apply_filters('academee_filter_post_meta_args', array(
				'components' => 'categories,date,counters'.($academee_columns < 3 ? ',edit' : ''),
				'counters' => 'comments'
				), $academee_blog_style[0], $academee_columns)
			);
		}
		// More button
		if ( $academee_show_learn_more ) {
			?><p><a class="more-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more', 'academee'); ?></a></p><?php
		}
		?>
	</div><!-- .entry-content -->

</article></div>