<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


class Router
{
	public static function init()
	{
		// Get handler
		$path	= @explode('/', substr(URI::getPath(), 1));
		$page	= strtolower(array_shift( $path ));
		
		switch ( $page )
		{
			// Static pages
			case 'about':
			case 'stats':
				Request::set('page', $page);
				self::route('static');
				break;
			
			// Home page
			case '':
				self::route('home');
				break;
			
			// Edit page
			case 'edit':
				self::routeEdit($path);
				break;
			
			// Specific page for dictionary or word
			default:
				self::routePage($page, $path);
		}
	}
	
	private static function route($controller) {
		Request::set('controller', $controller);
	}
	
	private static function routeEdit($path)
	{
		Request::set('controller', 'edit');
		
		// 
		$id		= isset($path[0]) ? $path[0] : '';
		
		// Edit language
		if (strlen($id) == 3)
		{
			Request::set('what', 'lang');
			Request::set('code', $id);
		}
		
		// Edit definition
		elseif (strlen($id) == 32)
		{
			Request::set('what', 'def');
			Request::set('def', $id);
		}
	}
	
	private static function routePage($lang, $path = array())
	{
		Request::set('controller', 'page');
		Request::set('lang', $lang);
		
		// Specific word
		if (isset($path[0]) && strlen($path[0])) {
			Request::set('word', $path[0]);
		}
	}
}
