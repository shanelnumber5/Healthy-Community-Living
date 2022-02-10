<?php
/**
 * Deprecated functions.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.2.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'sinatra_get_meta_icon' ) ) {
	/**
	 * Get icon for post entry meta.
	 *
	 * @deprecated 1.2.0
	 * @param  string      $slug Icon slug.
	 * @param  string      $icon Icon markup.
	 * @param  int|WP_Post $post_id Post object or ID.
	 */
	function sinatra_get_meta_icon( $slug = '', $icon = '', $post_id = '' ) {

		if ( sinatra_display_notices() ) {
			trigger_error( 'Method sinatra_get_meta_icon is deprecated since Sinatra version 1.17. Use sinatra()->icons->get_meta_icon( $slug, $icon, $post_id ) instead.', E_USER_DEPRECATED ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		}

		return sinatra()->icons->get_meta_icon( $slug, $icon, $post_id );
	}
}

if ( ! function_exists( 'sinatra_get_svg' ) ) {
	/**
	 * Return SVG markup.
	 *
	 * @deprecated 1.2.0
	 * @param  array $args Icon SVG args.
	 */
	function sinatra_get_svg( $args = array() ) {

		if ( sinatra_display_notices() ) {
			trigger_error( 'Method sinatra_get_svg is deprecated since Sinatra version 1.17. Use sinatra()->icons->get_svg( $icon, $args ) instead.', E_USER_DEPRECATED ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		}

		$icon = isset( $args['icon'] ) ? $args['icon'] : '';

		return sinatra()->icons->get_svg( $icon, $args );
	}
}
