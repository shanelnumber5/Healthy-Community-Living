<?php
/**
 * Template part for displaying entry thumbnail (featured image).
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

// Get default post media.
$sinatra_media = sinatra_get_post_media( '' );

if ( ! $sinatra_media || post_password_required() ) {
	return;
}

$sinatra_post_format = get_post_format();

// Wrap with link for non-singular pages.
if ( 'link' === $sinatra_post_format || ! is_single( get_the_ID() ) ) {

	$sinatra_icon = '';

	if ( is_sticky() ) {
		$sinatra_icon = sprintf(
			'<span class="entry-media-icon" title="%1$s" aria-hidden="true"><span class="entry-media-icon-wrapper">%2$s%3$s</span></span>',
			esc_attr__( 'Featured', 'sinatra' ),
			sinatra()->icons->get_svg(
				'star',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			sinatra()->icons->get_svg( 'star', array( 'aria-hidden' => 'true' ) )
		);
	} elseif ( 'video' === $sinatra_post_format ) {
		$sinatra_icon = sprintf(
			'<span class="entry-media-icon" aria-hidden="true"><span class="entry-media-icon-wrapper">%1$s%2$s</span></span>',
			sinatra()->icons->get_svg(
				'play',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			sinatra()->icons->get_svg( 'play', array( 'aria-hidden' => 'true' ) )
		);
	} elseif ( 'link' === $sinatra_post_format ) {
		$sinatra_icon = sprintf(
			'<span class="entry-media-icon" title="%1$s" aria-hidden="true"><span class="entry-media-icon-wrapper">%2$s%3$s</span></span>',
			esc_url( sinatra_entry_get_permalink() ),
			sinatra()->icons->get_svg(
				'external-link',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			sinatra()->icons->get_svg( 'external-link', array( 'aria-hidden' => 'true' ) )
		);
	}

	$sinatra_icon = apply_filters( 'sinatra_post_format_media_icon', $sinatra_icon, $sinatra_post_format );

	$sinatra_media = sprintf(
		'<a href="%1$s" class="entry-image-link">%2$s%3$s</a>',
		esc_url( sinatra_entry_get_permalink() ),
		$sinatra_media,
		$sinatra_icon
	);
}

$sinatra_media = apply_filters( 'sinatra_post_thumbnail', $sinatra_media );

// Print the post thumbnail.
echo wp_kses(
	sprintf(
		'<div class="post-thumb entry-media thumbnail">%1$s</div>',
		$sinatra_media
	),
	sinatra_get_allowed_html_tags()
);
