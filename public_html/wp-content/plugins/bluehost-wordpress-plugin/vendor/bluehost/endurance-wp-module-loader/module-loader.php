<?php

if ( function_exists( 'add_action' ) ) {
	add_action( 'after_setup_theme', 'eig_load_modules', 100 );
}

/**
 * Initialize the module loader.
 */
function eig_load_modules() {
	Endurance_ModuleManager::loadActiveModules();

	// Read options, if module states don't match what is in database, do a write.
	$options = eig_active_module_options();
	$modules = Endurance_ModuleRegistry::collection()->all();
	$options->populate( $modules );
	$options->maybePersist();
}

/**
 * Returns an instance of Endurance_Options where the keys are the
 * names (slugs) for the modules. The values will be a boolean representing
 * whether the module is enabled or not.
 *
 * @return Endurance_Options
 */
function eig_active_module_options() {
	static $options;
	if ( ! isset( $options ) ) {
		$options = new Endurance_Options( 'eig_active_modules' );
	}

	return $options;
}

/**
 * Register a module
 *
 * Arguments:
 *  - name (required): The internal name used to reference the module.
 *  - label (required): The module label shown to end users.
 *  - callback (required): The callback used to load the module when it is active.
 *  - isActive (optional): The default state of the plugin on a fresh plugin installation. Defaults to false.
 *  - isHidden (optional): Whether to show the plugin toggle in the admin interface. Defaults to false.
 *
 * @param array $args
 */
function eig_register_module( array $args ) {

	if ( ! isset( $args['name'] ) ) {
		throw new InvalidArgumentException( 'Module must have a name!' );
	}

	if ( ! is_string( $args['name'] ) ) {
		throw new InvalidArgumentException( 'Module `name` argument must be a string!' );
	}

	if ( ! isset( $args['label'] ) ) {
		throw new InvalidArgumentException( 'Module must have a label!' );
	}

	if ( ! is_string( $args['label'] ) ) {
		throw new InvalidArgumentException( 'Module `label` argument must be a string!' );
	}

	if ( ! isset( $args['callback'] ) ) {
		throw new InvalidArgumentException( 'Module must have a callback!' );
	}

	if ( ! is_callable( $args['callback'] ) ) {
		throw new InvalidArgumentException( 'Module must have a valid callback!' );
	}

	if ( isset( $args['isActive'] ) && ! is_bool( $args['isActive'] ) ) {
		throw new InvalidArgumentException( 'Module `isActive` argument must be a boolean!' );
	}

	if ( isset( $args['isHidden'] ) && ! is_bool( $args['isHidden'] ) ) {
		throw new InvalidArgumentException( 'Module `isHidden` argument must be a boolean!' );
	}

	$defaults = array(
		'isActive' => false,
		'isHidden' => false,
	);

	$module = array_merge( $defaults, $args );

	$options = eig_active_module_options();

	// Fetch option to check if module is active or not, if no entry is found, use the default isActive state.
	$module['isActive'] = (bool) $options->get( $args['name'], (bool) $module['isActive'] );

	Endurance_ModuleRegistry::register( $module['name'], $module );

}
