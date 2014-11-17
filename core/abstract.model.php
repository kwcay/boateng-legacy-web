<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


abstract class Model
{
	public function __construct()
	{
	
	}
	
	/*
	 * Retrieves contents of a file
	 */
	protected function getContents($file, $ext = 'html')
	{
		Log::mark();
		
		// Lookup file and import contents
		$filename	= PATH_HTML.DS.str_replace(':', DS, $file) .'.'. $ext;
		if (file_exists( $filename ))
		{
			if ($ext == 'xml') {
				$output	= @simplexml_load_file( $filename ); 
			} else {
				$output	= @file_get_contents( $filename );
			}
		}
		
		// Raise error if file doesn't exist
		else {
			Log::mark('Missing file "'. $file .'" ('. $ext .')');
			Error::raiseError(EC_ERROR_CODE, 'Couldn\'t load content');
		}
		
		// Check for errors
		if ($output === false) {
			Log::mark('Error loading "'. $file .'" ('. $ext .')');
			Error::raiseError(EC_ERROR_CODE, 'Couldn\'t load content');
		}
		
		return $output;
	}
}

