<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


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
