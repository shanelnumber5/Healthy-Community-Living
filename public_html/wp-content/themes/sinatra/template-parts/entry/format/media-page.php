<?php
/**
 * Template part for displaying page featured image.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
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

$sinatra_media = apply_filters( 'sinatra_post_thumbnail', $sinatra_media, get_the_ID() );

$sinatra_classes = array( 'post-thumb', 'entry-media', 'thumbnail' );

$sinatra_classes = apply_filters( 'sinatra_post_thumbnail_wrapper_classes', $sinatra_classes, get_the_ID() );
$sinatra_classes = trim( implode( ' ', array_unique( $sinatra_classes ) ) );

// Print the post thumbnail.
echo wp_kses_post(
	sprintf(
		'<div class="%2$s">%1$s</div>',
		$sinatra_media,
		esc_attr( $sinatra_classes )
	)
);
