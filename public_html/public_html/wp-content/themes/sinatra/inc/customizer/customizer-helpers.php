<?php
/**
 * Sinatra Customizer helper functions.
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
 * Returns array of available widgets.
 *
 * @since 1.0.0
 * @return array, $widgets array of available widgets.
 */
function sinatra_get_customizer_widgets() {

	$widgets = array(
		'text'    => 'Sinatra_Customizer_Widget_Text',
		'nav'     => 'Sinatra_Customizer_Widget_Nav',
		'socials' => 'Sinatra_Customizer_Widget_Socials',
		'search'  => 'Sinatra_Customizer_Widget_Search',
		'button'  => 'Sinatra_Customizer_Widget_Button',
	);

	return apply_filters( 'sinatra_customizer_widgets', $widgets );
}

/**
 * Get choices for "Hide on" customizer options.
 *
 * @since  1.0.0
 * @return array
 */
function sinatra_get_display_choices() {

	// Default options.
	$return = array(
		'home'       => array(
			'title' => esc_html__( 'Home Page', 'sinatra' ),
		),
		'posts_page' => array(
			'title' => esc_html__( 'Blog / Posts Page', 'sinatra' ),
		),
		'search'     => array(
			'title' => esc_html__( 'Search', 'sinatra' ),
		),
		'archive'    => array(
			'title' => esc_html__( 'Archive', 'sinatra' ),
			'desc'  => esc_html__( 'Dynamic pages such as categories, tags, custom taxonomies...', 'sinatra' ),
		),
		'post'       => array(
			'title' => esc_html__( 'Single Post', 'sinatra' ),
		),
		'page'       => array(
			'title' => esc_html__( 'Single Page', 'sinatra' ),
		),
	);

	// Get additionally registered post types.
	$post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'objects'
	);

	if ( is_array( $post_types ) && ! empty( $post_types ) ) {
		foreach ( $post_types as $slug => $post_type ) {
			$return[ $slug ] = array(
				'title' => $post_type->label,
			);
		}
	}

	return apply_filters( 'sinatra_display_choices', $return );
}

/**
 * Get device choices for "Display on" customizer options.
 *
 * @since  1.2.0
 * @return array
 */
function sinatra_get_device_choices() {

	// Default options.
	$return = array(
		'desktop' => array(
			'title' => esc_html__( 'Hide On Desktop', 'sinatra' ),
		),
		'tablet' => array(
			'title' => esc_html__( 'Hide On Tablet', 'sinatra' ),
		),
		'mobile' => array(
			'title' => esc_html__( 'Hide On Mobile', 'sinatra' ),
		),
	);

	return apply_filters( 'sinatra_device_choices', $return );
}
