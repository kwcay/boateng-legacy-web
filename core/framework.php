<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
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
define('IS_LOCAL', ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == Config::LOCALHOST) ? 1 : 0);
define('INC_VER', IS_LOCAL ? time() : VER);
define('PATH_CORE', \NKOMO_DIR.DS.'core');
define('PATH_LIB', \NKOMO_DIR.DS.'libraries');
define('PATH_HTML', \NKOMO_DIR.DS.'html');

// Debugging
Config::DEBUG ? error_reporting(E_ALL) : null;

// App engine
require_once(PATH_CORE.DS.'app.php');

