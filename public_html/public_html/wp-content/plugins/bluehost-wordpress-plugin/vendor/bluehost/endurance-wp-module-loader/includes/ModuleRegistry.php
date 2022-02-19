<?php

/**
 * Class Endurance_ModuleRegistry
 */
class Endurance_ModuleRegistry {

	/**
	 * @var Endurance_Collection
	 */
	protected static $collection;

	/**
	 * Fetch the collection of modules.
	 *
	 * @return Endurance_Collection
	 */
	public static function collection() {

		if ( ! isset( self::$collection ) ) {
			self::$collection = Endurance_Collection::make();
		}

		return self::$collection;
	}
	
	/**
	 * Get a module by name.
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public static function get( $name ) {
		return self::collection()->get( $name );
	}

	/**
	 * Register a module.
	 *
	 * @param $module array Module definition
	 */
	public static function register( $name, array $module ) {
		self::collection()->put( $name, $module );
	}

}