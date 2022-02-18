<?php
/**
 * Configuration handles deployed configuration values for Resellers
 * Configuration is built and deployed on site publish
 * This file should not be altered in deployed environments
 *
 * @package Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-07-08
 */
class Configuration
{
	const RESELLER_REQUEST_SECRET = '588abb70d5e24f553311c9c73c7847718eeb7b4f66158c39f053ea3c024e1aa4';
	const ERROR_REPORTING_LEVEL = 0;
	const DEPLOYED_HOSTNAME = 'healthycommunityliving.com';
	const BUILDDATE = 1644781702;
	const DEPLOYED_REPOSITORY = 'live';
	const ORIGIN_API_ENDPOINT = 'https://api.weeblycloud.com/private/';
	const CLIENT_API_ENDPOINT = 'http://pages.weebly.com/';
	const PUBLISHED_DATA_LOCATION = 'w_published_data.txt';

	const ERROR_LEVEL_IGNORE = 0;
	const ERROR_LEVEL_LOG = 1;
	const ERROR_LEVEL_DISPLAY = 2;
	const ERROR_LEVEL_EXCEPTION = 3;
	const ERROR_LEVEL_DEBUG_API = 4;

	/**
	 * Published artifacts which must be removed on unpublish/new publish
	 * @var $publishedArtifacts
	 */
	public static $publishedArtifacts = array(
		'/mobile',
		'/crossdomain.xml',
		'/favicon.ico',
		'/mobile_template_header.txt',
		'/mobile_template_homepage.txt',
		'/mobile_template_landing.txt',
		'/mobile_template_no-header.txt',
		'/template_landing.txt',
		'/repo.txt',
		'/template_landing.txt',
		'/template_no-header.txt',
		'/template_short-header.txt',
		'/template_tall-header.txt',
		'/userid.txt',
		'/gdpr/gdprscript.js',
		'/.well-known',
	);

	/**
	 * Determines what error degredation method to perform
	 *
	 * @param array $error
	 *
	 * @return mixed
	 */
	public static function handleError( $error )
	{
		if ( is_array( $error ) === false )
		{
			return;
		}

		if ( isset( $error['odysseus'] ) === true && is_array( $error['odysseus'] ) === true )
		{
			\OriginRequest::reportError( $error );

			/**
			 * Don't disclose any more server details
			 */
			$error = $error['error'];
		}

		switch ( self::ERROR_REPORTING_LEVEL )
		{
			case self::ERROR_LEVEL_IGNORE:
				return;

			case self::ERROR_LEVEL_LOG:
				\error_log( "An Odysseus client error has occurred: .\n" . var_export( $error, true ) . "\n" );
				return;

			case self::ERROR_LEVEL_DISPLAY:
				/**
				 * Todo: come up with a better way of displaying these
				 */
				return;

			case self::ERROR_LEVEL_EXCEPTION:
				throw new \Exception( 'An Odysseus client error has occurred: ' . var_export( $error, true ) );

			case self::ERROR_LEVEL_DEBUG_API:
				echo "<pre>\n";
				var_dump( $error );
				echo "</pre>\n";
				exit( );
		}

		return;
	}

	/**
	 * Handles shutdown tasks, error passing to Origin via handleError
	 *
	 * @return void
	 */
	public static function handleShutdown( )
	{
		$error = \error_get_last( );

		if ( $error['type'] === \E_ERROR )
		{
			$details = array(
				'odysseus' => array(
					'version' => self::BUILDDATE,
					'endpoint' => self::ORIGIN_API_ENDPOINT,
					'repository' => self::DEPLOYED_REPOSITORY,
					'request' => $_SERVER['REQUEST_URI'],
					'referrer' => isset( $_SERVER['HTTP_REFERER'] ) === true ? $_SERVER['HTTP_REFERER'] : ''
				),
				'php' => array(
					'version' => \phpversion( ),
					'extensions' => \get_loaded_extensions( ),
				),
				'environment' => array(
					'name' => \php_uname( 'a' ),
					'address' => $_SERVER['SERVER_ADDR'],
					'remoteAddress' => $_SERVER['REMOTE_ADDR']
				),
				'error' => $error
			);

			self::handleError( $details );
		}
	}
}
