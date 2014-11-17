<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class Request
{
	public static function fetch()
	{
		URI::init();
		
		// Set GET variables
		$vars	= URI::getVars();
		if (!empty( $vars )) {
			self::setVars($vars, 'GET');
		}
	}
	
	public static function getMethod() {
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}
	
	public static function getFormat($default = 'html') {
		return self::get('format', $default, 'ALNUM', 'GET');
	}
	
	public static function get($name, $default = null, $type = 'none', $hash = 'default')
	{
		// Get value
		$input	= & self::getInputHash( $hash );
		if (isset($input[$name]) && !is_null($input[$name])) {
			$value	= Filter::input($input[$name], $type);
		}
		
		// Default value
		else {
			$value	= $default;
		}
		
		return $value;
	}
	
	public static function set($name, $value = null, $hash = 'method', $overwrite = true)
	{
		if (!$overwrite && array_key_exists($name, $_REQUEST)) {
			return $_REQUEST[$name];
		}
		
		$hash	= strtoupper( $hash );
		if ($hash === 'METHOD') {
			$hash	= strtoupper($_SERVER['REQUEST_METHOD']);
		}
		$previous	= array_key_exists($name, $_REQUEST) ? $_REQUEST[$name] : null;
		
		switch ( $hash )
		{
			case 'GET':
				$_GET[$name]		= $value;
				$_REQUEST[$name]	= $value;
				break;
			
			case 'POST':
				$_POST[$name]		= $value;
				$_REQUEST[$name]	= $value;
				break;
			
			case 'COOKIE':
				$_COOKIE[$name]		= $value;
				$_REQUEST[$name]	= $value;
				break;
			
			case 'FILES':
				$_FILES[$name]		= $value;
				break;
			
			case 'ENV':
				$_ENV[$name]		= $value;
				break;
			
			case 'SERVER':
				$_SERVER[$name]		= $value;
				break;
		}
		
		return $previous;
	}
	
	public static function setVars($vars, $hash = 'get')
	{
		if (is_array( $vars ))
		{
			foreach ($vars as $k => $v) {
				self::set( $k, $v, $hash );
			}
		}
	}
	
	public static function &getInputHash( $hash )
	{
		$hash	= strtoupper( $hash );
		if ($hash === 'METHOD') {
			$hash	= strtoupper($_SERVER['REQUEST_METHOD']);
		}
		
		switch ($hash)
		{
			case 'GET':
				$input	= &$_GET;
				break;
			
			case 'POST':
				$input	= &$_POST;
				break;
			
			case 'FILES':
				$input	= &$_FILES;
				break;
			
			case 'COOKIE':
				$input	= &$_COOKIE;
				break;
			
			case 'ENV':
				$input	= &$_ENV;
				break;
			
			case 'SERVER':
				$input	= &$_SERVER;
				break;
			
			default:
				$input	= &$_REQUEST;
				break;
		}
		
		return $input;
	}
}

