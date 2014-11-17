<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


/**
 * App
 */
class App
{
	// Variables
	protected static $_local	= null;
	protected static $_db		= null;
	
	// Framework objects
	protected static $_mvc		= array(
		'models'		=> array(),
		'views'			=> array(),
		'controllers'	=> array()
	);
	
	public static function init()
	{
		// Include some basic classes
		require_once(PATH_CORE.DS.'log.php');
		require_once(PATH_CORE.DS.'error.php');
		require_once(PATH_LIB.DS.'library.php');
		
		// Start profiler
		Log::mark('Initializing Di Nkomo...');
		
		// Run some checks
		// ...
		
		// Start session
		require_once(PATH_CORE.DS.'session.php');
		Session::init();
		
		// MVC architecture
		require_once(PATH_CORE.DS.'abstract.model.php');
		require_once(PATH_CORE.DS.'abstract.view.php');
		require_once(PATH_CORE.DS.'abstract.controller.php');
		
		// Essential libraries
		Library::import('utilities:filter');
		Library::import('environment:uri');
		Library::import('environment:request');
		Library::import('environment:response');
		Library::import('nkomo:class.generic');
		Library::import('nkomo:*');
		
		// Register shutdown method
		register_shutdown_function(array('\Nkomo\App', 'shutdown'));
		
		// Initialise variables
		Request::fetch();
		Log::mark('Initializing App... Done.');
	}
	
	public static function run()
	{
		Log::mark('Running App...');
		
		// Route request
		require_once(PATH_CORE.DS.'router.php');
		Router::init();
		
		// Let the controller make all the decisions
		$controller	= self::getController();
		$controller->execute();
		
		// Render the document (if applicable)
		Log::mark('Rendering App...');
		$view	= self::getView();
		echo $view->render();
		
		exit();
	}
	
	public static function getFrameworkComponent($mvc, $name)
	{
		// Performance check
		if (isset( self::$_mvc[$mvc][$name] )) {
			return self::$_mvc[$mvc][$name];
		}
		
		// Some checks...
		switch ($mvc)
		{
			case 'model':
			case 'view':
			case 'controller':
				$file	= PATH_CORE .DS. $mvc.'s' .DS. $name .'.php';
				$class	= '\Nkomo\\'. ucfirst($mvc) . ucfirst($name);
				break;
			
			default:
				Error::raiseError(Error::EC_ERROR_CODE, 'Invalid architecture component.');
		}
		
		// Include framework component file
		if (is_file($file)) {
			require_once $file;
		} else {
			Error::raiseError(Error::EC_ERROR_CODE, 'Couldn\'t load framework component.');
		}
		
		// Check that class name is well formatted
		if (!class_exists($class)) {
			Error::raiseError(Error::EC_ERROR_CODE, 'Couldn\'t load framework component.');
		}
		
		// Instantiate object
		self::$_mvc[$mvc][$name]	= new $class();
		
		Log::mark(ucfirst($mvc) .' for '. $name .' loaded.');
		
		return self::$_mvc[$mvc][$name];
	}
	
	public static function getModel($name = '')
	{
		// Model name
		if (empty( $name )) {
			$name	= Request::get('model', 'home');
		}
		
		return self::getFrameworkComponent('model', $name);
	}
	
	public static function getView($name = '')
	{
		// View name
		if (empty( $name )) {
			$name	= Request::get('view', 'home');
		}
		
		return self::getFrameworkComponent('view', $name);
	}
	
	public static function getController($name = '')
	{
		// Controller name
		if (empty( $name )) {
			$name	= Request::get('controller', 'home');
		}
		
		return self::getFrameworkComponent('controller', $name);
	}
	
	public static function getDatabase()
	{
		if (!is_null(self::$_db)) {
			return self::$_db;
		}
		
		// Connect to database
		Log::mark('Creating database object...');
		try {
			self::$_db	= new \PDO('mysql:host='. Config::DBHOST .';dbname='. Config::DBNAME, Config::DBUSER, Config::DBPASSWORD);
		} catch (PDOException $error) {
			Error::raiseError(Error::EC_SERVER_ERROR, $error->getMessage());
		}
		
		if (Config::DEBUG && IS_LOCAL) {
			self::$_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		
		// Return database object
		Log::mark('Creating database object... Done.');
		return self::$_db;
	}
	
	// Redirect
	public static function redirect($to, $status = 302)
	{
		// Status code
		if (is_int($status)) {
			header('HTTP/1.1 '. $status);
		}
		
		// Get redirect URL
		$url	= $to;
		if (strpos($url, 'http') !== 0) {
			$url	= URI::toString('scheme', 'host') .'/'. $url;
		}
		
		// Redirect
		header('Location: '. $url);
		exit;
	}
	
	public static function isLocal()
	{
		if (is_null( self::$_local )) {
			self::$_local = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1');
		}
		
		return self::$_local;
	}
	
	public static function shutdown()
	{
		// Unset database object
		if (!is_null(self::$_db)) {
			self::$_db	= null;
		}
		
		// Close session
		Session::close();
	}
}


