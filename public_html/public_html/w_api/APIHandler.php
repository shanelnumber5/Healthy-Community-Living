<?php
/**
 * APIHandler handles requests from origin to the available APIs of a deployed package
 *
 * @package Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-07-30
 * @copyright 2014 Weebly, Inc
 */
require_once( __DIR__ . '/Bootstrap.php' );

class APIHandler
{
	/**
	 * @var $request
	 */
	private $request = NULL;

	/**
	 * Builds the request object from the given request, performs the given API command, if available
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	public function __construct( $request )
	{
		$this->validateRequest( $request );
		$this->request = $request;

		if ( method_exists( $this, $this->request['method'] ) === true )
		{
			call_user_func( array( $this, $this->request['method'] ) );
		}
		else
		{
			\Output::render404( );
		}
	}

	/**
	 * Unpublishes the site at docroot by removing HTML and assets
	 *
	 * @return void
	 */
	private function unpublishSite( )
	{
		$site = json_decode( file_get_contents( \BASE_SERVICES_DIR . '/' . Configuration::PUBLISHED_DATA_LOCATION ), true );

		if ( $site === false )
		{
			return;
		}

		// cleanup weebly part of .htaccess
		$htaccessLocation = \BASE_DOCROOT_DIR . '/.htaccess';
		$htaccessContent = file_get_contents($htaccessLocation);
		$htaccessContent = preg_replace("/\s*#Weebly Additions Start.*#Weebly Additions End/s", "", $htaccessContent);
		file_put_contents($htaccessLocation, $htaccessContent);

		foreach ( $site['pages'] as $page )
		{
			if ( $page !== '' && is_file( \BASE_DOCROOT_DIR . '/' . $page ) === true )
			{
				unlink( \BASE_DOCROOT_DIR . '/' . $page );
			}
		}

		foreach ( Configuration::$publishedArtifacts as $item )
		{
			if ( is_dir( \BASE_DOCROOT_DIR . $item ) === true || is_file( \BASE_DOCROOT_DIR . $item ) === true )
			{
				\FileSystem::deleteTree( \BASE_DOCROOT_DIR . $item );
			}
		}

		// clean up all temp file if exists.
		\FileSystem::deleteTempFiles(\BASE_DOCROOT_DIR);

		/**
		 * Fix for the bug identified on 2014-04-20 with redirects to .html on non-.html requests
		 */
		if ( is_dir( \BASE_DOCROOT_DIR . '/blog' ) === true )
		{
			\rmdir( \BASE_DOCROOT_DIR . '/blog' );
		}
		if ( is_dir( \BASE_DOCROOT_DIR . '/store' ) === true )
		{
			\rmdir( \BASE_DOCROOT_DIR . '/store' );
		}

		return;
	}

	/**
	 * Receives a new deployed services package to upgrade the running version
	 *
	 * @return void
	 */
	private function upgradeServices( )
	{
		if ( isset( $this->request['package'] ) === false )
		{
			return;
		}

		$this->request['package'] = \Output::decodeWireObject( $this->request['package'] );

		/**
		 * Confirm authenticity with origin before upgrading.
		 */
		$checksum = hash( 'sha1', $this->request['package'] );

		if ( \OriginRequest::validateDeployedPackage( $checksum, $this->request['timestamp'] ) !== true )
		{
			\Output::render404( );
		}

		$upgradeDir = \BASE_DOCROOT_DIR . '/w_api_upgrade/';

		if ( mkdir( $upgradeDir ) !== true )
		{
			\Output::sendHeader( $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error' );
		}

		file_put_contents( $upgradeDir . 'w_api.zip', $this->request['package'] );
		$zip = new \ZipArchive( );

		if ( $zip->open( $upgradeDir . 'w_api.zip', \ZipArchive::CHECKCONS ) !== true )
		{
			/**
			 * Bad archive -- do not upgrade
			 */
			$zip->close( );
			return;
		}

		if ( $zip->extractTo( $upgradeDir ) !== true )
		{
			$zip->close( );
			return;
		}

		foreach ( scandir( $upgradeDir . '/w_api/' ) as $file )
		{
			if ( strpos( $file, '.php' ) !== false )
			{
				if ( unlink( \BASE_SERVICES_DIR . $file ) === false )
				{
					/**
					 * Can't upgrade these services due to permissions errors, looks like
					 */
					$failure = true;
					break;
				}

				file_put_contents( \BASE_SERVICES_DIR . $file, file_get_contents( $upgradeDir . '/w_api/' . $file ) );
				unlink( $upgradeDir . '/w_api/' . $file );
			}
		}

		\FileSystem::deleteTree( $upgradeDir );
		$zip->close( );

		if ( isset( $failure ) === true )
		{
			\Output::sendHeader( $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error' );
		}
	}

	/**
	 * Receives an array containing hashes for all uploaded files, compares our uploaded files
	 * retreives new objects if necessary
	 *
	 * @return void
	 */
	private function updateFiles( )
	{
		$uploads = json_decode( $this->request['files'], true );

		if ( is_array( $uploads ) === false )
		{
			return;
		}
		
		foreach ( $uploads as $upload => $hash )
		{
			$file = \BASE_DOCROOT_DIR . $upload;

			if ( file_exists( $file ) === true )
			{
				if ( hash_file( 'crc32b', $file ) !== $hash )
				{
					\unlink( $file );

					// the value of forceDownload is 0 or 1
					if ( $this->request['forceDownload'] == true )
					{
						\OriginRequest::getUpdatedFile( $upload );
					}
				}
			}
			elseif ( $this->request['forceDownload'] == true )
			{
				/**
				 * The files don't currenty exist, but origin wants us to retrieve them now
				 * Make an OriginRequest for the file path
				 */
				\OriginRequest::getUpdatedFile( $upload );
			}
		}
	}

	/**
	 * Receives an object to be placed at docroot
	 *
	 * @return void
	 */
	private function receiveObject( )
	{
		\OriginRequest::expandObject( $this->request['object'] );
		return;
	}

	/**
	 * Validates a given API request as being sent from origin via request signature
	 *
	 * @param string $body
	 *
	 * @return void
	 */
	private function validateRequest( $request )
	{
		$token = $request['requestToken'];
		unset( $request['requestToken'] );

		if ( $token === OriginAPI::generateSignedRequest( http_build_query( $request ) ) )
		{
			return;
		}

		Output::render404( );
	}
}

// For older PHP or special FTP setting, force turn off PHP magic quotes. So $_REQUEST value can match server.
// If with magic quotes, validate request will fail due to mismatching request value from extra escaping.
if (get_magic_quotes_gpc()) {
	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($key, $val) = each($process)) {
		foreach ($val as $k => $v) {
			unset($process[$key][$k]);
			if (is_array($v)) {
				$process[$key][stripslashes($k)] = $v;
				$process[] = &$process[$key][stripslashes($k)];
			} else {
				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}

new APIHandler( $_REQUEST );
