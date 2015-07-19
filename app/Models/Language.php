<?php namespace App\Models;

use URL;

use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes;

    /**
     * @var int     Internal identifier.
     */
    public $id = 0;

    /**
     * @var string
     */
    public $code = '';

    /**
     * @var string
     */
    public $parent = '';

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $alt_names = '';

    /**
     * @var string
     */
    public $countries = '';

    /**
     * @var string
     */
    public $desc = '';

    /**
     * @var int
     */
    public $state = -1;

    /**
     * @var string
     */
    public $params = '';

    /**
     * @var array   Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * @var array   Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array   Validation rules.
     */
    public $validationRules  = [
        'code'      => 'required|min:3|max:7|unique:languages',
        'parent'    => 'min:3|max:7',
        'name'      => 'required|min:2',
        'alt_names' => 'min:2'
    ];

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

