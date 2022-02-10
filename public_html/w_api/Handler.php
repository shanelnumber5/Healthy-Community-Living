<?php
/**
 * Handler performs request handling and file proxying via stepping in front of the 404 handler
 *
 * @package Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-07-01
 * @copyright 2014 Weebly, Inc
 */
require_once( __DIR__ . '/Bootstrap.php' );

define("MEMORY_LIMIT", 1048576);

class Handler
{
	/**
	 * @var $request
	 */
	private $request = NULL;

	/**
	 * @var $site
	 */
	private $site = NULL;

	/**
	 * @var boolean
	 */
	private $isRedirect = false;

	/**
	 * Constructor
	 * On construct, Handler runs through everything needed to either render the requested page, or render a 404
	 * No methods are called outside of this class, and no values are returned
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	public function __construct( $request )
	{
		$this->buildSiteArray( );
		$this->buildRequestArray( $request );

		/**
		 * Is this an API request to a client API that we need to proxy along?
		 */
		if ( $this->isClientApiRequest( ) === true )
		{
			\OriginAPI::makeClientAPIRequest( $this->request, file_get_contents( 'php://input' ) );
		}

		if ( $this->isPage( ) === true )
		{
			/**
			 * Might have the mobile cookie, and just need to get redirected to mobile file on disk
			 * For dynamic mobile page, always let dynamic page handle it.
			 */
			if (
				$this->request['mobile'] === true &&
				file_exists( \BASE_DOCROOT_DIR . '/mobile/' . $this->request['file'] ) === true
			) {
				\setcookie( 'is_mobile', 1, time( ) + 2592000, '/' );
				if ($this->isDynamicPage() === false) {
					\Output::sendHeader('Location: ' . $this->request['file']);
					$this->isRedirect = true;
					$this->finalizeOutput();
					exit();
				}
			}

			/**
			 * Do we have it in the page hierarchy, or is it a dynamic page? Go get it from Origin
			 */
			if ($this->isPageInPublishedData( $this->request['file'] ) === true || $this->isDynamicPage( ) === true )
			{
				$this->isDynamicStandardPage(); // update request with isDynamic if needed
				$response = \OriginRequest::getObject( $this->request );
				$this->handleOriginResponse( $response );
			} else {
				\Output::render404( );
			}

			/**
			 * Should we try a simple redirect from .htm to .html?
			 */
			if ( $this->isPageInPublishedData( $this->request['file'] . 'l' ) === true )
			{
				if ( file_exists( \BASE_DOCROOT_DIR . '/' . $this->request['file'] ) === true )
				{
					\Output::sendHeader( 'Location: ' . $this->request['file'] . 'l' );
					$this->isRedirect = true;
					$this->finalizeOutput();
					exit( );
				}
				else
				{
					/**
					 * Don't have it yet, go get it before forwarding
					 */
					$this->request['file'] .= 'l';
					$this->isDynamicStandardPage(); // update request with isDynamic if needed
					$response = \OriginRequest::getObject( $this->request );
					$this->handleOriginResponse( $response );
				}
			}
		}
		else
		{
			/**
			 * Not a page, we have to use benefit of the doubt here for checking origin
			 */
			// Assets files.
			// Set default memory_limit so wServer will send back retryRaw message for file larger than 1MB.
			// Not setting it means it's remote server published before this change,
			// then wServer will always return old type of response. (no retryRaw mechanism)
			$this->request['memory_limit'] = MEMORY_LIMIT;
			$response = \OriginRequest::getObject( $this->request );
			$this->handleOriginResponse( $response );
		}

