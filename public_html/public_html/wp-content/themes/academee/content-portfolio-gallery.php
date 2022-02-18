<?php
/**
 * The Gallery template to display posts
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_blog_style = explode('_', academee_get_theme_option('blog_style'));
$academee_columns = empty($academee_blog_style[1]) ? 2 : max(2, $academee_blog_style[1]);
$academee_post_format = get_post_format();
$academee_post_format = empty($academee_post_format) ? 'standard' : str_replace('post-format-', '', $academee_post_format);
$academee_animation = academee_get_theme_option('blog_animation');
$academee_image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full' );

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_portfolio post_layout_gallery post_layout_gallery_'.esc_attr($academee_columns).' post_format_'.esc_attr($academee_post_format) ); ?>
	<?php echo (!academee_is_off($academee_animation) ? ' data-animation="'.esc_attr(academee_get_animation_classes($academee_animation)).'"' : ''); ?>
	data-size="<?php if (!empty($academee_image[1]) && !empty($academee_image[2])) echo intval($academee_image[1]) .'x' . intval($academee_image[2]); ?>"
	data-src="<?php if (!empty($academee_image[0])) echo esc_url($academee_image[0]); ?>"
	>

	<?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	$academee_image_hover = 'icon';	
	if (in_array($academee_image_hover, array('icons', 'zoom'))) $academee_image_hover = 'dots';
	academee_show_post_featured(array(
		'hover' => $academee_image_hover,
		'thumb_size' => academee_get_thumb_size( strpos(academee_get_theme_option('body_style'), 'full')!==false || $academee_columns < 3 ? 'masonry-big' : 'masonry' ),
		'thumb_only' => true,
		'show_no_image' => true,
		'post_info' => '<div class="post_details">'
							. '<h2 class="post_title"><a href="'.esc_url(get_permalink()).'">'. esc_html(get_the_title()) . '</a></h2>'
							. '<div class="post_description">'
								. academee_show_post_meta(apply_filters('academee_filter_post_meta_args', array(
									'components' => 'categories,date,counters,share',
									'counters' => 'comments',
									'seo' => false,
									'echo' => false
									), $academee_blog_style[0], $academee_columns))
								. '<div class="post_description_content">'
									. apply_filters('the_excerpt', get_the_excerpt())
								. '</div>'
								. '<a href="'.esc_url(get_permalink()).'" class="theme_button post_readmore"><span class="post_readmore_label">' . esc_html__('Learn more', 'academee') . '</span></a>'
							. '</div>'
						. '</div>'
	));
	?>
</article>