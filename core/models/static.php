<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


class ModelStatic extends Model
{
	public function getTempStats()
	{
		$temp	= array();
		
		// Number of languages in database
		$db	= App::getDatabase();
		if ($numLang = $db->query('SELECT COUNT(code) FROM languages')) {
			$numLangRes	= (array) $numLang->fetch(PDO::FETCH_NUM);
			$temp['lang']	= $numLangRes[0];
		} else {
			$temp['lang']	= '??';
		}
		
		// Number of words
		if ($numWords = $db->query('SELECT COUNT(id) FROM definitions')) {
			$numWordsRes	= (array) $numWords->fetch(PDO::FETCH_NUM);
			$temp['def']	= $numWordsRes[0];
		} else {
			$temp['def']	= '??';
		}
		
		return $temp;
	}
}

