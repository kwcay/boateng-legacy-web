<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


class ControllerPage extends Controller
{
	public function execute()
	{
		parent::setupFramework('page');
		
		// Decide view
		$this->view->lang	= Request::get('lang', '', 'A-Z');
		$this->view->word	= Request::get('word', '', 'WORD');
		$this->view->type	= strlen($this->view->word) ? 'definitions' : 'dictionary';
		
		// Load page contents
		$this->view->load();
	}
}

