<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ModelEdit extends Model
{
	public function getLanguage()
	{
		// Retrieve definition from database
		// ... TODO
		$code	= Request::get('lang', null, 'ALNUM');
		
		return new Language();
	}
	
	public function getDefinition()
	{
		// Performance check
		static $def;
		if ($def instanceof Definition) {
			return $def;
		}
		
		// Definition object
		$def	= new Definition();
		
		// Load from database
		$id		= Request::get('def', null, 'MD5');
		if (strlen($id) == 32) {
			if (!$def->load($id)) {
				Error::raiseError(Error::EC_ERROR_CODE, $def->getError());
			}
		}
		
		// Or serve a brand new Definition object
		else {
			$def->reset();
		}
		
		return $def;
	}
}

