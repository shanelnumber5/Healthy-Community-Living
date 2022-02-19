<?php

namespace Endurance\WP\Module\Data\Helpers;

/**
 * Helper class for gathering and formatting multibrand data
 */
class Multibrand {

	/**
	 * Get originating plugin based on plugin constants
	 * 
	 * @return string
	 */
	public static function get_origin_plugin() {
		if ( defined( 'BLUEHOST_PLUGIN_VERSION' ) ) {
			return array(
				'id' => 'bluehost',
				'name' => 'Bluehost',
				'slug' => 'bluehost-wordpress-plugin/bluehost-wordpress-plugin.php',
				'version' => BLUEHOST_PLUGIN_VERSION,
			);
		}
		if ( defined( 'HOSTGATOR_PLUGIN_VERSION' ) ) {
			return array(
				'id' => 'hostgator',
				'name' => 'HostGator',
				'slug' => 'hostgator-wordpress-plugin/hostgator-wordpress-plugin.php',
				'version' => HOSTGATOR_PLUGIN_VERSION,
			);
		}
		if ( defined( 'MM_VERSION' ) ) {
			return array(
				'id' => 'mojo',
				'name' => 'MOJO Marketplace',
				'slug' => 'mojo-marketplace-wp-plugin/mojo-marketplace.php',
				'version' => MM_VERSION,
			);
		}
		// default
		return array(
			'id' => 'error',
			'name' => 'Error',
			'slug' => 'error',
			'version' => '0',
		);
	}

	/**
	 * Get originating plugin version
	 * 
	 * @return string
	 */
	public static function get_origin_plugin_version() {	
		$origin = self::get_origin_plugin();
		return $origin['version'];
	}

	/**
	 * Get originating plugin id
	 * 
	 * @return string
	 */
	public static function get_origin_plugin_id() {	
		$origin = self::get_origin_plugin();
		return $origin['id'];
	}

	/**
	 * Get originating plugin name
	 * 
	 * @return string
	 */
	public static function get_origin_plugin_name() {	
		$origin = self::get_origin_plugin();
		return $origin['name'];
	}

	/**
	 * Get originating plugin slug
	 * 
	 * @return string
	 */
	public static function get_origin_plugin_slug() {	
		$origin = self::get_origin_plugin();
		return $origin['slug'];
	}

	/**
	 * Get originating plugin via reflection class
	 * 
	 * @return string
	 */
	public static function get_origin_plugin_path() {
		$reflector = new \ReflectionClass( get_class( $this ) );
		$plugins   = get_plugins();
		$file      = plugin_basename( $reflector->getFileName() );

		// is this file a standalone plugin? shouldn't be
		if ( array_key_exists( $file, $plugins ) ) {
			return $file;
		}

		// is file within another plugin? (as a vendor package) - expected
		$paths    = explode( '/', $file );
		$root_dir = array_shift( $paths );
		foreach ( $plugins as $path => $data ) {
			if ( 0 === strpos( $path, $root_dir ) ) {
				return $path;
			}
		}

		// not found yet? just return the full path
		// ie file not contained within a plugin (our ewphub local setup)
		return $file;
	}

}