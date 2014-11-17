<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


/**
 * App version
 */
const VER	= '0.0.1';

// Runtime constants
define('IS_LOCAL', ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') ? 1 : 0);
define('INC_VER', IS_LOCAL ? time() : VER);
define('PATH_CORE', \NYANSA_APP.DS.'core');
define('PATH_LIB', \NYANSA_APP.DS.'libraries');
define('PATH_HTML', \NYANSA_APP.DS.'html');

// Debugging
Config::DEBUG ? error_reporting(E_ALL) : null;

// App engine
require_once(PATH_CORE.DS.'app.php');

