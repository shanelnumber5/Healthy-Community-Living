<?php

use Endurance\WP\Module\Data\Data;
use Endurance\WP\Module\Data\Helpers\Multibrand;
use Endurance\WP\Module\Data\Helpers\Transient;

// Define constants
// Do not allow multiple copies of the module to be active
if ( defined( 'NFD_DATA_MODULE_VERSION' ) ) {
	exit;
} else {
	define( 'NFD_DATA_MODULE_VERSION', '1.8.2' );
}

if ( function_exists( 'add_action' ) ) {
	add_action( 'after_setup_theme', 'eig_module_data_register' );
}

/**
 * Register the data module
 */
function eig_module_data_register() {
	// exit if module manager does not exist
	// OR data module is already active
	if ( ! class_exists( 'Endurance_ModuleManager') || Endurance_ModuleManager::isModuleActive('data') ) {
		return;
	}
	
	eig_register_module(
		array(
			'name'     => 'data',
			'label'    => __( 'Data', 'endurance' ),
			'callback' => 'eig_module_data_load',
			'isActive' => true,
			'isHidden' => true,
		)
	);
}

/**
 * Load the data module
 */
function eig_module_data_load() {
	$module = new Data();
	$module->start();
}

/**
 * Register activation hook outside init so it will fire on activation.
 */
function nfd_plugin_activate() {
	Transient::set( 'nfd_plugin_activated', Multibrand::get_origin_plugin_slug() );
}
if ( function_exists( 'register_activation_hook' ) ) {
	register_activation_hook(
		Multibrand::get_origin_plugin_slug(),
		'nfd_plugin_activate'
	);
}
