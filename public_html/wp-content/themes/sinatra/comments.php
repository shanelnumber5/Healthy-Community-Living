<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

/*
 * Return if comments are not meant to be displayed.
 */
if ( ! sinatra_comments_displayed() ) {
	return;
}

?>
<?php do_action( 'sinatra_before_comments' ); ?>
<section id="comments" class="comments-area">

	<div class="comments-title-wrapper center-text">
		<h3 class="comments-title">
			<?php

			// Get comments number.
			$sinatra_comments_count = get_comments_number();

			if ( 0 === intval( $sinatra_comments_count ) ) {
				$sinatra_comments_title = esc_html__( 'Comments', 'sinatra' );
			} else {
				/* translators: %s Comment number */
				$sinatra_comments_title = sprintf( _n( '%s Comment', '%s Comments', $sinatra_comments_count, 'sinatra' ), number_format_i18n( $sinatra_comments_count ) );
			}

			// Apply filters to the comments count.
			$sinatra_comments_title = apply_filters( 'sinatra_comments_count', $sinatra_comments_title );

			echo wp_kses( $sinatra_comments_title, sinatra_get_allowed_html_tags() );
			?>
		</h3><!-- END .comments-title -->

		<?php
		if ( ! have_comments() ) {
			$sinatra_no_comments_title = apply_filters( 'sinatra_no_comments_text', esc_html__( 'No comments yet. Why don&rsquo;t you start the discussion?', 'sinatra' ) );
			?>
			<p class="no-comments"><?php echo esc_html( $sinatra_no_comments_title ); ?></p>
		<?php } ?>
	</div>

	<ol class="comment-list">
		<?php

		// List comments.
		wp_list_comments(
			array(
				'callback'    => 'sinatra_comment',
				'avatar_size' => apply_filters( 'sinatra_comment_avatar_size', 50 ),
				'reply_text'  => __( 'Reply', 'sinatra' ),
			)
		);
		?>
	</ol>

	<?php
	// If comments are closed and there are comments, let's leave a note.
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="comments-closed center-text"><?php esc_html_e( 'Comments are closed', 'sinatra' ); ?></p>
	<?php endif; ?>

	<?php
	the_comments_pagination(
		array(
			'prev_text' => '<span class="screen-reader-text">' . __( 'Previous', 'sinatra' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'sinatra' ) . '</span>',
		)
	);
	?>

	<?php
	comment_form(
		array(
			/* translators: %1$s opening anchor tag, %2$s closing anchor tag */
			'must_log_in'   => '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a comment.', 'sinatra' ), '<a href="' . wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) . '">', '</a>' ) . '</p>', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			'logged_in_as'  => '<p class="logged-in-as">' . esc_html__( 'Logged in as', 'sinatra' ) . ' <a href="' . esc_url( admin_url( 'profile.php' ) ) . '">' . $user_identity . '</a> <a href="' . wp_logout_url( get_permalink() ) . '" title="' . esc_html__( 'Log out of this account', 'sinatra' ) . '">' . esc_html__( 'Log out?', 'sinatra' ) . '</a></p>',
			'class_submit'  => 'si-btn primary-button',
			'comment_field' => '<p class="comment-textarea"><textarea name="comment" id="comment" cols="44" rows="8" class="textarea-comment" placeholder="' . esc_html__( 'Write a comment&hellip;', 'sinatra' ) . '" required="required"></textarea></p>',
			'id_submit'     => 'comment-submit',
		)
	);
	?>

</section><!-- #comments -->
<?php do_action( 'sinatra_after_comments' ); ?>
