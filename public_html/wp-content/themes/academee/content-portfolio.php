<?php
/**
 * The Portfolio template to display the content
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

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_portfolio post_layout_portfolio_'.esc_attr($academee_columns).' post_format_'.esc_attr($academee_post_format).(is_sticky() && !is_paged() ? ' sticky' : '') ); ?>
	<?php echo (!academee_is_off($academee_animation) ? ' data-animation="'.esc_attr(academee_get_animation_classes($academee_animation)).'"' : ''); ?>>
	<?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	$academee_image_hover = academee_get_theme_option('image_hover');
	// Featured image
	academee_show_post_featured(array(
		'thumb_size' => academee_get_thumb_size(strpos(academee_get_theme_option('body_style'), 'full')!==false || $academee_columns < 3 ? 'masonry-big' : 'masonry'),
		'show_no_image' => true,
		'class' => $academee_image_hover == 'dots' ? 'hover_with_info' : '',
		'post_info' => $academee_image_hover == 'dots' ? '<div class="post_info">'.esc_html(get_the_title()).'</div>' : ''
	));
	?>
</article>