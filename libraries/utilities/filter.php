<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class Filter
{
	public static function input($source, $style = '')
	{
		$style	= strtolower($style);
		switch ($style)
		{
			case 'bool':
			case 'boolean':
				$output	= (bool) $source;
				break;
			
			case 'int':
			case 'integer':
				$output	= (int) $source;
				break;
			
			case 'float':
			case 'double':
				$output	= (float) $source;
				break;
			
			case 'string':
				$output	= (string) $source;
				break;
			
			case 'array':
				$output	= (array) $source;
				break;
			
			case 'a-z':
				$output	= strip_tags((string) $source);
				$output	= preg_replace('/[^A-Z]/i', '', $output);
				break;
			
			case 'md5':
			case 'sha1':
			case 'alnum':
			case 'alphanum':
			case 'alphanumeric':
			case 'a-z0-9':
				$output	= strip_tags((string) $source);
				$output	= preg_replace('/[^A-Z0-9]/i', '', $output);
				break;
			
			case 'base64':
				$output	= strip_tags((string) $source);
				$output	= preg_replace('/[^A-Z0-9\/+=]/i', '', $output);
				break;
			
			case 'word':
				Log::mark('TODO: what kind of filtering can we apply to words?');
				$output	= trim(strip_tags((string) $source));
				break;
			
			case 'none':
				$output	= $source;
				break;
			
			default:
				$output	= null;
		}
		
		return $output;
	}
	
	public static function output($source, $style = '')
	{
		
		
		return $source;
	}
}

