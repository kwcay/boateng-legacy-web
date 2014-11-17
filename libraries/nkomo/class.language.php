<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class Language extends Generic
{
	public $code;			// Language ISO-639-3 code
	public $name;			// Language name; alt spellings
	public $countries;		// Country codes in which language is spoken
	public $isNew	= 0;
	
	public function __construct($code = null)
	{
		// Retrieve definition from database
		if (strlen($code) == 3)
		{
			Log::mark('TODO: load language from code');
		}
		
		// Reset
		else {
			$this->isNew	= 1;
		}
	}
	
	public function getName() {
		$spellings	= explode(';', (string)$this->name);
		return trim($spellings[0]);
	}
	
	public function getAltSpellings($array = false)
	{
		$spellings	= explode(';', (string)$this->name);
		$name		= array_shift($spellings);
		
		return $array ? $spellings : implode('; ', $spellings);
	}
}

