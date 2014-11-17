<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ControllerHome extends Controller
{
	public function execute()
	{
		parent::setupFramework('home');
		
		// JSON request
		if (Request::getFormat() == 'json') {
			$this->sendJSON($this->model->getQueryResults());
		}
		
		// TODO: word of the day
		// ...
		
		// Load page contents
		$this->view->load();
	}
}

