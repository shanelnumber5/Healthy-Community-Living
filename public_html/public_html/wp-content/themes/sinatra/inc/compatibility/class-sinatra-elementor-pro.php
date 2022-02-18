<?php
/**
 * Sinatra compatibility class for Elementor Pro.
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
if ( ! class_exists( '\Elementor\Plugin' ) || ! class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) {
	return;
}

// PHP 5.3+ is required.
if ( ! version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	return;
}

if ( ! class_exists( 'Sinatra_Elementor_Pro' ) ) :

	/**
	 * Elementor compatibility.
	 */
	class Sinatra_Elementor_Pro {

		/**
		 * Singleton instance of the class.
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Elementor location manager
		 *
		 * @var object
		 */
		public $elementor_location_manager;

		/**
		 * Instance.
		 *
		 * @since 1.0.0
		 * @return Sinatra_Elementor_Pro
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Elementor_Pro ) ) {
				self::$instance = new Sinatra_Elementor_Pro();
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

			// Register theme locations.
			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			// Override templates.
			add_action( 'sinatra_header', array( $this, 'do_header' ), 0 );
			add_action( 'sinatra_footer', array( $this, 'do_footer' ), 0 );
			add_action( 'sinatra_content_404', array( $this, 'do_content_none' ), 0 );
			add_action( 'sinatra_content_single', array( $this, 'do_content_single_post' ), 0 );
			add_action( 'sinatra_content_page', array( $this, 'do_content_single_page' ), 0 );
			add_action( 'sinatra_content_archive', array( $this, 'do_archive' ), 0 );
		}

		/**
		 * Register Theme Location for Elementor.
		 *
		 * @param object $manager Elementor object.
		 * @since 1.0.0
		 * @return void
		 */
		public function register_locations( $manager ) {
			$manager->register_all_core_location();

			$this->elementor_location_manager = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager(); // phpcs:ignore PHPCompatibility.Syntax.NewDynamicAccessToStatic.Found
		}

		/**
		 * Override Header.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_header() {
			$did_location = $this->elementor_location_manager->do_location( 'header' );

			if ( $did_location ) {
				remove_action( 'sinatra_header', 'sinatra_topbar_output', 10 );
				remove_action( 'sinatra_header', 'sinatra_header_output', 20 );
			}
		}

		/**
		 * Override Footer.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_footer() {
			$did_location = $this->elementor_location_manager->do_location( 'footer' );

			if ( $did_location ) {
				remove_action( 'sinatra_footer', 'sinatra_footer_output', 20 );
				remove_action( 'sinatra_footer', 'sinatra_copyright_bar_output', 30 );
			}
		}

		/**
		 * Override Footer.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_content_none() {
			if ( ! is_404() ) {
				return;
			}

			$did_location = $this->elementor_location_manager->do_location( 'single' );

			if ( $did_location ) {
				remove_action( 'sinatra_content_404', 'sinatra_404_page_content' );
			}
		}

		/**
		 * Override Single Post.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_content_single_post() {
			$did_location = $this->elementor_location_manager->do_location( 'single' );

			if ( $did_location ) {
				remove_action( 'sinatra_content_single', 'sinatra_content_single' );
				remove_action( 'sinatra_after_singular', 'sinatra_output_comments' );
			}
		}

		/**
		 * Override Single Page.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_content_single_page() {
			$did_location = $this->elementor_location_manager->do_location( 'single' );

			if ( $did_location ) {
				remove_action( 'sinatra_content_page', 'sinatra_content_page' );
				remove_action( 'sinatra_after_singular', 'sinatra_output_comments' );
			}
		}

		/**
		 * Override Archive.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_archive() {
			$did_location = $this->elementor_location_manager->do_location( 'archive' );

			if ( $did_location ) {
				remove_action( 'sinatra_before_content', 'sinatra_archive_info' );
				remove_action( 'sinatra_content_archive', 'sinatra_content' );
			}
		}
	}

endif;

/**
 * Returns the one Sinatra_Elementor_Pro instance.
 */
function sinatra_elementor_pro() {
	return Sinatra_Elementor_Pro::instance();
}

sinatra_elementor_pro();
