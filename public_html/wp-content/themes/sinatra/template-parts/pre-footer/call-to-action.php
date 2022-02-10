<?php
/**
 * The template for displaying call to action in pre footer.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

$sinatra_cta_text = apply_filters( 'sinatra_pre_footer_cta_text', sinatra_option( 'pre_footer_cta_text' ) );

$sinatra_cta_button_args = array(
	'text'    => sinatra_option( 'pre_footer_cta_btn_text' ),
	'url'     => sinatra_option( 'pre_footer_cta_btn_url' ),
	'new_tab' => sinatra_option( 'pre_footer_cta_btn_new_tab' ),
	'class'   => 'si-btn btn-large',
);
$sinatra_cta_button_args = apply_filters( 'sinatra_pre_footer_cta_button', $sinatra_cta_button_args );

$sinatra_cta_button = '';

if ( $sinatra_cta_button_args['text'] || is_customize_preview() ) {
	$sinatra_cta_button = sprintf(
		'<a href="%1$s" class="%2$s" role="button" %3$s>%4$s</a>',
		esc_url( $sinatra_cta_button_args['url'] ),
		esc_attr( $sinatra_cta_button_args['class'] ),
		$sinatra_cta_button_args['new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : 'target="_self"',
		esc_html( $sinatra_cta_button_args['text'] )
	);
}

// Classes.
$sinatra_cta_classes    = array( 'si-container', 'si-pre-footer-cta' );
$sinatra_cta_visibility = sinatra_option( 'pre_footer_cta_visibility' );

if ( 'all' !== $sinatra_cta_visibility ) {
	$sinatra_cta_classes[] = 'sinatra-' . $sinatra_cta_visibility;
}

$sinatra_cta_classes = apply_filters( 'sinatra_pre_footer_cta_classes', $sinatra_cta_classes );
$sinatra_cta_classes = trim( implode( ' ', $sinatra_cta_classes ) );

?>
<div class="<?php echo esc_attr( $sinatra_cta_classes ); ?>">
	<div class="si-flex-row middle-md">

		<div class="col-xs-12 col-md-8 center-xs start-md">
			<p class="h3"><?php echo wp_kses_post( $sinatra_cta_text ); ?></p>
		</div>

		<div class="col-xs-12 col-md-4 center-xs end-md">
			<?php echo $sinatra_cta_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

	</div>
</div>
