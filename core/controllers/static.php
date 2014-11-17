<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ControllerStatic extends Controller
{
	public function execute()
	{
		parent::setupFramework('static');
		
		// Load page contents
		$this->view->load();
	}
}

