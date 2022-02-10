<?php
/**
 * Theme functions and definitions.
 *
 * @package Sinatra
 * @author  Sinatra Team <hello@sinatrawp.com>
 * @since   1.0.0
 */

/**
 * Main Sinatra class.
 *
 * @since 1.0.0
 */
final class Sinatra {

	/**
	 * Singleton instance of the class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private static $instance;

	/**
	 * Theme version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $version = '1.2.1';

	/**
	 * Main Sinatra Instance.
	 *
	 * Insures that only one instance of Sinatra exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0.0
	 * @return Sinatra
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra ) ) {
			self::$instance = new Sinatra();

			self::$instance->constants();
			self::$instance->includes();
			self::$instance->objects();

			// Hook now that all of the Sinatra stuff is loaded.
			do_action( 'sinatra_loaded' );
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
	}

	/**
	 * Setup constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function constants() {

		if ( ! defined( 'SINATRA_THEME_VERSION' ) ) {
			define( 'SINATRA_THEME_VERSION', $this->version );
		}

		if ( ! defined( 'SINATRA_THEME_URI' ) ) {
			define( 'SINATRA_THEME_URI', get_parent_theme_file_uri() );
		}

		if ( ! defined( 'SINATRA_THEME_PATH' ) ) {
			define( 'SINATRA_THEME_PATH', get_parent_theme_file_path() );
		}
	}

	/**
	 * Include files.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function includes() {

		require_once SINATRA_THEME_PATH . '/inc/common.php';
		require_once SINATRA_THEME_PATH . '/inc/deprecated.php';
		require_once SINATRA_THEME_PATH . '/inc/helpers.php';
		require_once SINATRA_THEME_PATH . '/inc/widgets.php';
		require_once SINATRA_THEME_PATH . '/inc/template-tags.php';
		require_once SINATRA_THEME_PATH . '/inc/template-parts.php';
		require_once SINATRA_THEME_PATH . '/inc/icon-functions.php';
		require_once SINATRA_THEME_PATH . '/inc/breadcrumbs.php';
		require_once SINATRA_THEME_PATH . '/inc/class-sinatra-dynamic-styles.php';

		// Core.
		require_once SINATRA_THEME_PATH . '/inc/core/class-sinatra-options.php';
		require_once SINATRA_THEME_PATH . '/inc/core/class-sinatra-enqueue-scripts.php';
		require_once SINATRA_THEME_PATH . '/inc/core/class-sinatra-fonts.php';
		require_once SINATRA_THEME_PATH . '/inc/core/class-sinatra-theme-setup.php';
		require_once SINATRA_THEME_PATH . '/inc/core/class-sinatra-db-updater.php';

		// Compatibility.
		require_once SINATRA_THEME_PATH . '/inc/compatibility/woocommerce/class-sinatra-woocommerce.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/socialsnap/class-sinatra-socialsnap.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/class-sinatra-wpforms.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/class-sinatra-jetpack.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/class-sinatra-endurance.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/class-sinatra-beaver-themer.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/class-sinatra-elementor.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/class-sinatra-elementor-pro.php';
		require_once SINATRA_THEME_PATH . '/inc/compatibility/class-sinatra-hfe.php';

		if ( is_admin() ) {
			require_once SINATRA_THEME_PATH . '/inc/utilities/class-sinatra-plugin-utilities.php';
			require_once SINATRA_THEME_PATH . '/inc/admin/class-sinatra-admin.php';
		}

		// Customizer.
		require_once SINATRA_THEME_PATH . '/inc/customizer/class-sinatra-customizer.php';
	}

	/**
	 * Setup objects to be used throughout the theme.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function objects() {

		sinatra()->options    = new Sinatra_Options();
		sinatra()->fonts      = new Sinatra_Fonts();
		sinatra()->icons      = new Sinatra_Icons();
		sinatra()->customizer = new Sinatra_Customizer();

		if ( is_admin() ) {
			sinatra()->admin = new Sinatra_Admin();
		}
	}
}

/**
 * The function which returns the one Sinatra instance.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sinatra = sinatra(); ?>
 *
 * @since 1.0.0
 * @return object
 */
function sinatra() {
	return Sinatra::instance();
}

sinatra();
