<?php
/**
 * Di Nkomo App
 *
 * Nice description...
 *
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */

const _NKOMO_INC	= 1;
const NKOMO_DIR		= __DIR__;
const DS	= DIRECTORY_SEPARATOR;

// Specify the path to your configuration file
require_once NKOMO_DIR.DS.'config.php';

// Include framework setup file
require_once NKOMO_DIR.DS.'core'.DS.'framework.php';

// Initialize App
\Nkomo\App::init();

// Execute application
\Nkomo\App::run();

// This is it. Let's now tap ourselves on the back :)
