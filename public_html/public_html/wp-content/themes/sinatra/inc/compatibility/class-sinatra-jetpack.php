<?php
/**
 * Jetpack compatibility class.
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

// Check if Jetpack is installed & activated.
if ( ! class_exists( 'Jetpack' ) ) {
	return;
}

if ( ! class_exists( 'Sinatra_Jetpack' ) ) :
	/**
	 * Jetpack compatibility class.
	 */
	class Sinatra_Jetpack {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'after_setup_theme', array( $this, 'jetpack_supports' ) );

			add_filter( 'infinite_scroll_credit', array( $this, 'tweak_credits_link' ) );
			add_filter( 'infinite_scroll_js_settings', array( $this, 'filter_infinite_scroll_js_settings' ) );
		}

		/**
		 * Add Jetpack theme supports.
		 *
		 * @since 1.0.0
		 */
		public function jetpack_supports() {

			/**
			 * Add theme support for Infinite Scroll.
			 */
			add_theme_support(
				'infinite-scroll',
				array(
					'container'      => 'content',
					'render'         => array( $this, 'infinite_scroll_render' ),
					'footer'         => 'page',
					'posts_per_page' => get_option( 'posts_per_page' ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
					'type'           => 'click',
				)
			);

			/**
			 * Add theme support for Responsive Videos.
			 */
			add_theme_support( 'jetpack-responsive-videos' );

			/**
			 * Add theme support for geo-location.
			 */
			add_theme_support( 'jetpack-geo-location' );
		}

		/**
		 * Custom render function for Infinite Scroll.
		 *
		 * @since 1.0.0
		 */
		public function infinite_scroll_render() {

			// WooCommerce products.
			if ( function_exists( 'is_shop' ) && is_shop() || function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {

				// Shop & category pages handled by default.
				return;

			} else {

				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', sinatra_get_article_feed_layout() );
				endwhile;
			}
		}

		/**
		 * Tweak footer credits bar link.
		 *
		 * @since 1.0.0
		 */
		public function tweak_credits_link() {
			return '<a href="https://wordpress.org/" rel="noopener noreferrer" target="_blank">' . esc_html__( 'Proudly powered by WordPress', 'sinatra' ) . '</a> | <a href="https://sinatrawp.com/" rel="noopener noreferrer" target="_blank">Sinatra Theme</a>';
		}

		/**
		 * Filter Jetpack infinite scroll JS settings.
		 *
		 * @since 1.0.0
		 * @param array $settings Infinite Scroll JS settings.
		 */
		public function filter_infinite_scroll_js_settings( $settings ) {

			$settings['text'] = esc_html__( 'Load More', 'sinatra' );

			return $settings;
		}

	}
endif;

new Sinatra_Jetpack();
