<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


/**
 * Error class
 */
class Error
{
	const EC_ERROR_CODE			= 0;
	const EC_MOVED_PERMANENTLY	= 300;
	const EC_MOVED_TEMPORARILY	= 301;
	const EC_BAD_REQUEST			= 400;
	const EC_UNAUTHORIZED		= 401;
	const EC_FORBIDDEN			= 403;
	const EC_NOT_FOUND			= 404;
	const EC_SERVER_ERROR		= 500;
	const EC_NOT_IMPLEMENTED		= 501;
	const EC_SERVICE_UNAVAILABLE	= 503;
	
	// Raise error
	public static function raiseError($code, $msg = '')
	{
		// Clear buffer
		//Helper::clearBuffer();
		
		// Log data
		$log	= '';
		if (IS_LOCAL && Config::DEBUG) {
			$log	= Log::dump(true);
		}
		
		// Format display
		$output	= @file_get_contents(PATH_HTML.DS.'error.html');
		$output	= str_replace('[i:code]', $code, $output);
		$output	= str_replace('[i:msg]', $msg, $output);
		$output	= str_replace('[i:log]', $log, $output);
		
		// Show error message
		echo $output;
		exit();
	}
}
