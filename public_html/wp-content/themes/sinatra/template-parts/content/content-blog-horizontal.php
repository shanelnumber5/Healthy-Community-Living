<?php
/**
 * Template part for displaying blog post - horizontal.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<?php do_action( 'sinatra_before_article' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'sinatra-article' ); ?><?php sinatra_schema_markup( 'article' ); ?>>

	<?php
	$sinatra_blog_entry_format = get_post_format();

	if ( 'quote' === $sinatra_blog_entry_format ) {
		get_template_part( 'template-parts/entry/format/media', $sinatra_blog_entry_format );
	} else {

		$sinatra_classes     = array();
		$sinatra_classes[]   = 'si-blog-entry-wrapper';
		$sinatra_thumb_align = sinatra_option( 'blog_image_position' );
		$sinatra_thumb_align = apply_filters( 'sinatra_horizontal_blog_image_position', $sinatra_thumb_align );
		$sinatra_classes[]   = 'si-thumb-' . $sinatra_thumb_align;
		$sinatra_classes     = implode( ' ', $sinatra_classes );
		?>

		<div class="<?php echo esc_attr( $sinatra_classes ); ?>">
			<?php get_template_part( 'template-parts/entry/entry-thumbnail' ); ?>

			<div class="si-entry-content-wrapper">

				<?php
				if ( sinatra_option( 'blog_horizontal_post_categories' ) ) {
					get_template_part( 'template-parts/entry/entry-category' );
				}

				get_template_part( 'template-parts/entry/entry-header' );
				get_template_part( 'template-parts/entry/entry-summary' );


				if ( sinatra_option( 'blog_horizontal_read_more' ) ) {
					get_template_part( 'template-parts/entry/entry-summary-footer' );
				}

				get_template_part( 'template-parts/entry/entry-meta' );
				?>
			</div>
		</div>

	<?php } ?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'sinatra_after_article' ); ?>
