<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ViewPage extends View
{
	/**
	 * 
	 */
	public function load()
	{
		// Get page HTML
		$html	= $this->getContents('header');
		
		switch ($this->type)
		{
			case 'dictionary':
				$html	.= $this->_loadDictionary();
				break;
			
			case 'definitions':
				$html	.= $this->_loadDefinitions();
				break;
			
			default:
				App::redirect('');
		}
		
		// Footer
		$html	.= $this->getContents('footer');
		
		$this->body	= $html;
	}
	
	/**
	 * 
	 */
	private function _loadDictionary()
	{
		// Search database for word
		if (!$lang = $this->model->getLanguage($this->lang)) {
			return $this->_load404($this->lang, 'edit?what=lang&code='. $this->lang);
		}
		
		return 'TODO: display language details';
	}
	
	/**
	 * 
	 */
	private function _loadDefinitions()
	{
		// Search database for word
		if (!$defs = $this->model->getDefinitions($this->word)) {
			return $this->_load404($this->word, 'edit?word='. Filter::output($this->word));
		}
		
		// Prepare definitions page
		$contents	= $this->getContents('pages:definitions');
		$tmpl		= $this->getContents('pages:definitions.entry');
		$list		= '';
		
		// Add entries
		foreach ($defs as $def)
		{
			// Fill in template
			$entry	= $tmpl;
			$entry	= str_replace('[def:word]', $def->getWord(), $entry);
			$entry	= str_replace('[def:language]', $def->language, $entry);
			$entry	= str_replace('[def:tr:eng]', $def->getTranslation('eng'), $entry);
			$entry	= str_replace('[def:edit-uri]', $def->getEditURI(), $entry);
			
			// Add more info, if we have any
			$meaning	= $def->getMeaning('eng');
			$meaning	= strlen($meaning) ? ' &ndash; '. $meaning .'.' : '';
			$entry	= str_replace('[def:mean:eng]', $meaning, $entry);
			$alt	= $def->getAltSpellings();
			$alt	= strlen($alt) ? '<br />Alternate spellings: <i>'. $alt .'</i>' : '';
			$entry	= str_replace('[def:alt]', $alt, $entry);
			
			$list	.= $entry;
		}
		
		// Return HTML
		$lang	= $defs[0]->language;
		$lang	= '<a href="'. $lang .'">'. $lang .'</a>';
		$contents	= str_replace('[i:word]', $this->word, $contents);
		$contents	= str_replace('[i:lang]', $lang, $contents);
		$contents	= str_replace('[i:def-list]', $list, $contents);
		return $contents;
	}
	
	/**
	 * 
	 */
	private function _load404($item, $createUri)
	{
		$contents	= $this->getContents('pages:404');
		$contents	= str_replace('[404:item]', $item, $contents);
		$contents	= str_replace('[404:create]', Filter::output($createUri), $contents);
		
		return $contents;
	}
}

