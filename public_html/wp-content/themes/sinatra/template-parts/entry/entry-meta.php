<?php
/**
 * Template part for displaying entry meta info.
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

/**
 * Only show meta tags for posts.
 */
if ( ! in_array( get_post_type(), (array) apply_filters( 'sinatra_entry_meta_post_type', array( 'post' ) ), true ) ) {
	return;
}

do_action( 'sinatra_before_entry_meta' );

// Get meta items to be displayed.
$sinatra_meta_elements = sinatra_get_entry_meta_elements();

if ( ! empty( $sinatra_meta_elements ) ) {

	echo '<div class="entry-meta"><div class="entry-meta-elements">';

	do_action( 'sinatra_before_entry_meta_elements' );

	// Loop through meta items.
	foreach ( $sinatra_meta_elements as $sinatra_meta_item ) {

		// Call a template tag function.
		if ( function_exists( 'sinatra_entry_meta_' . $sinatra_meta_item ) ) {
			call_user_func( 'sinatra_entry_meta_' . $sinatra_meta_item );
		}
	}

	// Add edit post link.
	$sinatra_edit_icon = sinatra()->icons->get_meta_icon( 'edit', sinatra()->icons->get_svg( 'edit-3', array( 'aria-hidden' => 'true' ) ) );

	sinatra_edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				$sinatra_edit_icon . __( 'Edit <span class="screen-reader-text">%s</span>', 'sinatra' ),
				sinatra_get_allowed_html_tags()
			),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);

	do_action( 'sinatra_after_entry_meta_elements' );

	echo '</div></div>';
}

do_action( 'sinatra_after_entry_meta' );
