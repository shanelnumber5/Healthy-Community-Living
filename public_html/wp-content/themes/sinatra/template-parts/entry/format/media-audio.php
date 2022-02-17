<?php
/**
 * Template part for displaying audio format entry.
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

if ( post_password_required() ) {
	return;
}

$sinatra_media = sinatra_get_post_media( 'audio' );

if ( $sinatra_media ) : ?>

	<div class="post-thumb entry-media thumbnail">
		<div class="si-audio-wrapper">
			<?php echo $sinatra_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>

<?php else : ?>

	<?php get_template_part( 'template-parts/entry/format/media' ); ?>

	<?php
endif;
