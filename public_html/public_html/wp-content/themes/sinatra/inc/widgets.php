<?php
/**
 * Widget customization and register sidebar widget areas.
 *
 * @package Sinatra
 * @author  Gekik, LLC <hello@gekik.co>
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'sinatra_widgets_init' ) ) :
	/**
	 * Register widget area.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 * @since 1.0.0
	 */
	function sinatra_widgets_init() {

		// Default Sidebar.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Default Sidebar', 'sinatra' ),
				'id'            => 'sinatra-sidebar',
				'description'   => esc_html__( 'Widgets in this area are displayed in the left or right sidebar area based on your Default Sidebar Position settings.', 'sinatra' ),
				'before_widget' => '<div id="%1$s" class="si-sidebar-widget si-widget si-entry widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="h4 widget-title">',
				'after_title'   => '</div>',
			)
		);

		// Footer 1.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer 1', 'sinatra' ),
				'id'            => 'sinatra-footer-1',
				'description'   => esc_html__( 'Widgets in this area are displayed in the first footer column.', 'sinatra' ),
				'before_widget' => '<div id="%1$s" class="si-footer-widget si-widget si-entry widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="h4 widget-title">',
				'after_title'   => '</div>',
			)
		);

		// Footer 2.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer 2', 'sinatra' ),
				'id'            => 'sinatra-footer-2',
				'description'   => esc_html__( 'Widgets in this area are displayed in the second footer column.', 'sinatra' ),
				'before_widget' => '<div id="%1$s" class="si-footer-widget si-widget si-entry widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="h4 widget-title">',
				'after_title'   => '</div>',
			)
		);

		// Footer 3.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer 3', 'sinatra' ),
				'id'            => 'sinatra-footer-3',
				'description'   => esc_html__( 'Widgets in this area are displayed in the third footer column.', 'sinatra' ),
				'before_widget' => '<div id="%1$s" class="si-footer-widget si-widget si-entry widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="h4 widget-title">',
				'after_title'   => '</div>',
			)
		);

		// Footer 4.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer 4', 'sinatra' ),
				'id'            => 'sinatra-footer-4',
				'description'   => esc_html__( 'Widgets in this area are displayed in the fourth footer column.', 'sinatra' ),
				'before_widget' => '<div id="%1$s" class="si-footer-widget si-widget si-entry widget %2$s clr">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="h4 widget-title">',
				'after_title'   => '</div>',
			)
		);
	}
endif;
add_action( 'widgets_init', 'sinatra_widgets_init' );

if ( ! function_exists( 'sinatra_tag_cloud_widget' ) ) :
	/**
	 * Alters the default tag cloud font size.
	 *
	 * @since  1.0.0
	 * @param  array $args Widget arguments.
	 * @return Modified arguments.
	 */
	function sinatra_tag_cloud_widget( $args ) {
		$args['largest']  = 0.9285;
		$args['smallest'] = 0.9285;
		$args['unit']     = 'em';

		return $args;
	}
endif;
add_filter( 'widget_tag_cloud_args', 'sinatra_tag_cloud_widget' );
