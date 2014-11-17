<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


/**
 * Log
 */
class Log
{
	private static $_time;
	private static $_buffer;
	
	public static function init() {
		self::$_time	= self::getTime();
		self::$_buffer	= array();
	}
	
	public static function getTime() {
		list($usec, $sec)	= explode(' ', microtime());
		return ((float)$usec + (float)$sec) * 1000;
	}
	
	public static function mark($event = '')
	{
		$mark	= '';
		if (is_null(self::$_time)) {
			self::init();
		}

		// Add time
		$mark	.= sprintf('%.2fms', self::getTime() - self::$_time);
		
		// Add memory usage
		$mark	.= ', '. sprintf('%0.2f', memory_get_usage() / 1048576) .'MB';
		
		// Add event
		if (empty( $event )) {
			$trace	= debug_backtrace();
			$tfile	= array_shift($trace);
			$tclass	= array_shift($trace);
			$file	= str_ireplace(NKOMO_DIR, '', $tfile['file']);
			$mark	.= ', '. $tclass['class'] .'::'. $tclass['function'] .'() in '. $file .' on line '. $tfile['line'];
		} else {
			$mark	.= ', '. $event;
		}
		
		self::$_buffer[]	= $mark;
	}
	
	public static function dump($oList = false)
	{
		$dump	= $oList ? '<ol>' : '';
		$format	= $oList ? '<li>%s</li>' : "%s\n";
		
		foreach (self::$_buffer as $line) {
			$dump	.= sprintf($format, $line);
		}
		
		return $dump .($oList ? '</ol>' : '');
	}
	
	public static function dumpAndExit() {
		exit( '<pre>'. print_r( self::dump(), true ) .'</pre>' );
	}
}
