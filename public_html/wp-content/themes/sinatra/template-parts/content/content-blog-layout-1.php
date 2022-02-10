<?php
/**
 * Template part for displaying blog post - layout 1.
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

		$sinatra_blog_entry_elements = sinatra_get_blog_entry_elements();

		echo '<div class="si-blog-entry-content">';

		if ( ! empty( $sinatra_blog_entry_elements ) ) {
			foreach ( $sinatra_blog_entry_elements as $sinatra_element ) {
				get_template_part( 'template-parts/entry/entry', $sinatra_element );
			}
		}

		echo '</div>';
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'sinatra_after_article' ); ?>
