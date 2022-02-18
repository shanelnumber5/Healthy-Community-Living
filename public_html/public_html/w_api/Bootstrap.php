<?php
/**
 * Bootstrap handles simple requires for files in the Deployed Services package
 *
 * @package Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-08-03
 * @copyright 2014 Weebly, Inc
 */

// Require helpers file.
require_once(__DIR__ . '/helpers.php');

define( 'BASE_SERVICES_DIR', realpath( __DIR__ . '/' ) . '/' );
define( 'BASE_DOCROOT_DIR', realpath( __DIR__ . '/../' ) . '/' );

function autoloader( $classname )
{
	$file = BASE_SERVICES_DIR . $classname . '.php';

	if ( file_exists( $file ) === true )
	{
		require_once( $file );
	}
	else
	{
		throw new \Exception( $classname . ' at ' . $file . ' does not exist.' );
	}
}

spl_autoload_register( 'autoloader' );

if ( \Configuration::ERROR_REPORTING_LEVEL >= \Configuration::ERROR_LEVEL_EXCEPTION )
{
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
} else {
	ini_set( 'display_errors', 0 );
}

register_shutdown_function( array( 'Configuration', 'handleShutdown' ) );
