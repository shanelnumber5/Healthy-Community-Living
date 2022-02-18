<?php
/**
 * Template part for displaying more posts button in author box.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

?>

<div class="more-posts-button">
	<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ) ); ?>" class="si-btn btn-text-1" role="button"><span><?php echo wp_kses( __( 'View All Posts', 'sinatra' ), sinatra_get_allowed_html_tags( 'button' ) ); ?></span></i></a>
</div>
