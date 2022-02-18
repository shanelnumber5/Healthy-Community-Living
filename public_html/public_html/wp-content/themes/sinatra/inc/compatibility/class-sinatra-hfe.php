<?php
/**
 * Sinatra compatibility class for Header Footer Elementor plugin.
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

// Return if Elementor not active.
if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

// Return if HFE not active.
if ( ! class_exists( 'Header_Footer_Elementor' ) ) {
	return false;
}

if ( ! class_exists( 'Sinatra_HFE' ) ) :

	/**
	 * HFE compatibility.
	 */
	class Sinatra_HFE {

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
		 * @return Sinatra_HFE
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_HFE ) ) {
				self::$instance = new Sinatra_HFE();
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
			add_action( 'sinatra_header', array( $this, 'do_header' ), 0 );
			add_action( 'sinatra_footer', array( $this, 'do_footer' ), 0 );
		}

		/**
		 * Add theme support
		 *
		 * @since 1.0.0
		 */
		public function add_theme_support() {
			add_theme_support( 'header-footer-elementor' );
		}

		/**
		 * Override Header
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_header() {
			if ( ! hfe_header_enabled() ) {
				return;
			}

			hfe_render_header();

			remove_action( 'sinatra_header', 'sinatra_topbar_output', 10 );
			remove_action( 'sinatra_header', 'sinatra_header_output', 20 );
		}

		/**
		 * Override Footer
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_footer() {
			if ( ! hfe_footer_enabled() ) {
				return;
			}

			hfe_render_footer();

			remove_action( 'sinatra_footer', 'sinatra_footer_output', 20 );
			remove_action( 'sinatra_footer', 'sinatra_copyright_bar_output', 30 );
		}

	}

endif;

/**
 * Returns the one Sinatra_HFE instance.
 */
function sinatra_hfe() {
	return Sinatra_HFE::instance();
}

sinatra_hfe();
