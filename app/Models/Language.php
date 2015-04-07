<?php namespace App\Models;

use App\Models\BaseResource as Res;

class Language extends Res {

    /**
     * Properties with alternate spellings
     */
    public $altSpellings    = ['name'];

    /**
     * Array to help validate input data
     */
    public static $validationRules  = []
        'code'      => 'sometimes|required|min:3|max:7|unique:languages',
        'parent'    => 'min:3|max:7',
        'name'      => 'required|min:2',
        'countries' => 'array'
    ];

    /**
     * Allow only the code field to be populated on instantiation.
     */
    public $fillable    = ['code'];

    /**
     * Shortcut to retrieve language name
     * @return string
     */
    public function getName() {
        return parent::getMainAlt('name');
    }

    public function setName($name) {
        return parent::setMainAlt('name', $name);
    }

    public function getAltNames($toArray = false) {
        return parent::getOtherAlts('name', $toArray);
    }

    public function setAltName($name) {
        return parent::setOtherAlt('name', $name);
    }

    public function removeAltName($name) {
        return parent::unsetOtherAlt('name', $name);
    }

    public function getDescription() {
        return strlen($this->desc) ? preg_replace('/(\r\n|\n)/', '<br />', $this->desc) : '';
    }

    public static function findByCode($code) {
        return self::where(['code' => $code])->first();
    }

    public function getUri($full = true) {
        return $full ? URL::to($this->code) : $this->code;
    }

    public function getEditUri($full = true) {
        $path   = 'edit/lang/'. $this->code;
        return $full ? URL::to($path) : $path;
    }

}
