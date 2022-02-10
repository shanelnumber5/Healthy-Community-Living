<?php
/**
 * Plugin utilities class.
 *
 * This class has functions to install, activate & deactivate plugins.
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

/**
 * Plugin utilities.
 * Class that contains methods for changing plugin status.
 *
 * @since 1.0.0
 */
class Sinatra_Plugin_Utilities {

	/**
	 * Singleton instance of the class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private static $instance;

	/**
	 * Main Sinatra Plugin Utilities Instance.
	 *
	 * @since 1.0.0
	 * @return Sinatra_Plugin_Utilities
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sinatra_Plugin_Utilities ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Activate array of plugins.
	 *
	 * @param array, $plugins Plugins to be activated.
	 * @since 1.0.0
	 */
	public function activate_plugins( $plugins ) {

		$status = array();

		wp_clean_plugins_cache( false );

		// Activate plugins.
		foreach ( $plugins as $plugin ) {
			$status[ $plugin['slug'] ]['activate'] = $this->activate_plugin( $plugin['slug'] );
		}

		return $status;
	}

	/**
	 * Activate individual plugin.
	 *
	 * @param array, $plugin Plugin to be activated.
	 * @return void|WP_Error
	 * @since 1.0.0
	 */
	public function activate_plugin( $plugin ) {

		// Check permissions.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new WP_Error(
				'plugin_activation_failed',
				esc_html__( 'Current user can\'t activate plugins', 'sinatra' )
			);
		}

		// Validate plugin data.
		if ( empty( $plugin ) ) {
			return new WP_Error(
				'plugin_activation_failed',
				esc_html__( 'Missing plugin data.', 'sinatra' )
			);
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore
		}

		$plugin_data = get_plugins( '/' . $plugin );

		if ( empty( $plugin_data ) ) {
			return new WP_Error(
				'plugin_activation_failed',
				sprintf(
					// translators: %s is plugin name.
					esc_html__( 'Plugin %s is not installed.', 'sinatra' ),
					$plugin
				)
			);
		}

		$plugin_file_array  = array_keys( $plugin_data );
		$plugin_file        = $plugin_file_array[0];
		$plugin_to_activate = $plugin . '/' . $plugin_file;
		$activate           = activate_plugin( $plugin_to_activate );

		if ( is_wp_error( $activate ) ) {
			return $activate;
		}

		do_action( 'sinatra_plugin_activated_' . $plugin );
	}

	/**
	 * Deactivate individual plugin
	 *
	 * @param array, $plugin Plugin to be deactivated.
	 * @return void|WP_Error
	 * @since 1.0.0
	 */
	public function deactivate_plugin( $plugin ) {

		// Check permissions.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new WP_Error(
				'plugin_activation_failed',
				esc_html__( 'Current user can\'t activate plugins', 'sinatra' )
			);
		}

		// Validate plugin data.
		if ( empty( $plugin ) ) {
			return new WP_Error(
				'plugin_activation_failed',
				esc_html__( 'Missing plugin data.', 'sinatra' )
			);
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore
		}

		$plugin_data = get_plugins( '/' . $plugin );

		if ( empty( $plugin_data ) ) {
			return new WP_Error(
				'plugin_deactivation_failed',
				sprintf(
					// translators: %s is plugin name.
					esc_html__( 'Plugin %s is not active.', 'sinatra' ),
					$plugin
				)
			);
		}

		$plugin_file_array    = array_keys( $plugin_data );
		$plugin_file          = $plugin_file_array[0];
		$plugin_to_deactivate = $plugin . '/' . $plugin_file;

		deactivate_plugins( $plugin_to_deactivate );

		do_action( 'sinatra_plugin_deactivated_' . $plugin );
	}

	/**
	 * Check if plugin has a pending update.
	 *
	 * @param array,   $plugin Plugin to be activated.
	 * @param boolean, $strict Force plugin to update. Optional. Default is false.
	 * @since 1.0.0
	 */
	public function has_update( $plugin, $strict = false ) {

		$installed_plugin = $this->is_installed( $plugin['slug'] );

		if ( is_array( $installed_plugin ) && ! empty( $installed_plugin ) ) {

			$plugin_name = array_keys( $installed_plugin );
			$plugin_name = $plugin_name[0];

			$plugin_version = $installed_plugin ? $installed_plugin[ $plugin_name ]['Version'] : null;

			if ( $plugin_name && ! empty( $plugin_version ) ) {
				if ( isset( $plugin['version'] ) ) {
					return version_compare( $plugin_version, $plugin['version'], '<' );
				} elseif ( $strict ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check if plugin is installed.
	 *
	 * @param array, $plugin Check if plugin is installed.
	 * @since 1.0.0
	 */
	public function is_installed( $plugin ) {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore
		}

		$installed = get_plugins( '/' . $plugin );

		if ( ! empty( $installed ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if plugin is activated.
	 *
	 * @param array, $plugin Check if plugin is activated.
	 * @since 1.0.0
	 * @return bool Whether the plugin is activated.
	 */
	public function is_activated( $plugin ) {
		// Prevent a suppressed error within `get_plugins()` when the plugin is not installed.
		if ( ! file_exists( ABSPATH . '/' . WP_PLUGIN_DIR . '/' . $plugin ) ) {
			return false;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore
		}

		$installed_plugin = get_plugins( '/' . $plugin );

		if ( $installed_plugin ) {
			$plugin_name = array_keys( $installed_plugin );
			return is_plugin_active( $plugin . '/' . $plugin_name[0] );
		}

		return false;
	}

	/**
	 * Recommended plugins.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_recommended_plugins() {

		$plugins = array(
			'sinatra-core' => array(
				'name'  => 'Sinatra Core',
				'slug'  => 'sinatra-core',
				'desc'  => 'The Sinatra Core plugin adds extra functionality to Sinatra theme, such as Demo Library, widgets, custom blocks and more.',
				'thumb' => 'https://ps.w.org/sinatra-core/assets/icon-256x256.png',
			),
			'socialsnap'   => array(
				'name'      => 'Social Snap',
				'slug'      => 'socialsnap',
				'desc'      => 'Social Snap is the leading WordPress social media plugin that helps you drive more traffic and increase engagement.',
				'endurance' => false,
				'thumb'     => 'https://ps.w.org/socialsnap/assets/icon-256x256.png',
			),
		);

		return apply_filters( 'sinatra_recommended_plugins', $plugins );
	}

	/**
	 * Get non activated plugins from an array.
	 *
	 * @param array, $plugins Filter non active plugins.
	 * @since 1.0.0
	 */
	public function get_deactivated_plugins( $plugins ) {

		if ( is_array( $plugins ) && ! empty( $plugins ) ) {
			foreach ( $plugins as $slug => $plugin ) {
				if ( $this->is_activated( $slug ) ) {
					unset( $plugins[ $slug ] );
				}
			}
		}

		return $plugins;
	}

	/**
	 * Get plugin object based on slug.
	 *
	 * @since 1.0.0
	 * @param string $slug Plugin slug.
	 * @param array  $plugins Array of available plugins.
	 */
	public function get_plugin_by_slug( $slug, $plugins ) {

		if ( ! empty( $plugins ) ) {
			foreach ( $plugins as $plugin ) {
				if ( $plugin['slug'] === $slug ) {
					return $plugin;
				}
			}
		}

		return false;
	}
}

/**
 * The function which returns the one Sintra_Plugin_Utilities instance.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sinatra_plugin_utilities = sinatra_plugin_utilities(); ?>
 *
 * @since 1.0.0
 * @return object
 */
function sinatra_plugin_utilities() {
	return Sinatra_Plugin_Utilities::instance();
}
