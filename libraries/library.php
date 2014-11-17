<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class Library
{
	public static function import( $lib )
	{
		// Include all library files in a folder
		if (substr($lib, strlen($lib) - 1) == '*')
		{
			$prefix	= substr($lib, 0, strlen($lib) - 2);
			$dir	= PATH_LIB.DS.str_replace(':', DS, $prefix);
			
			if (is_dir( $dir ))
			{
				$files	= scandir($dir);
				foreach ($files as $file)
				{
					// Only include PHP libraries
					if (substr($file, strlen($file) - 4) == '.php') {
						self::import($prefix .':'. substr($file, 0, strlen($file) - 4));
					}
				}
			}
			
			// Raise error if folder doesn't exist
			else {
				Log::mark('Missing library folder "'. $lib .'"');
				Error::raiseError(Error::EC_ERROR_CODE, 'Missing library folder');
			}
		}
		
		// Include single library file
		else
		{
			$file	= PATH_LIB.DS.str_replace(':', DS, $lib).'.php';
			if (is_file( $file )) {
				require_once( $file );
			}
			
			// Raise error if file doesn't exist
			else {
				Log::mark('Missing library file "'. $lib .'"');
				Error::raiseError(Error::EC_ERROR_CODE, 'Missing library file');
			}
		}
		
		Log::mark('Library "'. str_replace(':', '.', $lib) .'" imported.');
	}
}

