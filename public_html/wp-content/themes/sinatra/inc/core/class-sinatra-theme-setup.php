<?php
/**
 * Sinatra Theme Setup Class.
 *
 * @package  Sinatra
 * @author   Sinatra Team <hello@sinatrawp.com>
 * @since    1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Theme_Setup' ) ) :

	/**
	 * Sinatra Options Class.
	 */
	class Sinatra_Theme_Setup {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Main Sinatra_Theme_Setup Instance.
		 *
		 * @since 1.0.0
		 * @return Sinatra_Theme_Setup
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Theme_Setup ) ) {
				self::$instance = new Sinatra_Theme_Setup();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Add theme supports.
			add_action( 'after_setup_theme', array( $this, 'setup' ), 10 );

			// Content width.
			add_action( 'wp', array( $this, 'content_width' ) );
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * @since 1.0.0
		 */
		public function setup() {

			// Make the theme available for translation.
			load_theme_textdomain( 'sinatra', SINATRA_THEME_PATH . '/languages' );

			// Add default posts and comments RSS feed links to head.
			add_theme_support( 'automatic-feed-links' );

			// Add theme support for Post Thumbnails and image sizes.
			add_theme_support( 'post-thumbnails' );

			// Add theme support for various Post Formats.
			add_theme_support(
				'post-formats',
				array(
					'gallery',
					'image',
					'link',
					'quote',
					'video',
					'audio',
					'status',
					'aside',
				)
			);

			// Add title output.
			add_theme_support( 'title-tag' );

			// Add wide image support.
			add_theme_support( 'align-wide' );

			// Responsive embeds support.
			add_theme_support( 'responsive-embeds' );

			// Add support for core block visual styles.
			add_theme_support( 'wp-block-styles' );

			// Selective Refresh for Customizer.
			add_theme_support( 'customize-selective-refresh-widgets' );

			// Excerpt support for pages.
			add_post_type_support( 'page', 'excerpt' );

			// Register Navigation menu.
			register_nav_menus(
				array(
					'sinatra-primary' => esc_html__( 'Primary Navigation', 'sinatra' ),
				)
			);

			// Add theme support for Custom Logo.
			add_theme_support(
				'custom-logo',
				apply_filters(
					'sinatra_custom_logo_args',
					array(
						'width'       => 200,
						'height'      => 40,
						'flex-height' => true,
						'flex-width'  => true,
					)
				)
			);

			// Add theme support for Custom Background.
			add_theme_support(
				'custom-background',
				apply_filters(
					'sinatra_custom_background_args',
					array(
						'default-color' => '#FFFFFF',
						'default-size'  => 'fit',
					)
				)
			);

			// Enable HTML5 markup.
			add_theme_support(
				'html5',
				array(
					'search-form',
					'gallery',
					'caption',
					'script',
					'style',
				)
			);

			// Add editor style.
			$sinatra_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			add_editor_style( 'assets/css/editor-style' . $sinatra_suffix . '.css' );

			do_action( 'sinatra_after_setup_theme' );
		}

		/**
		 * Set the content width in pixels, based on the theme's design and stylesheet.
		 *
		 * @global int $content_width
		 * @since 1.0.0
		 */
		public function content_width() {
			global $content_width;

			if ( ! isset( $content_width ) ) {
				$content_width = apply_filters( 'sinatra_content_width', intval( sinatra_option( 'container_width' ) ) - 100 ); // phpcs:ignore
			}
		}
	}

endif;

/**
 * The function which returns the one Sinatra_Options instance.
 *
 * @since 1.0.0
 * @return object
 */
function sinatra_theme_setup() {
	return Sinatra_Theme_Setup::instance();
}

sinatra_theme_setup();
