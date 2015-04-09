<?php namespace App\Models;

use URL;
use App\Models\BaseResource as Res;

class Definition extends Res {

    /**
     *
     */
    public $altSpellings = ['word'];

    /**
     *
     */
    public $jsonObjects = ['translation', 'meaning'];

    /**
     * Array to help validate input data
     */
    public $validationRules = [
        'word'      => 'required|min:2',
        'language'  => 'required|min:2|regex:/^([a-z, ]+)$/',
        'type'      => 'in:adj,adv,conn,pro,n,v'
    ];

    public $wordTypes = [
        'adj'   => 'adjective',
        'adv'   => 'adverb',
        'conn'  => 'connective',
        'pro'   => 'pronoun',
        'n'     => 'noun',
        'v'     => 'verb'
    ];

    public function getWord() {
        return parent::getMainAlt('word');
    }

    public function setWord($word) {
        return parent::setMainAlt('word', $word);
    }

    public function getAltWords($toArray = false) {
        return parent::getOtherAlts('word', $toArray);
    }

    public function setAltWord($word) {
        return parent::setOtherAlt('word', $word);
    }

    public function removeAltWord($word) {
        return parent::unsetOtherAlt('word', $word);
    }

    public function getTranslation($lang = 'eng') {
        return parent::getJsonParam('translation', $lang, '');
    }

    public function setTranslation($lang, $value = '') {
        return parent::setJsonParam('translation', $lang, $value);
    }

    public function getMeaning($lang = 'eng') {
        return parent::getJsonParam('meaning', $lang, '');
    }

    public function setMeaning($lang, $value = '') {
        return parent::setJsonParam('meaning', $lang, $value);
    }

    public function getMainLanguage($code = false)
    {
        if (!isset($this->_mainLangCode))
        {
            $langs  = explode(',', $this->language);
            $code   = trim(array_shift($langs));
            $this->_mainLangName    = '';
            $this->_mainLangCode    = $code;

            // Find language name
            if (strlen($code) && $lang = Language::findByCode($code)) {
                $this->_mainLangName = $lang->getName();
            }
        }

        return $code ? $this->_mainLangCode : $this->_mainLangName;
    }

    public function getUri($full = true) {
        return route('definition.show', ['id' => $this->getId()], $full);
    }

    public function getWordUri($full = true) {
        $path   = $this->getMainLanguage(true) .'/'. str_replace(' ', '_', $this->getWord());
        return $full ? URL::to($path) : $path;
    }

    public function getEditUri($full = true) {
        return route('definition.edit', ['id' => $this->getId()], $full);
    }

}
