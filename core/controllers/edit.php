<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ControllerEdit extends Controller
{
	public function execute()
	{
		parent::setupFramework('edit');
		
		// Determine what to do
		switch(Request::get('what'))
		{
			case 'save-language':
				
				break;
			
			case 'save-definition':
				$this->saveDefinition();
				break;
			
			case 'lang':
				$this->displayLanguageForm();
				break;
			
			case 'def':
			default:
				$this->displayDefinitionForm();
		}
		
		// Load page contents
		$this->view->load();
	}
	
	/**
	 * Displays language form
	 */
	public function displayLanguageForm() {
		$this->view->form	= 'edit:form.language';
	}
	
	/**
	 * Displays the definition form
	 */
	public function displayDefinitionForm()
	{
		$def	= $this->model->getDefinition();
		
		// Display the right definition form
		$this->view->form	= $def->isNew ? 'edit:form.definition.new' : 'edit:form.definition';
	}
	
	public function saveDefinition()
	{
		// Check token
		if (!Session::checkToken()) {
			Session::set('app.notice', 'Invalid session token');
			App::redirect('edit');
		}
		
		// Retrieve data
		$def	= $this->model->getDefinition();
		$result	= $def->loadPostData();
		$msg	= $result ? 'Definition saved, thanks!' : 'Error: '. $def->getError();
		
		// Redirect
		Session::set('app.notice', $msg);
		App::redirect($result ? 'edit/'. $result : '');
	}
}

