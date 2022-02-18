<?php
/**
 * Output handles generating and displaying rendered (or not) output as a result of the current request
 *
 * @package Weebly
 * @subpackage ResellerServices
 * @author Dustin Doiron <dustin@weebly.com>
 * @since 2014-07-01
 * @copyright 2014 Weebly, Inc
 */
class Output
{
	/**
	 * Renders a 404 page for the given host for the current request
	 *
	 * @param bool $retry Retry is set when a new 404 page is retreived from Origin, and we need to perform a recursive call
	 *	in order to serve the new 404 page.
	 *
	 * @return void
	 */
	public static function render404( $retry = false )
	{
		if ( file_exists( \BASE_DOCROOT_DIR . '/404.html' ) === true )
		{
			self::sendHeader( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );
			self::render(
				file_get_contents( \BASE_DOCROOT_DIR . '/404.html' )
			);
		}

		elseif ( $retry === false )
		{
			/**
			 * Might need to retrieve the 404 page from origin, but this should be rare
			 */
			$origin = OriginRequest::getObject(array('path' => '/404.html', 'file' => '404.html'));

			if ( $origin === true )
			{
				self::render404( true );
			}
		}

		/**
		 * Something's wrong, and we can't serve a 404 from origin, and we don't have a 404 page on disk.
		 * Fail gracefully to minimal output.
		 */
		self::sendHeader( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );
		self::sendHeader( 'Cache-Control: no-cache' );
		self::render( 'Not Found' );
	}

	/**
	 * Sends the designated header as a response to the current request
	 *
	 * @param string $header
	 *
	 * @return void
	 */
	public static function sendHeader( $header )
	{
		header( $header, (strpos($header, 'Set-Cookie') === false) );
	}

	/**
	 * Sends the given text/html output as a response to the current request
	 * A call to render is considered the last method called in a given request, therefore, render exit's after output.
	 * We don't need to set a lot of the headers, since Apache handles that for us on output.
	 *
	 * @param string $output
	 *
	 * @return void
	 */
	public static function render( $output )
	{
		self::sendHeader( 'X-DS-Version: ' . \Configuration::BUILDDATE );
		echo $output;
		exit( );
	}

	/**
	 * Decodes a wire object by performing a base64_decode
	 *
	 * @param string $object
	 *
	 * @return string
	 */
	public static function decodeWireObject( $object )
	{
		return base64_decode( $object );
	}
}