		// if redirect header, then add text to prevent some ftp server's firewall from block pure header redirect.
		$this->finalizeOutput();
	}

	/**
	 * Builds the site array for the current request
	 *
	 * @return void
	 */
	private function buildSiteArray( )
	{
		if ( file_exists( \BASE_SERVICES_DIR . '/' . Configuration::PUBLISHED_DATA_LOCATION ) === true )
		{
			$this->site = json_decode( file_get_contents( \BASE_SERVICES_DIR . '/' . Configuration::PUBLISHED_DATA_LOCATION ), true );
		}
	}

	/**
	 * Builds the request data array for the current request
	 * During one of the build cases, we may redirect out to the properly formed .html location
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	private function buildRequestArray( $request )
	{
		// Better detection of HTTPS
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == '1')) {
			$_SERVER['REQUEST_SCHEME'] = 'https';
		} elseif (!isset($_SERVER['REQUEST_SCHEME'])) {
			$_SERVER['REQUEST_SCHEME'] = 'http';
		}

		$this->request = parse_url( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] );

		// Always trim the trailing slash.
		$this->request['path'] = rtrim($this->request['path'], '/');
		$this->request['directories'] = explode('/', trim( $this->request['path'], '/' ));
		$this->request['headers'] = self::getHeaders( );
		$this->request['method'] = $_SERVER['REQUEST_METHOD'];

		if ( count( $this->request['directories'] ) > 1 )
		{
			$this->request['file'] = array_pop( $this->request['directories'] );
		}
		else
		{
			unset( $this->request['directories'] );
			$this->request['file'] = trim($this->request['path'], '/');
		}

		if ( strpos( $this->request['file'], '.' ) === false )
		{
			/**
			 * Is this a redirect to a .html file?
			 */
			$file = trim($this->request['path'], '/' ) . '.html';

			if ( $this->isPageInPublishedData( $file ) === true )
			{
				\Output::sendHeader( 'Location: /' . $file );
				$this->isRedirect = true;
				$this->finalizeOutput();
				exit( );
			}

			if ( $this->request['file'] !== '' )
			{
				$this->request['directories'][] = $this->request['file'];
			}

			$this->request['file'] = 'index.html';

			if ( $this->request['path'] === '/' )
			{
				$this->request['path'] = $this->request['path'] . $this->request['file'];
			}
		}

		$this->request['ua'] = $_SERVER['HTTP_USER_AGENT'];
		$this->request['mobile'] = ( ( isset( $_COOKIE['disable_mobile'] ) === false || $_COOKIE['disable_mobile'] === '0' ) &&
			( isset( $_COOKIE['is_mobile'] ) && $_COOKIE['is_mobile'] !== '0' || isset( $this->request['directories'] ) && $this->request['directories'][0] === 'mobile' ) );
	}

	/**
	 * Handles a response from Origin, generally the last item in the lifecycle of a request
	 *
	 * @param mixed $response
	 *
	 * @return void
	 */
	private function handleOriginResponse( $response )
	{
		/**
		 * Origin didn't have the object
		 */
		if ( $response === false )
		{
			\Output::render404( );
		}

		/**
		 * We're good to go, start rendering
		 */
		\Output::sendHeader( $_SERVER['SERVER_PROTOCOL'] . ' 200 OK' );

		/**
		 * Origin had the object, and it's now stored on disk, render the stored object
		 */
		if ( $response === true )
		{
			if ( isset( $_COOKIE['is_redirecting'] ) === true )
			{
				sleep( 2 );
			}

			\setcookie( 'is_redirecting', 1, time( ) + 5 );
			\Output::sendHeader( 'Location: ' . $this->request['path'] );
			$this->isRedirect = true;
		}

		if ( is_object( $response ) === true )
		{
			/**
			 * It's a streaming object, so we'll render it from here
			 */
			\Output::sendHeader( $_SERVER['SERVER_PROTOCOL'] . ' 200 OK' );

			if ($response->type && $response->type === 'js') {
				\Output::sendHeader('Content-Type: text/javascript;');
			}

			\Output::render( \Output::decodeWireObject( $response->object ) );
		}
	}

	/**
	 * Determines if the current request is to a page
	 *
	 * @return bool
	 */
	private function isPage( )
	{
		/**
		 * The only pages with directories are mobile pages and dynamic pages (commerce & blog)
		 */
		if( isset( $this->request['directories'] ) === true
			&& count( $this->request['directories'] ) > 0
			&& $this->request['directories'] !== 'mobile' && $this->isDynamicPage( ) === false
		)
		{
			$this->request['isPage'] = false;
			return false;
		}

		if ( preg_match( '/.html\Z/', $this->request['file'] ) > 0 || preg_match( '/.htm\Z/', $this->request['file'] ) > 0 )
		{
			$this->request['isPage'] = true;
			return true;
		}

		$this->request['isPage'] = false;
		return false;
	}

	/**
	 * Uses base directory to determine if the a page is a dynamic page (blog & commerce)
	 * These pages are not always in published data and therefore require directory checking
	 *
	 * @return bool
	 */
	private function isDynamicPage()
	{
		if ( isset( $this->request['directories'] ) === true && count( $this->request['directories'] ) > 0 )
		{
			/**
			 * Check if either the base directory is a store or a call to a file in the apps folder
			 * or if it is the base directory for a blog (meaning the base directory is also a file in published data)
			 */
			if ( $this->isDynamicRoute( $this->request['directories'][0] ) ||
				( is_numeric( $this->request['directories'][0] ) === true )
			)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a standard page should be considered dynamic, thus not cached.
	 * i.e. Standard page with commerce element, we want to keep commerce data up to date,
	 *     So we can't allow odysseus to cache the page, serving outdated commerce data.
	 *
	 * @return bool
	 */
	private function isDynamicStandardPage()
	{
		if ($this->isDynamicRoute(ltrim($this->request['path'], '/'))) {
			// page containing commerce element is considered dynamic page here,
			// so odysseus don't cache it.
			// so commerce data can stay up to date.

			// we add a isDynamic in request.
			// it will tell DeployedServiceController to return in dynamic page's format instead.
			$this->request['isDynamic'] = true;
			return true;
		}
		return false;
	}

	/**
	 * Determines if the first directory in the request is a known "dynamic" endpoint
	 *
	 * @param string $directory
	 *
	 * @return bool
	 */
	private function isDynamicRoute( $directory )
	{
		if (starts_with_any($directory, array('store', 'blog', 'apps', 'gdpr', '.well-known'))) {
			return true;
		}

		return (isset($this->site['dynamic']) && isset($this->site['dynamic'][$directory]));
	}

	/**
	 * Determines if the current page (by filename) is in the published site data hierarchy
	 *
	 * @param string $page
	 *
	 * @return bool
	 */
	private function isPageInPublishedData( $page )
	{
		if ( isset( $page ) === false )
		{
			$page = $this->request['file'];
		}

		return in_array( $page, $this->site['pages'] );
	}

	/**
	 * Determines if the current request is a client API related request
	 *
	 * @return bool
	 */
	private function isClientApiRequest( )
	{
		if ( strpos( $this->request['path'], '/ajax/' ) === 0 )
		{
			return true;
		}

		if (strpos($this->request['path'], '/app/store/api/') === 0) {
			return true;
		}

		return false;
	}

	/**
	 * Attempts to retrieve the HTTP headers from the current request
	 *
	 * @return array|bool
	 */
	private static function getHeaders( )
	{
		if ( \function_exists( 'apache_request_headers' ) === true )
		{
			return \apache_request_headers( );
		}

		foreach ( $_SERVER as $key => $value )
		{
			if ( substr( $key, 0, 5 ) === 'HTTP_' )
			{
				$headers[str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $key, 5 ) ) ) ) )] = $value;
			}
			elseif ( $key === 'CONTENT_TYPE' || $key === 'CONTENT_LENGTH' )
			{
				$headers[str_replace( '_', '-', ucwords( strtolower( $key ) ) )] = $value;
			}
		}

		if ( isset(  $headers ) === true )
		{
			return $headers;
		}

		return false;
	}

	/**
	 * Output some content if the page has redirect header.
	 *
	 * This is used to prevent some FTP (i.e. fatcow.com) has firewall not allow empty content redirect header.
	 *
	 */
	private function finalizeOutput()
	{
		if ($this->isRedirect === true) {
			// this shouldn't appear, as redirect header would take care of it.
			// in case it's seen, reload link would help user to manually reload/redirect the page.
			echo "<a onclick='location.reload()'>click here to reload the page.</a>";
		}
	}
}

$handler = new Handler( $_REQUEST );
