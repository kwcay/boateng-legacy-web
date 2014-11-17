<?php
/**
 * Nyansa App
 *
 * Nice description...
 *
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */

const _NYANSA_INC	= 1;
const NYANSA_APP	= __DIR__;
const DS	= DIRECTORY_SEPARATOR;

// Specify the path to your configuration file
require_once NYANSA_APP.DS.'config.php';

// Include framework setup file
require_once NYANSA_APP.DS.'core'.DS.'framework.php';

// Initialize App
\Nyansa\App::init();

// Execute application
\Nyansa\App::run();
