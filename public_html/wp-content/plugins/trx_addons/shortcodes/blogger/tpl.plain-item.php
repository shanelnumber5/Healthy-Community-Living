<?php
/**
 * The style "plain" of the Blogger
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.4.3
 */

$args = get_query_var('trx_addons_args_sc_blogger');

if ($args['slider']) {
	?><div class="swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'])); ?>"><?php
}

$post_format = get_post_format();
$post_format = empty($post_format) ? 'standard' : str_replace('post-format-', '', $post_format);
$post_link = get_permalink();
$post_title = get_the_title();

?><div <?php post_class( 'sc_blogger_item post_format_'.esc_attr($post_format) ); ?>><?php

	// Post content
	?><div class="sc_blogger_item_content entry-content"><?php

		// Post categories
		trx_addons_sc_show_post_meta('sc_blogger', apply_filters('trx_addons_filter_show_post_meta', array(
			'components' => 'categories'
			), 'sc_blogger_plain', $args['columns'])
		);

		// Post title
		the_title( sprintf( '<h5 class="sc_blogger_item_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h5>' );
		
		// Post meta
		trx_addons_sc_show_post_meta('sc_blogger', apply_filters('trx_addons_filter_show_post_meta', array(
			'components' => 'date,counters',
			'counters' => 'views,comments,likes'
			), 'sc_blogger_plain', $args['columns'])
		);
		
	?></div><!-- .entry-content --><?php
	
?></div><!-- .sc_blogger_item --><?php

if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
?>