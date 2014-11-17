<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


abstract class Controller
{
	public function __construct() {}
	
	abstract public function execute();
	
	/*
	 *
	 */
	public function setupFramework($controller = '')
	{
		Log::mark('Setting up architecture...');
		
		// Set framework objects
		$this->model	= App::getModel($controller);
		$this->view		= App::getView($controller);
		$this->view->model	= $this->model;
		
		// Save controller data
		if (strlen($controller)) {
			Request::set('model', $controller);
			Request::set('view', $controller);
		}
		
		Log::mark('Setting up architecture... Done.');
	}
	
	public function sendJSON($data)
	{
		// Send appropriate headers
		header('Content-type: application/json; charset=UTF-8');
		
		// Send JSON data
		echo json_encode($data);
		
		// Exit application
		exit();
	}
}

