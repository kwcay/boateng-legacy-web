<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


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
				Error::raiseError(EC_ERROR_CODE, $def->getError());
			}
		}
		
		// Or serve a brand new Definition object
		else {
			$def->reset();
		}
		
		return $def;
	}
}

