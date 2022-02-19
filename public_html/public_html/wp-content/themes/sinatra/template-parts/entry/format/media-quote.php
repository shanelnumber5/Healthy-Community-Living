<?php
/**
 * Template part for displaying quote format entry.
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

$sinatra_quote_content = apply_filters( 'sinatra_post_format_quote_content', get_the_content() );
$sinatra_quote_author  = apply_filters( 'sinatra_post_format_quote_author', get_the_title() );
$sinatra_quote_bg      = has_post_thumbnail() ? ' style="background-image: url(\'' . esc_url( get_the_post_thumbnail_url() ) . '\')"' : '';
?>

<div class="si-blog-entry-content">
	<div class="entry-content si-entry"<?php sinatra_schema_markup( 'text' ); ?>>

		<?php if ( ! is_single() ) { ?>
			<a href="<?php the_permalink(); ?>" class="quote-link" aria-label="<?php esc_attr_e( 'Read more', 'sinatra' ); ?>"></a>
		<?php } ?>

			<div class="quote-post-bg"<?php echo $sinatra_quote_bg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></div>

			<div class="quote-inner">

				<?php echo sinatra()->icons->get_svg( 'quote', array( 'class' => 'icon-quote' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<h3><?php echo wp_kses( $sinatra_quote_content, sinatra_get_allowed_html_tags() ); ?></h3>
				<div class="author"><?php echo wp_kses( $sinatra_quote_author, sinatra_get_allowed_html_tags() ); ?></div>

			</div><!-- END .quote-inner -->

	</div>
</div><!-- END .si-blog-entry-content -->
