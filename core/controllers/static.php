<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


class ControllerStatic extends Controller
{
	public function execute()
	{
		parent::setupFramework('static');
		
		// Load page contents
		$this->view->load();
	}
}

