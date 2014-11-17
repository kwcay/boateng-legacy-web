<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


class Definition extends Generic
{
	// Table columns
	public $word;			// Actual word in native language; alt spellings
	public $language;		// List of languages applicable to this definition
	public $translation;	// List of translations in different languages
	public $meaning;		// List of meanings in different languages
	public $state;			// Status of word: suggested (0), published (1)
	public $date;			// Date definition was added to the database
	public $id;				// md5
	
	// Working parameters
	public $isNew		= 0;
	protected $_words	= array();
	protected $_language;
	protected $_translation;
	protected $_meaning;
	
	public function __construct()
	{
		parent::__construct();
		
		// Words array
		$this->_words		= strlen($this->word) ? @explode(',', $this->word) : array();
		foreach ($this->_words as &$word) {
			$word	= trim($word);
		}
		
		// Languages
		Log::mark('TODO: how to formant definition languages in Definition::__construct?');
		
		// Translations
		$this->_translation	= (object) json_decode($this->translation);
		
		// Meanings
		$this->_meaning		= (object) json_decode($this->meaning);
		
		$this->isNew		= ($this->id && strlen($this->id) == 32) ? 0 : 1;
		$this->date			= $this->isNew ? gmdate('Y-m-d') : $this->date;
	}
	
	public function load($id)
	{
		// Performance check
		$id	= Response::filter($id, 'ALNUM');
		if (strlen($id) != 32) {
			return $this->setError('class.def.load: Bad ID');
		}
		
		// Load data
		$db		= App::getDatabase();
		$query	= $db->prepare('SELECT * FROM definitions WHERE id = :id');
		if ($query->execute(array(':id' => $id))) {
			$this->setProperties($query->fetch(PDO::FETCH_ASSOC));
		} else {
			$error	= $query->errorInfo();
			return $this->setError('class.def.load ('. $id .'): '. $error[2]);
		}
		
		// Format data
		$this->__construct();
		return true;
	}
	
	public function loadPostData()
	{
		// ID
		$id		= Request::get('def', null, 'ALNUM', 'POST');
		$isNew	= Request::get('isnew', true, 'BOOLEAN', 'POST');
		if (!$isNew && (strlen($id) != 32 || $id != $this->id)) {
			return $this->setError('class.def.loadPostData: Bad ID');
		}
		
		// Word
		$word	= Request::get('word', '', 'STRING', 'POST');
		if ($this->saveWord($word) === false) {
			return false;
		}
		
		// Alternate spellings
		$alt	= Request::get('alt', '', 'STRING', 'POST');
		$num	= $this->saveAltSpelling($alt);
		
		// Translations
		$translation	= Request::get('translation', array(), 'ARRAY', 'POST');
		if (count($translation)) {
			foreach ($translation as $lang => $str) {
				$this->saveTranslation($lang, $str);
			}
		}
		
		// Meanings
		$meaning		= Request::get('meaning', '', 'ARRAY', 'POST');
		if (count($meaning)) {
			foreach ($meaning as $k => $str) {
				$this->saveMeaning($lang, $str);
			}
		}
		
		// Languages
		$language		= trim(Request::get('language', '', 'STRING', 'POST'));
		$this->language	= $language;
		
		// Format new data
		$data	= $this->getDatabaseBindingArray();
		
		// Date, state, ID
		if ($isNew) {
			$data[':state']	= 1;
			$data[':date']	= gmdate('Y-m-d');
			$data[':id']	= $this->createId();
		}
		
		// Save data
		$db		= App::getDatabase();
		if ($isNew) {
			$query	= $db->prepare(
				'INSERT INTO definitions(word, language, translation, meaning, state, date, id) '.
				'VALUES(:word, :language, :translation, :meaning, :state, :date, :id)');
		} else {
			$query	= $db->prepare(
				'UPDATE definitions SET '.
				'word = :word, language = :language, translation = :translation, meaning = :meaning, '.
				'state = :state, date = :date '.
				'WHERE id = :id'
			);
		}
		
		if ($query->execute($data)) {
			return $this->id;
		} else {
			$error	= $query->errorInfo();
			return $this->setError('Error saving definition data: '. $error[2]);
		}
	}
	
	public function getWord() {
		return isset($this->_words[0]) ? $this->_words[0] : '';
	}
	
