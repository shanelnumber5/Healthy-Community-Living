<?php
/**
 * Template part for displaying ”Show Comments” button.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

// Do not show if the post is password protected.
if ( post_password_required() ) {
	return;
}

$sinatra_comment_count = get_comments_number();
$sinatra_comment_title = esc_html__( 'Leave a Comment', 'sinatra' );

if ( $sinatra_comment_count > 0 ) {
	/* translators: %s is comment count */
	$sinatra_comment_title = esc_html( sprintf( _n( 'Show %s Comment', 'Show %s Comments', $sinatra_comment_count, 'sinatra' ), $sinatra_comment_count ) );
}

?>
<a href="#" id="sinatra-comments-toggle" class="si-btn btn-large btn-fw btn-left-icon">
	<?php echo sinatra()->icons->get_svg( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span><?php echo $sinatra_comment_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
</a>
