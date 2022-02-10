<?php
/**
 * OriginRequest handles requests to origin, Weebly or otherwise, to retrieve files that have yet been cached locally
 *
 * @pacakge Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-07-01
 * @copyright 2014 Weebly, Inc
 */
class OriginRequest
{
	/**
	 * Performs a request to Origin to retrieve a given object for the given path
	 *
	 * @param array $request
	 *
	 * @return object|bool
	 * @throws Exception
	 */
	public static function getObject( $request )
	{
		if ( isset( $request ) === false )
		{
			return false;
		}

		$result = OriginAPI::makeRequest(
			'getObject',
			array(
				'host' => Configuration::DEPLOYED_HOSTNAME,
				'request' => $request
			)
		);

		if ( isset( $result->response ) === false || isset( $result->response->success ) === false || $result->response->success === false )
		{
			return false;
		}

		// if it's a gdpr script, we handle things a little bit differently (because we can't store it on the server)
		if ($request['path'] === '/gdpr/gdprscript.js') {
			// first, expand it like normal
			self::expandObject($result->response->object);

			// fetch the contents
			$content = file_get_contents(__DIR__ . '/../gdpr/gdprscript.js');

			// remvoe the directory
			unlink(__DIR__ . '/../gdpr/gdprscript.js');
			rmdir(__DIR__ .'/../gdpr');

			// return the content
			return (object)array(
				'object' => base64_encode($content),
				'type' => 'js'
			);
		}

		/**
		 * Pull the asset out of the response, if we're not streaming it, and store it
		 */
		if ( $result->response->stream !== true && (!isset($result->response->retryRaw) || $result->response->retryRaw !== true))
		{
			$success = self::expandObject( $result->response->object );

			if ( $success === false )
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		elseif (isset($result->response->retryRaw) && $result->response->retryRaw === true)
		{
			return true;
		}
		else
		{
			return $result->response;
		}
	}

	/**
	 * Initiates a request to download (pre-cache) a file that has been updated at origin
	 *
	 * @param string $file
	 *
	 * @return void
	 */
	public static function getUpdatedFile( $file = NULL )
	{
		if ( isset( $file ) === false )
		{
			return;
		}

		/**
		 * Build a rudimentary request array
		 */
		$request = array( );
		$request['directories'] = explode( '/', ltrim( $file, '/' ) );
		$request['path'] = $file;

		if ( count( $request['directories'] ) > 1 )
		{
			$request['file'] = array_pop( $request['directories'] );
		}

		/**
		 * Eeek -- it's unlikely a page, but this may bite someone down the road!
		 */
		$request['isPage'] = false;

		self::getObject( $request );
	}

	/**
	 * Sends a request to Origin to confirm authenticity of a received DeployedService package
	 *
	 * @param string $checksum
	 * @param int $timestamp
	 *
	 * @return bool
	 */
	public static function validateDeployedPackage( $checksum, $timestamp )
	{
		$request = \OriginAPI::makeRequest(
			'validateDeployedPackage',
			array(
				'host' => \Configuration::DEPLOYED_HOSTNAME,
				'checksum' => $checksum,
				'timestamp' => $timestamp
			)
		);

		if ( isset( $request->response->validated ) === true && $request->response->validated === true )
		{
			return true;
		}

		return false;
	}

	/**
	 * Reports an error back to origin via OriginAPI
	 *
	 * @param array $error
	 *
	 * @return void
	 */
	public static function reportError( $error )
	{
		if ( is_array( $error ) === false )
		{
			return;
		}

		\OriginAPI::makeRequest(
			'reportError',
			array(
				'host' => \Configuration::DEPLOYED_HOSTNAME,
				'error' => $error
			)
		);
	}

	/**
	 * Unzips the content from a given object resource
	 *
	 * @param string $object
	 *
	 * @return bool
	 */
	public static function expandObject( $object )
	{
		if ( isset( $object ) === false )
		{
			return false;
		}

		/**
		 * Create a fairly random place to drop this in -- it'll get removed at the end of execution anyway
		 */
		$path = \BASE_DOCROOT_DIR . md5( rand( 0, 999 ) ) . '.zip';

		/**
		 * Object should be "prop-up" ready, with a path that reflects where it should live on disk
		 * from a relative path of the client webroot
		 */
		$object = file_put_contents( $path, \Output::decodeWireObject( $object ) );

		$zip = new \ZipArchive( );

		if ( $zip->open( $path, \ZipArchive::CHECKCONS ) !== true )
		{
			unlink( $path );
			$zip->close( );
			return false;
		}

		/**
		 * The @ symbol here is awful, but it's even more important that we don't throw a Warning on the extractTo if it fails
		 * The early Warning shown causes any further rendering to get lost, because we've already started sending headers
		 */
		if ( @$zip->extractTo( \BASE_DOCROOT_DIR ) !== true )
		{
			/**
			 * Something has gone really wrong -- send a 500.
			 */
			\Output::sendHeader( $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error' );
			unlink( $path );
			$zip->close( );
			throw new \Exception( 'Could not expand our origin object' );
		}

		unlink( $path );
		$zip->close( );

		return true;
	}
}
