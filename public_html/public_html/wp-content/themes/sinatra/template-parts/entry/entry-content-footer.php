<?php
/**
 * Template part for displaying entry tags.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

$sinatra_entry_elements    = sinatra_option( 'single_post_elements' );
$sinatra_entry_footer_tags = isset( $sinatra_entry_elements['tags'] ) && $sinatra_entry_elements['tags'] && has_tag();
$sinatra_entry_footer_date = isset( $sinatra_entry_elements['last-updated'] ) && $sinatra_entry_elements['last-updated'] && get_the_time( 'U' ) !== get_the_modified_time( 'U' );

$sinatra_entry_footer_tags = apply_filters( 'sinatra_display_entry_footer_tags', $sinatra_entry_footer_tags );
$sinatra_entry_footer_date = apply_filters( 'sinatra_display_entry_footer_date', $sinatra_entry_footer_date );

// Nothing is enabled, don't display the div.
if ( ! $sinatra_entry_footer_tags && ! $sinatra_entry_footer_date ) {
	return;
}
?>

<?php do_action( 'sinatra_before_entry_footer' ); ?>

<div class="entry-footer">

	<?php
	// Post Tags.
	if ( $sinatra_entry_footer_tags ) {
		sinatra_entry_meta_tag(
			'<div class="post-tags"><span class="cat-links">',
			'',
			'</span></div>',
			0,
			false
		);
	}

	// Last Updated Date.
	if ( $sinatra_entry_footer_date ) {

		$sinatra_before = '<span class="last-updated si-iflex-center">';

		if ( true === sinatra_option( 'single_entry_meta_icons' ) ) {
			$sinatra_before .= sinatra()->icons->get_svg( 'edit-3' );
		}

		sinatra_entry_meta_date(
			array(
				'show_published' => false,
				'show_modified'  => true,
				'before'         => $sinatra_before,
				'after'          => '</span>',
			)
		);
	}
	?>

</div>

<?php do_action( 'sinatra_after_entry_footer' ); ?>
