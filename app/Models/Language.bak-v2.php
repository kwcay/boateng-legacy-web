<?php namespace App\Models;

use URL;

use Illuminate\Support\Arr;
use cebe\markdown\MarkdownExtra;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LanguageBACKUP_V2 extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes;

    // TODO: on save, strip tags from desc[lang] attribute.

    private $markdown;

    /**
     * @var array   Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * @var array   Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'parent' => 'string',
        'name' => 'string',
        'alt_names' => 'string',
        'countries' => 'string',
        'desc' => 'array',
        'state' => 'integer',
        'params' => 'array'
    ];

    /**
     * @var array   Validation rules.
     */
    public $validationRules  = [
        'code'      => 'required|min:3|max:7|unique:languages',
        'parent'    => 'min:3|max:7',
        'name'      => 'required|min:2',
        'alt_names' => 'min:2'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        //
        static::saving([$this, 'onSave']);

        // Markdown parser.
        $this->markdown = new MarkdownExtra;
        $this->markdown->html5 = true;
    }

    /**
     * @param $code
     * @return mixed
     */
    public static function findByCode($code) {
        return static::where(['code' => $code])->first();
    }

    //
    // Methods dealing with descriptions.
    //

    public function hasDescription($lang) {
        return Arr::has($this->desc, $lang) && strlen(Arr::get($this->lang, $lang));
    }

    public function getDescription($lang = 'en') {
        return $this->hasDescription($lang) ? $this->markdown->parse(Arr::get($this->desc, $lang)) : '';
    }

    public function setDescription($lang, $desc)
    {
        $descArray = $this->desc;
        Arr::set($descArray, $lang, $desc);
        $this->desc = $descArray;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getUri($full = true) {
        return $full ? url($this->code) : $this->code;
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

    public function onSave($lang)
    {
        // Sanitize some properties
//        foreach ($lang->desc as $loc => $desc) {
//            $lang->desc[$loc] = strip_tags($desc);
//        }

        if (!is_int($lang->state)) {
            $lang->state = 1;
        }

        return true;
    }

    //
    // Definition parameters.
    //

    public function hasParam($key) {
        return Arr::has($this->params, $key);
    }

    public function getParam($key, $default = '') {
        return Arr::get($this->params, $key, $default);
    }

    public function setParam($key, $value)
    {
        $params = $this->params;
        Arr::set($params, $key, $value);
        $this->params = $params;
    }

    /**
     * Mutator for $this->desc.
     *
     * @param $desc
     */
    public function setDescAttribute($desc)
    {
        // Sanitize.
        foreach ($desc as $key => $val) {
            $desc[$key] = strip_tags($val);
        }

        $this->desc = $desc;
    }
}

