<?php
/**
 * Template part for displaying content of Sinatra Canvas [Fullwidth] page template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?><?php sinatra_schema_markup( 'article' ); ?>>
	<div class="entry-content si-entry si-fullwidth-entry">
		<?php
		do_action( 'sinatra_before_page_content' );

		the_content();

		do_action( 'sinatra_after_page_content' );
		?>
	</div><!-- END .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
