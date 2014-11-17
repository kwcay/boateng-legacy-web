<?php
namespace Nyansa;
defined('_NYANSA_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class Generic
{
	protected $params	= '';
	protected $_params	= array();
	protected $_errors	= array();
	
	public function __construct()
	{
		// Parameters
		$this->_params	= (object) json_decode($this->params);
	}
	
	public function getParam($key, $def = null) {
		return isset($this->_params[$key]) ? $this->_params[$key] : $def;
	}
	
	public function setParam($key, $value = null)
	{
		// Set new value, return old value
		$old	= $this->_params[$key];
		$this->_params[$key]	= $value;
		return $old;
	}
	
	public function getProperties() {
		$vars	= get_object_vars( $this );
		foreach ($vars as $key => $value) {
			if (substr( $key, 0, 1 ) == '_') {
				unset($vars[$key]);
			}
		}
		
        return $vars;
	}
	
	public function setProperties( $p )
	{
		$p	= (array) $p;
		if (is_array( $p ))
		{
			foreach ($p as $k => $v) {
				$this->$k = $v;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function getError($i = null)
	{
		// Find the error
		if ($i === null) {
			// Default, return the last message
			$error	= end($this->_errors);
		}
		
		elseif (!array_key_exists($i, $this->_errors)) {
			// If $i has been specified but does not exist, return false
			return false;
		}
		
		else {
			$error	= $this->_errors[$i];
		}
		
		return $error;
	}
	
	public function getErrors() {
		return $this->_errors;
	}
	
	public function setError($error) {
		array_push($this->_errors, $error);
		Log::mark($this->toString() .' (error): '. $error);
		return false;
	}
	
	public function toString() {
		return get_class( $this );
	}
}

