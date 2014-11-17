<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


/**
 * Session class
 */
class Session
{
	public static function init()
	{
		// Start session
		self::_start();
		
		Log::mark('Session started.');
	}
	
	private static function _start()
	{
		// Destroy session started by "session.auto_start"
		if ((ini_get('session.auto_start') && session_id())) {
			session_unset();
			session_destroy();
		}
		
		session_start();
	}
	
	// Closes a session
	public static function close() {
		session_write_close();
	}
	
	public static function getToken()
	{
		$token	= self::get('token');
		
		// Create new token
		if (is_null($token))
		{
			self::set('token', md5(session_id()));
			$token	= self::get('token');
		}
		
		return $token;
	}
	
	public static function checkToken()
	{
		// Check that token exists
		$token	= self::get('token', '');
		if (is_null($token) || strlen($token) != 32) {
			return false;
		}
		
		// Check that tokens match
		$sent	= Request::get($token, null, 'MD5');
		return $sent == 1;
	}
	
	public static function get($key, $def = null, $unset = false)
	{
		// Retrieve variable
		$value	= isset($_SESSION[$key]) ? $_SESSION[$key] : $def;
		
		// Unset
		if ($unset) {
			unset($_SESSION[$key]);
		}
		
		// Return value
		return $value;
	}
	
	public static function set($key, $value = null) {
		$old	= self::get($key);
		$_SESSION[$key]	= $value;
		return $old;
	}
}
