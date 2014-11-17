<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ViewHome extends View
{
	public $title	= 'Di Nkomo: the book of Native tongues.';
	
	public function load()
	{
		// ...
		$content	= $this->getContents('header');
		$content	.= $this->getContents('home:home');
		
		// Word of the day
		$content	= str_replace('[i:wotd]', $this->getWotd(), $content);
		
		// Footer
		$content	.= $this->getContents('footer');
		
		$this->body	= $content;
	}
	
	public function getWotd()
	{
		// Performance check
		$def	= $this->model->getWotd();
		if (!$def) {
			return '(none)';
		}
		
		// Format
		$word	= '<em>&ldquo; <a href="'. $def->getURI() .'">'. $def->getWord() .'</a> &rdquo;</em>';
		
		return $word;
	}
}

