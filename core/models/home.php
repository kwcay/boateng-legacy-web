<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ModelHome extends Model
{
	public function getWotd()
	{
		Log::mark();
		$db	= App::getDatabase();
		
		$query	= $db->query('SELECT * FROM definitions ORDER BY RAND() LIMIT 1');
		$query->setFetchMode(\PDO::FETCH_CLASS, 'Nkomo\Definition');
		
		return $query->fetch();
	}
	
	public function getQueryResults()
	{
		Log::mark();
		$db	= App::getDatabase();
		
		$q	= '%'. Request::get('q') .'%';
		
		$query	= $db->prepare('SELECT * FROM definitions WHERE word LIKE :q OR translation LIKE :q ORDER BY word ASC');
		$query->bindParam(':q', $q);
		
		// Retrieve results
		if ($query->execute())
		{
			$res	= new stdClass;
			$res->query	= $q;
			$res->definitions	= $query->fetchAll(\PDO::FETCH_CLASS, 'Nkomo\Definition');
		}
		
		// Return error message
		else {
			$res	= new stdClass;
			$error	= $query->errorInfo();
			$res->error	= $error[2];
		}
		
		return $res;
	}
}

