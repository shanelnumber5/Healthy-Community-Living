<?php
/**
 * The template 'Style 1' to displaying related posts
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_link = get_permalink();
$academee_post_format = get_post_format();
$academee_post_format = empty($academee_post_format) ? 'standard' : str_replace('post-format-', '', $academee_post_format);
?><div id="post-<?php the_ID(); ?>" 
	<?php post_class( 'related_item related_item_style_1 post_format_'.esc_attr($academee_post_format) ); ?>><?php
	academee_show_post_featured(array(
		'thumb_size' => academee_get_thumb_size( (int) academee_get_theme_option('related_posts') == 1 ? 'huge' : 'big' ),
		'show_no_image' => false,
		'singular' => false,
		'post_info' => '<div class="post_header entry-header">'
							. '<div class="post_categories">' . academee_get_post_categories('') . '</div>'
							. '<h6 class="post_title entry-title"><a href="' . esc_url($academee_link) . '">' . wp_kses_post( get_the_title() ) . '</a></h6>'
							. (in_array(get_post_type(), array('post', 'attachment'))
									? '<span class="post_date"><a href="' . esc_url($academee_link) . '">' . academee_get_date() . '</a></span>'
									: '')
						. '</div>'
		)
	);
?></div>