<?php namespace App\Models;

use URL;

use App\Traits\ExportableResourceTrait;
use App\Traits\ImportableResourceTrait;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use ExportableResourceTrait, ImportableResourceTrait;

    /**
     * @var array   Validation rules.
     */
    public $validationRules  = [
        'code'      => 'required|min:3|max:7|unique:languages',
        'parent'    => 'min:3|max:7',
        'name'      => 'required|min:2',
        'alt_names' => 'min:2',
        'countries' => 'array'
    ];

    /**
     * Allow only the code field to be populated on instantiation.
     *
     * @var array
     */
    public $fillable    = ['code', 'parent', 'name', 'alt_names'];

    public function getDescription() {
        return strlen($this->desc) ? preg_replace('/(\r\n|\n|\r)/', '<br />', $this->desc) : '';
    }

    /**
     * @param $code
     * @return mixed
     */
    public static function findByCode($code) {
        return self::where(['code' => $code])->first();
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getUri($full = true) {
        return route('language', ['code' => $this->code], $full);
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getEditUri($full = true) {
        return route('language.edit', ['code' => $this->code], $full);
    }

    /**
     * Retrieves country list (compiled with umpirsky/country-list library).
     *
     * @param string $locale    Language in which to retrieve country names
     * @return array            List of countries
     */
    public static function getCountryList($locale = 'en')
    {
        $locale = preg_replace('/[^a-z_]/', '', $locale);
        $list   = file_exists(base_path() .'/resources/countries/'. $locale .'.php') ?
            include base_path() .'/resources/countries/'. $locale .'.php' :
            include base_path() .'/resources/countries/en.php';

        return $list;
    }
}

