<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


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
			$entry	= $tmpl;
			$entry	= str_replace('[def:word]', $def->getWord(), $entry);
			$entry	= str_replace('[def:language]', $def->language, $entry);
			$entry	= str_replace('[def:tr:eng]', $def->getTranslation('eng'), $entry);
			$entry	= str_replace('[def:edit-uri]', $def->getEditURI(), $entry);
			$list	.= $entry;
		}
		
		// Return HTML
		$contents	= str_replace('[i:word]', $this->word, $contents);
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

