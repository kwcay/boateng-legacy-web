<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


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

