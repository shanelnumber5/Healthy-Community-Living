<?php
/**
 * Sinatra compatibility class for Elementor.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

namespace Elementor; // phpcs:ignore

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

if ( ! class_exists( 'Sinatra_Elementor' ) ) :

	/**
	 * Elementor compatibility.
	 */
	class Sinatra_Elementor {

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
		 * @return Sinatra_Elementor
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Elementor ) ) {
				self::$instance = new Sinatra_Elementor();
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

			// Enqueue Elementor editor styles.
			add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_editor_style' ) );

			// Setup postdata for Elementor pages.
			add_action( 'wp', array( $this, 'setup_postdata' ), 1 );
			add_action( 'elementor/preview/init', array( $this, 'setup_postdata' ), 5 );

			// Enqueue additional Elementor styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_additional' ) );
		}

		/**
		 * Editor stylesheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_editor_style() {

			// Script debug.
			$sinatra_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style(
				'sinatra-elementor-editor',
				SINATRA_THEME_URI . '/assets/css/compatibility/elementor-editor-style' . $sinatra_suffix . '.css',
				false,
				SINATRA_THEME_VERSION,
				'all'
			);
		}

		/**
		 * Setup default postdata for Elementor pages.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function setup_postdata() {

			// Page builder compatibility disabled.
			if ( ! sinatra_enable_page_builder_compatibility() ) {
				return;
			}

			// Skip posts.
			if ( 'post' === get_post_type() ) {
				return;
			}

			// Don't modify postdata if we are not on Elementor's edit page.
			if ( ! $this->is_elementor_editor() ) {
				return;
			}

			global $post;

			$id = sinatra_get_the_id();

			$setup = get_post_meta( $id, '_sinatra_page_builder_setup', true );

			if ( isset( $post ) && empty( $setup ) && ( is_admin() || is_singular() ) && empty( $post->post_content ) && $this->is_built_with_elementor( $id ) ) {

				update_post_meta( $id, '_sinatra_page_builder_setup', true );
				update_post_meta( $id, 'sinatra_disable_page_title', true );
				update_post_meta( $id, 'sinatra_disable_breadcrumbs', true );
				update_post_meta( $id, 'sinatra_disable_thumbnail', true );
				update_post_meta( $id, 'sinatra_sidebar_position', 'no-sidebar' );

				update_post_meta( $id, '_wp_page_template', 'page-templates/template-sinatra-fullwidth.php' );
			}

		}

		/**
		 * Additional Elementor styles.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_additional() {

			// Script debug.
			$sinatra_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Enqueue theme stylesheet.
			wp_enqueue_style(
				'sinatra-elementor',
				SINATRA_THEME_URI . '/assets/css/compatibility/elementor' . $sinatra_suffix . '.css',
				false,
				SINATRA_THEME_VERSION,
				'all'
			);
		}

		/**
		 * Check if page is built with elementor.
		 *
		 * @param int $id Post/Page Id.
		 * @since 1.0.0
		 *
		 * @return boolean
		 */
		public function is_built_with_elementor( $id ) {
			if ( version_compare( ELEMENTOR_VERSION, '1.5.0', '<' ) ) {
				return ( 'builder' === Plugin::$instance->db->get_edit_mode( $id ) );
			} else {
				return Plugin::$instance->db->is_built_with_elementor( $id );
			}
		}

		/**
		 * Check if Elementor Editor is loaded.
		 *
		 * @since 1.0.0
		 *
		 * @return boolean Elementor editor is loaded.
		 */
		private function is_elementor_editor() {
			return ( isset( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

endif;

/**
 * Returns the one Sinatra_Elementor instance.
 */
function sinatra_elementor() {
	return Sinatra_Elementor::instance();
}

sinatra_elementor();
