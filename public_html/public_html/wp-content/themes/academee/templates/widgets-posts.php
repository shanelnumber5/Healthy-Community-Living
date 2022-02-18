<?php
/**
 * The template to display posts in widgets and/or in the search results
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_post_id    = get_the_ID();
$academee_post_date  = academee_get_date();
$academee_post_title = get_the_title();
$academee_post_link  = get_permalink();
$academee_post_author_id   = get_the_author_meta('ID');
$academee_post_author_name = get_the_author_meta('display_name');
$academee_post_author_url  = get_author_posts_url($academee_post_author_id, '');

$academee_args = get_query_var('academee_args_widgets_posts');
$academee_show_date = isset($academee_args['show_date']) ? (int) $academee_args['show_date'] : 1;
$academee_show_image = isset($academee_args['show_image']) ? (int) $academee_args['show_image'] : 1;
$academee_show_author = isset($academee_args['show_author']) ? (int) $academee_args['show_author'] : 1;
$academee_show_counters = isset($academee_args['show_counters']) ? (int) $academee_args['show_counters'] : 1;
$academee_show_categories = isset($academee_args['show_categories']) ? (int) $academee_args['show_categories'] : 1;

$academee_output = academee_storage_get('academee_output_widgets_posts');

$academee_post_counters_output = '';
if ( $academee_show_counters ) {
	$academee_post_counters_output = '<span class="post_info_item post_info_counters">'
								. academee_get_post_counters('comments')
							. '</span>';
}


$academee_output .= '<article class="post_item with_thumb">';

if ($academee_show_image) {
	$academee_post_thumb = get_the_post_thumbnail($academee_post_id, academee_get_thumb_size('tiny'), array(
		'alt' => the_title_attribute( array( 'echo' => false ) )
	));
	if ($academee_post_thumb) $academee_output .= '<div class="post_thumb">' . ($academee_post_link ? '<a href="' . esc_url($academee_post_link) . '">' : '') . ($academee_post_thumb) . ($academee_post_link ? '</a>' : '') . '</div>';
}

$academee_output .= '<div class="post_content">'
			. ($academee_show_categories 
					? '<div class="post_categories">'
						. academee_get_post_categories()
						. $academee_post_counters_output
						. '</div>' 
					: '')
			. '<h6 class="post_title">' . ($academee_post_link ? '<a href="' . esc_url($academee_post_link) . '">' : '') . ($academee_post_title) . ($academee_post_link ? '</a>' : '') . '</h6>'
			. apply_filters('academee_filter_get_post_info', 
								'<div class="post_info">'
									. ($academee_show_date 
										? '<span class="post_info_item post_info_posted">'
											. ($academee_post_link ? '<a href="' . esc_url($academee_post_link) . '" class="post_info_date">' : '') 
											. esc_html($academee_post_date) 
											. ($academee_post_link ? '</a>' : '')
											. '</span>'
										: '')
									. ($academee_show_author 
										? '<span class="post_info_item post_info_posted_by">' 
											. esc_html__('by', 'academee') . ' ' 
											. ($academee_post_link ? '<a href="' . esc_url($academee_post_author_url) . '" class="post_info_author">' : '') 
											. esc_html($academee_post_author_name) 
											. ($academee_post_link ? '</a>' : '') 
											. '</span>'
										: '')
									. (!$academee_show_categories && $academee_post_counters_output
										? $academee_post_counters_output
										: '')
								. '</div>')
		. '</div>'
	. '</article>';
academee_storage_set('academee_output_widgets_posts', $academee_output);
?>