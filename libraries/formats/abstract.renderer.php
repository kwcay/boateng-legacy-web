<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


abstract class Renderer
{
	// Page details
	public $body		= '';
	protected $_tags	= array();
	protected $_css		= '';
	protected $_js		= '';
	
	public function __construct() {}
	
	abstract public function render();
	
	public function format($str)
	{
		// Do string replacements
		$str	= str_ireplace('[c:VER]', VER, $str);
		
		return $str;
	}
}
