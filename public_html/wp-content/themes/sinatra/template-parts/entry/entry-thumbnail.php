<?php
/**
 * Template part for displaying media of the entry.
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

$sinatra_post_format = get_post_format();

if ( is_single() ) {
	$sinatra_post_format = '';
}

do_action( 'sinatra_before_entry_thumbnail' );

get_template_part( 'template-parts/entry/format/media', $sinatra_post_format );

do_action( 'sinatra_after_entry_thumbnail' );
