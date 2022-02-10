<?php
/**
 * Template part for displaying entry category.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<div class="post-category">

	<?php
	do_action( 'sinatra_before_post_category' );

	if ( is_singular() ) {
		sinatra_entry_meta_category( ' ', false );
	} else {
		if ( 'blog-horizontal' === sinatra_get_article_feed_layout() ) {
			sinatra_entry_meta_category( ' ', false );
		} else {
			sinatra_entry_meta_category( ', ', false );
		}
	}

	do_action( 'sinatra_after_post_category' );
	?>

</div>
