<?php namespace App\Models;

use App\Traits\ExportableResourceTrait;
use App\Traits\ImportableResourceTrait;
use URL;

class LanguageBACKUP extends BaseResource {

    use ExportableResourceTrait, ImportableResourceTrait;

    /**
     * Properties with alternate spellings.
     *
     * @var array
     */
    public $altSpellings    = ['name'];

    /**
     * JSONable properties.
     *
     * @var array
     */
    public $jsonObjects = ['params'];

    /**
     * Validation rules.
     *
     * @var array
     */
    public $validationRules  = [
        'code'      => 'sometimes|required|min:3|max:7|unique:languages',
        'parent'    => 'min:3|max:7',
        'name'      => 'required|min:2',
        'countries' => 'array'
    ];

    /**
     * Allow only the code field to be populated on instantiation.
     *
     * @var array
     */
    public $fillable    = ['code'];

    /**
     * Shortcut to retrieve language name.
     *
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
        return strlen($this->desc) ? preg_replace('/(\r\n|\n|\r)/', '<br />', $this->desc) : '';
    }

    public static function findByCode($code) {
        return self::where(['code' => $code])->first();
    }

    public function getUri($full = true) {
        return $full ? URL::to('/'. $this->code) : $this->code;
    }

    public function getEditUri($full = true) {
        return route('language.edit', ['code' => $this->code], $full);
    }

    /**
     * Retrieves country list (compiled with umpirsky/country-list library).
     *
     * @param string $locale    Language in which to retrieve country names
     * @return array            List of countries
     */
    public function getCountryList($locale = 'en')
    {
        $locale = preg_replace('/[^a-z_]/', '', $locale);
        $list   = file_exists(base_path() .'/resources/countries/'. $locale .'.php') ?
            include base_path() .'/resources/countries/'. $locale .'.php' :
            include base_path() .'/resources/countries/en.php';

        return $list;
    }

}
