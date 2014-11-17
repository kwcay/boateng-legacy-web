<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ModelPage extends Model
{
	public function __construct() {
		parent::__construct();
	}
	
	public function getLanguage()
	{
		
		
		return false;
	}
	
	public function getDefinitions($searchTerm)
	{
		// Performance check
		$searchTerm	= Filter::input($searchTerm, 'WORD');
		if (strlen($searchTerm) < 2) {
			return false;
		}
		
		// Build query
		$qunique	= $searchTerm;
		$qfirst		= $searchTerm .',%';
		$qmiddle	= '%, '. $searchTerm .',%';
		$qlast		= '%, '. $searchTerm;
		$sql	=	'SELECT * FROM definitions '.
						'WHERE word = :qunique '.
						'OR word LIKE :qfirst '.
						'OR word LIKE :qmiddle '.
						'OR word LIKE :qlast '.
					'ORDER BY word ASC';
		
		// Query the database
		$db		= App::getDatabase();
		$query	= $db->prepare($sql);
		$query->bindParam(':qunique',	$qunique);
		$query->bindParam(':qfirst',	$qfirst);
		$query->bindParam(':qmiddle',	$qmiddle);
		$query->bindParam(':qlast',		$qlast);
		if ($query->execute()) {
			$results	= $query->fetchAll(\PDO::FETCH_CLASS, '\Nkomo\Definition');
		} else {
			$results	= false;
		}
		
		return $results;
	}
}

