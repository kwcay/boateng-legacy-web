<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class Response
{
	private static $_cache		= false;
	private static $_headers	= array();
	private static $_content	= array();
	
	private static $_renderer;
	private static $_handler;
	
	
	public static function allowCache($change = null)
	{
		if (!is_null( $change )) {
			self::$_cache	= (bool) $change;
		}
		
		return self::$_cache;
	}
	
	/*
	 * Set a header
	 */
	public static function setHeader($name, $value, $replace = false)
	{
		$name	= (string) $name;
		$value	= (string) $value;
		if ($replace) {
			foreach (self::$_headers as $key => $header) {
				if ($name == $header['name']) {
					unset(self::$_headers[$key]);
				}
			}
		}
		
		// Save header
		self::$_headers[]	= array(
			'name'	=> $name,
			'value'	=> $value
		);
	}

	/*
	 * Return array of headers
	 */
	public static function getHeaders() {
		return self::$_headers;
	}

	/*
	 * Delete all headers
	 */
	public static function clearHeaders() {
		self::$_headers = array();
	}

	/*
	 * Send all headers
	 */
	public static function sendHeaders()
	{
		if (!headers_sent())
		{
			foreach (self::$_headers as $header)
			{
				if (strtolower($header['name']) == 'status') {
					header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int) $header['value']);
				} else {
					header($header['name'] . ': ' . $header['value']);
				}
			}
		}
	}

	// Set page content
	public static function setContent($content) {
		self::$_content	= array((string) $content);
	}
	
	public static function setContentFromRenderer() {
		self::setContent( self::$_renderer->render() );
	}

	public static function prependBody($content) {
		array_unshift(self::$_content, (string) $content);
	}
	public static function appendBody($content) {
		array_push(self::$_content, (string) $content);
	}

	// Retrieve page content
	public static function getContent($toArray = false)
	{
		if ($toArray) {
			return self::$_content;
		}
		
		ob_start();
		foreach (self::$_content as $content) {
			echo $content;
		}
		return ob_get_clean();
	}
	
	/*
	 * Page renderer
	 */
	public static function setRenderer($renderer) {
		self::$_renderer	= & $renderer;
	}
	public static function &getRenderer() {
		return self::$_renderer;
	}
	
	/*
	 * Content handler
	 */
	public static function setHandler($handler) {
		self::$_handler	= & $handler;
	}
	public static function &getHandler() {
		return self::$_handler;
	}

	/*
	 * Sends headers, then data
	 */
	public static function toString($compress = false)
	{
		// HTML compactor
		if (!IS_LOCAL)
		{
			// Compress using JSMin and Compactor
			Library::import('jsmin:jsmin');
			Library::import('compactor:compactor');
			new \Compactor();
		}
		
		// HTML data
		$data	= self::getContent();
		
		// Check to see if server is compressing
		if ($compress && !ini_get('zlib.output_compression') && ini_get('output_handler') != 'ob_gzhandler') {
			$data	= self::_compress($data);
		}
		
		// Set browser caching
		if (self::allowCache() === false)
		{
			self::setHeader( 'Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true );
			self::setHeader( 'Last-Modified', gmdate('D, d M Y H:i:s') .' GMT', true );
			self::setHeader( 'Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false );
			self::setHeader( 'Pragma', 'no-cache' );
		}
		
		Log::mark();
		
		// Send data
		self::sendHeaders();
		return $data;
	}

	/*
	* Compress data
	*/
	private static function _compress( $data )
	{
		$encoding	= self::_clientEncoding();
		if (!$encoding) {
			return $data;
		}
		
		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}
		
		if (headers_sent()) {
			return $data;
		}
		
		if (connection_status() !== 0) {
			return $data;
		}
		
		$level	= 4;	//ideal level
		$gzdata	= gzencode($data, $level);
		self::setHeader('Content-Encoding', $encoding);
		self::setHeader('X-Content-Encoded-By', 'FYA');
		
		Log::mark();
		
		return $gzdata;
	}

	/*
	* Check, whether client supports compressed data
	*/
	private static function _clientEncoding()
	{
		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			return false;
		}
		
		$encoding = false;
		if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
			$encoding = 'gzip';
		}
		
		if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip')) {
			$encoding = 'x-gzip';
		}
		
		return $encoding;
	}
	
	/*
	 * Filter shortcuts
	 * 
	 */
	public static function filter($str, $type = '')
	{
		Log::mark('Response::filter deprecated');
		Library::import('utilities:filter');
		
		return Filter::input($str, $type);
	}
}
