<?php
/**
 * Sinatra compatibility class for Beaver Themer.
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

// Return if Beaver Themer not active.
if ( ! class_exists( 'FLThemeBuilderLoader' ) || ! class_exists( 'FLThemeBuilderLayoutData' ) ) {
	return;
}

// PHP 5.3+ is required.
if ( ! version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	return;
}

if ( ! class_exists( 'Sinatra_Beaver_Themer' ) ) :

	/**
	 * Beaver Themer compatibility.
	 */
	class Sinatra_Beaver_Themer {

		/**
		 * Singleton instance of the class.
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Instance.
		 *
		 * @since 1.0.0
		 * @return Sinatra_Beaver_Themer
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Beaver_Themer ) ) {
				self::$instance = new Sinatra_Beaver_Themer();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
			add_action( 'wp', array( $this, 'header_footer_render' ) );
			add_action( 'wp', array( $this, 'page_header_render' ) );
			add_filter( 'fl_theme_builder_part_hooks', array( $this, 'register_part_hooks' ) );
		}

		/**
		 * Add theme support
		 *
		 * @since 1.0.0
		 */
		public function add_theme_support() {
			add_theme_support( 'fl-theme-builder-headers' );
			add_theme_support( 'fl-theme-builder-footers' );
			add_theme_support( 'fl-theme-builder-parts' );
		}

		/**
		 * Update header/footer with Beaver template
		 *
		 * @since 1.0.0
		 */
		public function header_footer_render() {

			// Get the header ID.
			$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();

			// If we have a header, remove the theme header and hook in Theme Builder's.
			if ( ! empty( $header_ids ) ) {

				// Remove Top Bar.
				remove_action( 'sinatra_header', 'sinatra_topbar_output', 10 );

				// Remove Main Header.
				remove_action( 'sinatra_header', 'sinatra_header_output', 20 );

				// Replacement header.
				add_action( 'sinatra_header', 'FLThemeBuilderLayoutRenderer::render_header' );
			}

			// Get the footer ID.
			$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();

			// If we have a footer, remove the theme footer and hook in Theme Builder's.
			if ( ! empty( $footer_ids ) ) {

				// Remove Main Footer.
				remove_action( 'sinatra_footer', 'sinatra_footer_output', 20 );

				// Remove Copyright Bar.
				remove_action( 'sinatra_footer', 'sinatra_copyright_bar_output', 30 );

				// Replacement footer.
				add_action( 'sinatra_footer', 'FLThemeBuilderLayoutRenderer::render_footer' );
			}
		}

		/**
		 * Remove page header if using Beaver Themer.
		 *
		 * @since 1.0.0
		 */
		public function page_header_render() {

			// Get the page ID.
			$page_ids = FLThemeBuilderLayoutData::get_current_page_content_ids();

			// If we have a content layout, remove the theme page header.
			if ( ! empty( $page_ids ) ) {
				remove_action( 'sinatra_page_header', 'sinatra_page_header_template' );
			}
		}

		/**
		 * Register hooks
		 *
		 * @since 1.0.0
		 */
		public function register_part_hooks() {
			return array(
				array(
					'label' => 'Header',
					'hooks' => array(
						'sinatra_before_masthead' => esc_html__( 'Before Header', 'sinatra' ),
						'sinatra_after_masthead'  => esc_html__( 'After Header', 'sinatra' ),
					),
				),
				array(
					'label' => 'Main',
					'hooks' => array(
						'sinatra_before_main' => esc_html__( 'Before Main', 'sinatra' ),
						'sinatra_after_main'  => esc_html__( 'After Main', 'sinatra' ),
					),
				),
				array(
					'label' => 'Content',
					'hooks' => array(
						'sinatra_before_page_content' => esc_html__( 'Before Content', 'sinatra' ),
						'sinatra_after_page_content'  => esc_html__( 'After Content', 'sinatra' ),
					),
				),
				array(
					'label' => 'Footer',
					'hooks' => array(
						'sinatra_before_colophon' => esc_html__( 'Before Footer', 'sinatra' ),
						'sinatra_after_colophon'  => esc_html__( 'After Footer', 'sinatra' ),
					),
				),
				array(
					'label' => 'Sidebar',
					'hooks' => array(
						'sinatra_before_sidebar' => esc_html__( 'Before Sidebar', 'sinatra' ),
						'sinatra_after_sidebar'  => esc_html__( 'After Sidebar', 'sinatra' ),
					),
				),
				array(
					'label' => 'Singular',
					'hooks' => array(
						'sinatra_before_singular'       => __( 'Before Singular', 'sinatra' ),
						'sinatra_after_singular'        => __( 'After Singular', 'sinatra' ),
						'sinatra_before_comments'       => __( 'Before Comments', 'sinatra' ),
						'sinatra_after_comments'        => __( 'After Comments', 'sinatra' ),
						'sinatra_before_single_content' => __( 'Before Single Content', 'sinatra' ),
						'sinatra_after_single_content'  => __( 'After Single Content', 'sinatra' ),
					),
				),
			);
		}

	}

endif;

/**
 * Returns the one Sinatra_Beaver_Themer instance.
 */
function sinatra_beaver_themer() {
	return Sinatra_Beaver_Themer::instance();
}

sinatra_beaver_themer();
