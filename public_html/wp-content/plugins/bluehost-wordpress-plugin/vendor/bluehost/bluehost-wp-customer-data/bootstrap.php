<?php
use Bluehost\WP\Data\Customer;

if ( function_exists( 'add_action' ) ) {
	add_action( 'after_setup_theme', 'bluehost_register_data_package' );
}

/**
 * Register the single sign-on module
 */
function bluehost_register_data_package() {
	// exit if module manager does not exist
	if ( ! class_exists( 'Endurance_ModuleManager' ) ) {
		return;
	}

	// exit if data module is not active
	if ( ! Endurance_ModuleManager::isModuleActive('data') ) {
		return;
	}
	
	// add filter callback to add customer data
	add_filter( 'endurance_wp_data_module_cron_data_filter', 'bluehost_data_cron_callback' );
}

/**
 * Filter the cron event data object with bluehost specific customer data
 * 
 * @param array $data prepared to send in the cron event
 * @return array filtered data to send in the cron event
 */
function bluehost_data_cron_callback( $data ) {
	$data['customer'] = Customer::collect();
	return $data;
}