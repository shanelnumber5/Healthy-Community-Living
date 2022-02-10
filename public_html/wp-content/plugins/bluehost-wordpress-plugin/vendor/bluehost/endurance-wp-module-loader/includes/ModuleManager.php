<?php

/**
 * Class Endurance_ModuleManager
 */
class Endurance_ModuleManager {

	/**
	 * Activate a module.
	 *
	 * @param string $name
	 */
	public static function activate( $name ) {

		// Update database
		$options = eig_active_module_options();
		$options->set( $name, true );
		$options->maybePersist();

		// Update registry
		$module = Endurance_ModuleRegistry::get( $name );
		if ( $module && is_array( $module ) ) {
			$module['isActive'] = true;
		}
		Endurance_ModuleRegistry::collection()->put( $name, $module );
	}

	/**
	 * Deactivate a module
	 *
	 * @param string $name
	 */
	public static function deactivate( $name ) {

		// Update database
		$options = eig_active_module_options();
		$options->set( $name, false );
		$options->maybePersist();

		// Update registry
		$module = Endurance_ModuleRegistry::get( $name );
		if ( $module && is_array( $module ) ) {
			$module['isActive'] = false;
		}
		Endurance_ModuleRegistry::collection()->put( $name, $module );
	}

	/**
	 * Load a specific module by name.
	 *
	 * @param string $name
	 */
	public static function load( $name ) {

		$module = Endurance_ModuleRegistry::get( $name );

		if ( isset( $module, $module['callback'] ) && is_callable( $module['callback'] ) ) {
			call_user_func( $module['callback'] );
		}
	}

	/**
	 * Load active modules.
	 */
	public static function loadActiveModules() {

		$activeModules = Endurance_ModuleRegistry::collection()->where( 'isActive', '===', true )->all();

		foreach ( $activeModules as $name => $module ) {
			self::load( $name );
		}
	}

	/**
	 * Report active status of specified module.
	 * 
	 * @param string $module name of specified module
	 * @return boolean boolean specifying if the named module is active or not
	 */
	public static function isModuleActive( $module_name ) {

		$activeModules = Endurance_ModuleRegistry::collection()->where( 'isActive', '===', true )->all();

		foreach ( $activeModules as $name => $module ) {
			if ( $module_name === $name ) {
				return true;
			}
		}

		return false;
	}
}