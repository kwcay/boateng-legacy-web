<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class ViewEdit extends View
{
	public $title	= 'Di Nkomo: edit';
	public $form	= 'edit:form.definition';
	
	public function load()
	{
		// Import form
		$html	= $this->getContents('header');
		$html	.= $this->getContents($this->form);
		
		// Form elements
		switch ($this->form)
		{
			case 'edit:form.language':
				$html	= $this->_loadLanguageForm($html);
				break;
			
			case 'edit:form.definition':
			case 'edit:form.definition.new':
				$html	= $this->_loadDefinitionForm($html);
				break;
			
			default:
				Error::raiseError('Invalid form type');
		}
		
		// Add footer
		$html	.= $this->getContents('footer');
		
		$this->body	= $html;
	}
	
	private function _loadLanguageForm($html)
	{
		// Get language
		$lang	= $this->model->getLanguage();
		
		// Form header
		$header	= $lang->isNew ? 'Suggest a new <i>language</i>' : 'Edit an existing language';
		
		// Populate input fields
		$html	= str_replace('[lang:h1]', $header, $html);
		$html	= str_replace('[lang:name]', $lang->getName(), $html);
		$html	= str_replace('[lang:alt]', $lang->getAltSpellings(), $html);
		$html	= str_replace('[lang:code]', $lang->code, $html);
		$html	= str_replace('[lang:countries]', $lang->countries, $html);
		$html	= str_replace('[lang:isnew]', $lang->isNew, $html);
		
		return $html;
	}
	
	private function _loadDefinitionForm($html)
	{
		// Get definition
		$def	= $this->model->getDefinition();
		
		// Populate input fields
		$html	= str_replace('[def:word]', $def->getWord(), $html);
		$html	= str_replace('[def:alt]', $def->getAltSpellings(), $html);
		$html	= str_replace('[def:lang]', $def->language, $html);
		$html	= str_replace('[def:id]', $def->isNew ? '' : $def->id, $html);
		$html	= str_replace('[def:isnew]', $def->isNew, $html);
		
		// Language-specific params
		$html	= str_replace('[def:tr:eng]', $def->getTranslation('eng'), $html);
		$html	= str_replace('[def:meaning:eng]', $def->getMeaning('eng'), $html);
		
		return $html;
	}
}

