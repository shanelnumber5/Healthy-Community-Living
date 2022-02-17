<?php
/**
 * The Sticky template to display the sticky posts
 *
 * Used for index/archive
 *
 * @package WordPress
 * @subpackage ACADEMEE
 * @since ACADEMEE 1.0
 */

$academee_columns = max(1, min(3, count(get_option( 'sticky_posts' ))));
$academee_post_format = get_post_format();
$academee_post_format = empty($academee_post_format) ? 'standard' : str_replace('post-format-', '', $academee_post_format);
$academee_animation = academee_get_theme_option('blog_animation');

?><div class="column-1_<?php echo esc_attr($academee_columns); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_sticky post_format_'.esc_attr($academee_post_format) ); ?>
	<?php echo (!academee_is_off($academee_animation) ? ' data-animation="'.esc_attr(academee_get_animation_classes($academee_animation)).'"' : ''); ?>
	>

	<?php
	if ( is_sticky() && is_home() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	academee_show_post_featured(array(
		'thumb_size' => academee_get_thumb_size($academee_columns==1 ? 'big' : ($academee_columns==2 ? 'med' : 'avatar'))
	));

	if ( !in_array($academee_post_format, array('link', 'aside', 'status', 'quote')) ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			the_title( sprintf( '<h6 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h6>' );
			// Post meta
			academee_show_post_meta(apply_filters('academee_filter_post_meta_args', array(), 'sticky', $academee_columns));
			?>
		</div><!-- .entry-header -->
		<?php
	}
	?>
</article></div>