<?php
/**
 * Template part for displaying about author box.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

// Do not show the about author box if post is password protected.
if ( post_password_required() ) {
	return;
}
?>

<?php do_action( 'sinatra_entry_before_author' ); ?>
<section class="author-box"<?php sinatra_schema_markup( 'author' ); ?>>

	<div class="author-box-avatar">
		<?php echo get_avatar( get_the_author_meta( 'email' ), 75 ); ?>
	</div>

	<div class="author-box-meta">
		<div class="h4 author-box-title">
			<?php
			if ( is_single() ) {
				?>
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="url fn n" rel="author"<?php sinatra_schema_markup( 'url' ); ?>>
				<?php echo esc_html( get_the_author() ); ?>	
				</a>
				<?php
			} else {
				esc_html_e( 'About', 'sinatra' );
				?>
				<?php echo esc_html( get_the_author() ); ?>
			<?php } ?>
		</div>

		<?php do_action( 'sinatra_entry_after_author_name' ); ?>

		<?php
		$sinatra_author_description = get_the_author_meta( 'description' );
		$sinatra_author_id          = get_the_author_meta( 'ID' );
		$sinatra_current_user_id    = is_user_logged_in() ? wp_get_current_user()->ID : false;
		?>

		<div class="author-box-content"<?php sinatra_schema_markup( 'description' ); ?>>
			<?php
			if ( '' === $sinatra_author_description ) {
				if ( $sinatra_current_user_id && $sinatra_author_id === $sinatra_current_user_id ) {

					// Translators: %1$s: <a> tag. %2$s: </a>.
					printf( wp_kses_post( __( 'You haven&rsquo;t entered your Biographical Information yet. %1$sEdit your Profile%2$s now.', 'sinatra' ) ), '<a href="' . esc_url( get_edit_user_link( $sinatra_current_user_id ) ) . '">', '</a>' );
				}
			} else {
				echo wp_kses_post( $sinatra_author_description );
			}
			?>
		</div>

		<?php do_action( 'sinatra_entry_after_author_description' ); ?>
	</div><!-- END .author-box-meta -->

</section>
<?php do_action( 'sinatra_entry_after_author' ); ?>
