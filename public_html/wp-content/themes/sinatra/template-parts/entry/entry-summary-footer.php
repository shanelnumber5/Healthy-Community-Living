<?php
/**
 * Template part for displaying entry footer.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'sinatra_before_entry_footer' ); ?>
<footer class="entry-footer">
	<?php

	// Allow text to be filtered.
	$sinatra_read_more_text = apply_filters( 'sinatra_entry_read_more_text', __( 'Read More', 'sinatra' ) );

	?>
	<a href="<?php echo esc_url( sinatra_entry_get_permalink() ); ?>" class="si-btn btn-text-1"><span><?php echo esc_html( $sinatra_read_more_text ); ?></span></a>
</footer>
<?php do_action( 'sinatra_after_entry_footer' ); ?>