	public function saveWord($word)
	{
		// Performance check
		$word	= Filter::input($word, 'WORD');
		$word	= html_entity_decode($word, ENT_QUOTES, 'UTF-8');
		if (strlen($word) < 2) {
			return $this->setError('Word is too short.');
		}
		
		// Save new word to beginning of array
		$old		= array_shift($this->_words);
		array_unshift($this->_words, htmlspecialchars($word, ENT_QUOTES, 'UTF-8', false));
		
		return $old;
	}
	
	public function getAltSpellings($array = false)
	{
		// Remove first word, return alternate spellings
		$copy	= $this->_words;
		$word	= array_shift($copy);
		
		return $array ? $copy : implode(', ', $copy);
	}
	
	public function saveAltSpelling($alt)
	{
		// Performance check
		$alt	= Filter::input($alt, 'WORD');
		$alt	= html_entity_decode($alt, ENT_QUOTES, 'UTF-8');
		if (strlen($alt) < 2) {
			return count($this->_words);
		}
		
		// String of alternate words
		$alt	= str_replace(';', ',', $alt);
		if (strpos($alt, ',') !== false)
		{
			$alt	= explode(',', $alt);
			foreach ($alt as $word) {
				$this->saveAltSpelling($word);
			}
			
			return count($this->_words);
		}
		
		// Check if word is already present
		$alt	= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8', false);
		if (in_array($alt, $this->_words)) {
			return count($this->_words);
		}
		
		// Add word, sort alphabetically, and return number of spellings
		$copy	= $this->_words;
		$main	= array_shift($copy);
		$num	= array_push($copy, $alt) + 1;
		sort($copy, SORT_REGULAR);
		array_unshift($copy, $main);
		$this->_words	= $copy;
		
		return $num;
	}
	
	public function getTranslation($lang = 'en') {
		return property_exists($this->_translation, $lang) ? $this->_translation->$lang : '';
	}
	
	public function saveTranslation($lang, $source = '')
	{
		// Performance check
		$lang	= strtolower(Filter::input($lang, 'A-Z'));
		if (strlen($lang) != 3) {
			return $this->setError('class.def.saveTranslation: Bad language code');
		}
		
		// Save translation
		$source	= Filter::input($source, 'WORD');
		$this->_translation->$lang	= htmlspecialchars($source, ENT_QUOTES, 'UTF-8', false);
		return true;
	}
	
	public function getMeaning($lang = 'en') {
		return property_exists($this->_meaning, $lang) ? $this->_meaning->$lang : '';
	}
	
	public function saveMeaning($lang, $source = '')
	{
		// Performance check
		$lang	= strtolower(Filter::input($lang, 'A-Z'));
		if (strlen($lang) != 3) {
			return $this->setError('class.def.saveMeaning: Bad language code');
		}
		
		// Save meaning
		$source	= Filter::input($source, 'WORD');
		$this->_meaning->$lang	= htmlspecialchars($source, ENT_QUOTES, 'UTF-8', false);
		return true;
	}
	
	public function getURI() {
		return $this->language .'/'. $this->getWord();
	}
	
	public function getEditURI() {
		return 'edit/'. ($this->id ? $this->id : '');
	}
	
	public function createId()
	{
		$db	= App::getDatabase();
		$ck	= $db->prepare('SELECT word FROM definitions WHERE id = :check');
		
		while(true)
		{
			// Create new ID
			$this->id	= md5(Session::getToken() . mt_rand() . microtime());
			$ck->bindParam(':check', $this->id);
			
			// Handle errors
			if (!$ck->execute()) {
				$error	= $ck->errorInfo();
				Error::raiseError(500, 'class.def.create: '. $error[2]);
			}
			
			// Check that ID is unique
			if (!$ck->fetch()) {
				break;
			}
		}
		
		$this->reset();
		return $this->id;
	}
	
	public function getDatabaseBindingArray()
	{
		return array(
			':word'			=> implode(', ', $this->_words),
			':language'		=> $this->language,
			':translation'	=> json_encode($this->_translation),
			':meaning'		=> json_encode($this->_meaning),
			':state'		=> $this->state,
			':date'			=> $this->date,
			':id'			=> $this->id
		);
	}
	
	public function reset() {
		$this->isNew		= 1;
		$this->word			= '';
		$this->language		= '';
		$this->translation	= '';
		$this->meaning		= '';
		$this->state		= 1;
		$this->date			= date('Y-m-d');
		$this->params		= '';
		
		$this->_words		= array();
		$this->_language	= new stdClass;
		$this->_translation	= new stdClass;
		$this->_meaning		= new stdClass;
		$this->_params		= new stdClass;
	}
}

