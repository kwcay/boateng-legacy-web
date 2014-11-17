<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class URI
{
	// URI
	private static $_uri	= '';
	private static $_ssl	= false;
	private static $_scheme	= 'http';
	private static $_host	= '';
	private static $_path	= '';
	private static $_query	= '';
	private static $_fragment	= '';
	
	// Array of subdomains
	static $_sub	= array( 'www' );
	
	// Array of query vars
	static $_vars	= array();
	
	public static function init()
	{
		// Performance check
		if (!empty( self::$_uri )) {
			return;
		}
		
		// Get URL
		$url	= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
			$url = 'https://'. $url;
			self::$_ssl	= true;
		} else {
			$url = 'http://'. $url;
			self::$_ssl	= false;
		}
		
		// Parse url
		self::parse( $url );
	}
	
	public static function parse( $url )
	{
		// Save URI
		self::$_uri	= self::clean( $url );
		
		// Parse
		$pieces	= @parse_url( self::$_uri );
		if (!is_array( $pieces )) {
			Error::raiseError(EC_ERROR_CODE, 'Can\'t parse URI');
		}
		
		// Set info
		self::setScheme( @ $pieces['scheme'] );
		self::setHost( @ $pieces['host'] );
		self::setPath( rtrim(@$pieces['path'], '/') );
		self::setQuery( @ $pieces['query'] );
		self::setFragment( @ $pieces['fragment'] );
		
		// Format host and path
		$base	= rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		if (strlen( $base ) > 1) {
			self::$_path	= str_ireplace($base, '', self::$_path);
			self::$_host	.= $base;
		}
		
		// Subdomain
		$sub	= explode( '.', self::$_host );
		$host	= array_pop( $sub );
		if (!empty( $sub )) {
			self::$_sub	= array_reverse( $sub );
		} else {
			self::$_sub	= array( 'www' );
		}
		
		// File format
		$format	= pathinfo(self::$_path, PATHINFO_EXTENSION);
		if ($format) {
			self::$_vars['format']	= strtolower( $format );
			self::$_path	= str_replace('.'. $format, '', self::$_path);
		}
	}
	
	public static function setScheme($scheme) {
		if (!empty( $scheme )) {
			self::$_scheme	= strtolower( $scheme ) .'://';
		}
	}
	
	public static function getScheme() {
		return self::$_scheme;
	}
	
	public static function setHost($host) {
		self::$_host	= strtolower( $host );
	}
	
	public static function getHost() {
		return self::$_host;
	}
	
	public static function setPath($path) {
		self::$_path	= $path;
	}
	
	public static function getPath() {
		return self::$_path;
	}
	
	public static function setQuery($query)
	{
		if (!empty( $query ))
		{
			if (strpos($query, '&amp;') !== false) {
				$query	= str_replace('&amp;', '&', $query);
			}
			
			self::$_query	= '?'. $query;
			parse_str($query, self::$_vars);
		}
	}
	
	public static function getQuery() {
		return self::$_query;
	}
	
	public static function setVars($vars = null)
	{
		$vars	= $vars ? $vars : self::$_vars;
		self::$_vars	= (array) $vars;
		self::$_query	= http_build_query(self::$_vars);
	}
	
	public static function getVars() {
		return self::$_vars;
	}
	
	public static function setFragment($fragment)
	{
		if (!empty( $fragment )) {
			self::$_fragment	= '#'. $fragment;
		}
	}
	
	public static function getFragment() {
		return self::$_fragment;
	}
	
	public static function getSubdomain( $level = null ) {
		return $level ? self::$_sub[$level-1] : self::$_sub;
	}
	
	public static function toString()
	{
		// Get parts to return
		$parts	= func_get_args();
		if ($parts[0] == 'all') {
			$parts	= array('scheme', 'host', 'path', 'query', 'fragment');
		}
		
		// Remove some query variables
		if (in_array('query', $parts))
		{
			if (array_key_exists('style', self::$_vars)) {
				unset(self::$_vars['style']);
				self::setVars();
			}
		}
		
		// Create URI
		$uri = '';
		$uri .= in_array('scheme', $parts)  ? self::getScheme() : '';
		$uri .= in_array('host', $parts)	? self::getHost() : '';
		$uri .= in_array('path', $parts)	? self::getPath() : '';
		$uri .= in_array('query', $parts)	? self::getQuery() : '';
		$uri .= in_array('fragment', $parts)? self::getFragment() : '';
		
		return $uri;
	}
	
	public static function clean( $url )
	{
		$url = urldecode( $url );
		$url = str_replace( '"', '&quot;', $url );
		$url = str_replace( '<', '&lt;', $url );
		$url = str_replace( '>', '&gt;', $url );
		$url = preg_replace( '/eval\((.*)\)/', '', $url );
		$url = preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', $url );
		
		return $url;
	}
}
