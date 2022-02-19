<?php
/**
 * Template part for displaying post format image entry.
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

$sinatra_media = sinatra_get_post_media( 'image' );

if ( ! $sinatra_media || post_password_required() ) {
	return;
}

?>

<div class="post-thumb entry-media thumbnail">

	<?php
	if ( ! is_single( get_the_ID() ) ) {
		$sinatra_media = sprintf(
			'<a href="%1$s" class="entry-image-link">%2$s</a>',
			esc_url( sinatra_entry_get_permalink() ),
			$sinatra_media
		);
	}

	echo $sinatra_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</div>
