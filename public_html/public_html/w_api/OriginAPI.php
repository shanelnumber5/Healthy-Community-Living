<?php
/**
 * OriginAPI provides helper methods for performing requests to Origin and the Origin's API
 *
 * @package Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-07-01
 * @copyright 2014 Weebly, Inc
 */
class OriginAPI
{
	/**
	 * Origin HMAC hash algorithm
	 */
	const ORIGIN_HASH_ALGORITHM = 'SHA256';

	/**
	 * Origin method prefix
	 */
	const METHOD_PREFIX = 'DeployedServices::';

	/**
	 * @var $curlHandler
	 */
	private static $curlHandler = NULL;

	/**
	 * Our default cURL options, merged with details of a given request
	 *
	 * @var $curlOptions
	 */
	private static $defaultCurlOptions = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => false,
		CURLOPT_CONNECTTIMEOUT => 2,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_POST => true,
		CURLOPT_USERAGENT => 'odysseus0/1'
	);

	/**
	 * Generates a signed request to the Origin API
	 * This function matches functionality on the API side to ensure hash consistency
	 *
	 * @param string
	 *
	 * @return string
	 */
	public static function generateSignedRequest( $string )
	{
		return hash_hmac( self::ORIGIN_HASH_ALGORITHM, $string, Configuration::RESELLER_REQUEST_SECRET );
	}

	/**
	 * Generates a string to be used as the request object for requests to origin
	 *
	 * @param string $method
	 * @param array $body
	 *
	 * @return string
	 */
	public static function generateRequestBody( $method, array $body )
	{
		if ($body['request'] && $body['request']['headers'] && $body['request']['headers']['Cookie']) {
			$cookie = $body['request']['headers']['Cookie'];
			$cookie = preg_replace('/_sp_ses.[^.]{4}=[^;]*[;$]?/', '', $cookie);
			$cookie = preg_replace('/_sp_id.[^.]{4}=[^;]*[;$]?/', '', $cookie);
			$cookie = preg_replace('/_snow_ses.[^.]{4}=[^;]*[;$]?/', '', $cookie);
			$cookie = preg_replace('/_snow_id.[^.]{4}=[^;]*[;$]?/', '', $cookie);
			$body['request']['headers']['Cookie'] = $cookie;
		}

		ksort( $body, SORT_REGULAR );

		$request = array(
			'id' => '0',
			'jsonrpc' => '2.0',
			'method' => self::METHOD_PREFIX . $method,
			'params' => $body
		);

		return json_encode( $request );
	}

	/**
	 * Performs a request to the remote API with the given request body
	 *
	 * @param string $method
	 * @param array $body
	 *
	 * @return instanceof OriginAPIResponse
	 */
	public static function makeRequest( $method, $body )
	{
		$curl = self::getCurlHandler( );

		// copy a backup in case we need to go download raw object.
		$copyBody = $body;

		if ($body['request']['path'] === '/gdpr/gdprscript.js') {
			// OK, THIS IS A GDPR BANNER REQUEST
			// we need to include the IP of the origin user so that the monolith can properly determine
			// whether or not that user is in the EU or not.
			if ($body['request']['query']) {
				$body['request']['query'] .= '&ip=' . $_SERVER['REMOTE_ADDR'];
			} else {
				$body['request']['query'] = 'ip=' . $_SERVER['REMOTE_ADDR'];
			}
		}

		$body = self::generateRequestBody( $method, $body );

		$options = array(
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_URL => Configuration::ORIGIN_API_ENDPOINT,
			CURLOPT_HTTPHEADER => array(
				'Content-type: application/json',
				'X-Deployed-Hostname: ' . Configuration::DEPLOYED_HOSTNAME,
				'X-Signed-Request-Hash: ' . self::generateSignedRequest( $body ),
				'X-Repository: ' . Configuration::DEPLOYED_REPOSITORY,
				'X-Deployed-Version: ' . Configuration::BUILDDATE
			)
		);

		curl_setopt_array( $curl, $options + self::$defaultCurlOptions );

		$response = curl_exec( $curl );

		if ( \Configuration::ERROR_REPORTING_LEVEL === \Configuration::ERROR_LEVEL_DEBUG_API )
		{
			\Configuration::handleError( array( $response ) );
		}

		$info = curl_getinfo( $curl );
		$error = curl_error( $curl );

		$response = new OriginAPIResponse( $response, $info, $error );

		if (isset($response->response->retryRaw) && $response->response->retryRaw === true) {
			// prepare checksum for getLargeObject() to verify
			$checksum = false;
			$checksumAlgo = false;
			if (!empty($response->response->checksum) && !empty($response->response->checksumAlgo)) {
				$checksum = $response->response->checksum;
				$checksumAlgo = $response->response->checksumAlgo;
			}
			$largeObjResponse = self::getLargeObject("getRawObject", $copyBody, $checksum, $checksumAlgo);
			if ($largeObjResponse === false) {
				return false;
			}
		}

		return $response;
	}

	/**
	 * @param $method
	 * @param $body
	 */
	public static function getLargeObject($method, $body, $checksum = false, $checksumAlgo = false)
	{
		ini_set('max_execution_time', 0);

		// get file destination path
		$filePath = \BASE_DOCROOT_DIR . $body['request']['path'];
		// get temp file desintation path based on file path and timestamp
		$tempFilePath = $filePath . '.' . time() . '.tmp.' .  Configuration::BUILDDATE . '.tmp';

		// open temp file for buffer loading
		$fp = fopen($tempFilePath, 'w+');

		$curl = self::getCurlHandler( );

		$body = self::generateRequestBody($method, $body);

		register_shutdown_function(
			array('OriginAPI', 'cleanUpTempFiles'),
			$fp,
			$tempFilePath,
			$filePath,
			$checksum,
			$checksumAlgo,
			$body
		);

		$options = array(
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_URL => Configuration::ORIGIN_API_ENDPOINT,
			CURLOPT_HTTPHEADER => array(
				'Content-type: application/json',
				'X-Deployed-Hostname: ' . Configuration::DEPLOYED_HOSTNAME,
				'X-Signed-Request-Hash: ' . self::generateSignedRequest( $body ),
				'X-Repository: ' . Configuration::DEPLOYED_REPOSITORY,
				'X-Deployed-Version: ' . Configuration::BUILDDATE
			),
			CURLOPT_TIMEOUT => 0,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_WRITEFUNCTION => function( $curl, $string ) use ($fp) {
				fwrite($fp, $string);
				echo $string;
				ob_flush();
				flush();
				return strlen($string);
			},
			CURLOPT_HEADERFUNCTION => function( $curl, $header ) {
				header( $header );
				return strlen( $header );
			},
		);

		curl_setopt_array( $curl, $options + self::$defaultCurlOptions );

		$response = curl_exec( $curl );

		$info = curl_getinfo( $curl );
		$error = curl_error( $curl );

		// make sure optional parameter $body is not inputted so it will not go infinite recursion loop.
		return self::cleanUpTempFiles($fp, $tempFilePath, $filePath, $checksum, $checksumAlgo);
	}

	/**
	 * Shutdown function for getLargeObject()
	 * Will clean up file handler and temp file if needed.
	 *
	 * @param resource $fp
	 * @param string $tempFilePath
	 * @param string $filePath
	 * @param string $checksum
	 * @param string $checksumAlgo
	 * @param array|bool $body
	 */
	private static function cleanUpTempFiles($fp, $tempFilePath, $filePath, $checksum, $checksumAlgo, $body = null)
	{
		fclose($fp);

		// if no checksum to match, rename to new file.
		// this case only caused by undesigned situation.
		if ($checksum === false || $checksumAlgo === false) {
			if (file_exists($tempFilePath)) {
				rename($tempFilePath, $filePath);
				return true;
			}
		}

		// check downloaded file checksum,
		$tempFileChecksum = hash_file($checksumAlgo, $tempFilePath);
		if ($tempFileChecksum == $checksum) {
			// if checksun match, rename to finalized file if that file not exist or different.
			if (file_exists($filePath)) {
				$existFileChecksum = hash_file($checksumAlgo, $filePath);
				if ($tempFileChecksum === $existFileChecksum) {
					// if destination file already exists (caused by other client's access),
					// and if destination file has same checksum.
					// remove temp file because everything is good.
					unlink($tempFilePath);
					return true;
				}
			}
			// rename to finalized file if that file not exist or different.
			rename($tempFilePath, $filePath);
			return true;
		}

		// if temp file checksum doesn't match, mostly because client has terminated his process.
		// re-try download once more in shutdown function.
		// recursion termination condition: $body = null (optional parameter's default value)
		if (!is_null($body)) {
			// open temp file for buffer loading
			$fp = fopen($tempFilePath, 'w+');

			// seems self::getCurlHandler() doesn't work inside shutdown_funciton.
			$curl = curl_init();

			// $body is already well configured when coming into this function.
			$options = array(
				CURLOPT_POSTFIELDS => $body,
				CURLOPT_URL => Configuration::ORIGIN_API_ENDPOINT,
				CURLOPT_HTTPHEADER => array(
					'Content-type: application/json',
					'X-Deployed-Hostname: ' . Configuration::DEPLOYED_HOSTNAME,
					'X-Signed-Request-Hash: ' . self::generateSignedRequest( $body ),
					'X-Repository: ' . Configuration::DEPLOYED_REPOSITORY,
					'X-Deployed-Version: ' . Configuration::BUILDDATE
				),
				CURLOPT_TIMEOUT => 300,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FILE => $fp,
			);

			curl_setopt_array( $curl, $options + self::$defaultCurlOptions );

			$response = curl_exec( $curl );

			$info = curl_getinfo( $curl );
			$error = curl_error( $curl );

			curl_close($curl);

			// make sure optional parameter $body is not inputted so it will not go infinite recursion loop.
			return self::cleanUpTempFiles($fp, $tempFilePath, $filePath, $checksum, $checksumAlgo);
		} else {
			unlink($tempFilePath);
			return false;
		}
	}

	/**
	 * Retrieve an instance of the cURL handler. If a resource does not exist, create one.
	 *
	 * @return resource cURL
	 */
	private static function getCurlHandler( )
	{
		if ( isset( self::$curlHandler ) === false )
		{
			self::$curlHandler = curl_init( );
			register_shutdown_function(
				array(
					'\OriginAPI',
					'closeCurlHandler'
				)
			);
		}

		return self::$curlHandler;
	}

	/**
	 * Closes the cURL handler on shutdown
	 *
	 * @return void
	 */
	public static function closeCurlHandler( )
	{
		if ( isset( self::$curlHandler ) === false )
		{
			return;
		}

		curl_close( self::$curlHandler );
	}

	/**
	 * Proxies a client API request back to Origin, returning the full result of the request to the client via \Output
	 *
	 * @param array $request
	 * @param mixed $post
	 *
	 * @return void
	 */
	public static function makeClientAPIRequest( $request, $post )
	{
		$headers = array( );

		foreach ( $request['headers'] as $header => $value )
		{
			if ( $header === 'Host' && $value !== Configuration::DEPLOYED_HOSTNAME )
			{
				$value = Configuration::DEPLOYED_HOSTNAME;
			}

			/**
			 * Let cURL handle Content-Type and Content-Length, as they may have changed from the browser to our parsing
			 */
			if (strpos($request['path'], '/app/store/api/') === 0) {
				// it's chamber, just proxy the header forward.
				$headers[] = $header . ': ' . $value;
			} else {
				if (stripos($header, 'Content-Type') === false && stripos($header, 'Content-Length') === false) {
					$headers[] = $header . ': ' . $value;
				}
			}
		}

		$curl = self::getCurlHandler( );

		// Prepare the endpoint URL.
		$url = Configuration::CLIENT_API_ENDPOINT . ltrim($request['path'], '/');

		// Add a trailing slash to the URL if one does not already exist.
		// This is required specifically for Membership RPC calls, which fail if the trailing slash is not there.
		if (substr($url, -1) !== '/') {
			$url .= '/';
		}

		// Add query string to the URL if one exists.
		if (isset($request['query'])) {
			$url .= '?' . $request['query'];
		}

		$options = array(
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_URL => $url,
			CURLOPT_HEADERFUNCTION => function( $curl, $header ) {
				if ( strpos( $header, 'Content-Length' ) === false && strpos( $header, 'Transfer-Encoding:' ) === false )
				{
					Output::sendHeader( $header );
				}
				return strlen( $header );
			}
		);

		// decide if use HTTP_POST or HTTP_GET
		if (isset($request['method']) && $request['method'] === 'POST') {
			$options[CURLOPT_POSTFIELDS] = ( is_array( $post ) === true ) ? http_build_query( $post ) : $post;
		} else {
			$options[CURLOPT_POST] = false;
			$options[CURLOPT_URL] = $options[CURLOPT_URL];

			if (isset($request['method']) && $request['method'] === 'OPTIONS') {
				$options[CURLOPT_CUSTOMREQUEST] = 'OPTIONS';
			}
		}

		curl_setopt_array( $curl, $options + self::$defaultCurlOptions );
		$response = curl_exec( $curl );

		$response = str_replace( array( '\n', '\t' ), '', $response );

		Output::render( $response );
	}
}

/**
 * OriginAPIResponse is the resource returned on every response from the API
 *
 * @package Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-07-08
 * @copyright 2014 Weebly, Inc
 */
class OriginAPIResponse
{
	/**
	 * @var $response
	 */
	public $response;

	/**
	 * Constructor receives details of the request
	 *
	 * @param mixed $response
	 * @param mixed $info
	 * @param mixed $error
	 *
	 * @return void
	 */
	public function __construct( $response, $info, $error )
	{
		$response = json_decode( $response );

		if ( $response === false || isset( $response ) === false )
		{
			$this->handleAPIError( $response, $info, $error );
			return;
		}

		if ( isset( $response->result ) === true )
		{
			$this->response = $response->result;
		}
	}

	/**
	 * Handles errors which may occur from API requests
	 *
	 * @param mixed $response
	 * @param mixed $info
	 * @param mixed $error
	 *
	 * @return void
	 */
	private function handleAPIError( $response, $info, $error )
	{
		$this->response = new \stdClass( );

		$this->response->success = false;
		$this->response->error = $error;
		$this->response->info = $info;

		Configuration::handleError(
			array(
				'errorType' => 'api',
				'errorData' => array(
					'error' => $this->response->error,
					'info' => $this->response->info
				)
			)
		);
	}
}
