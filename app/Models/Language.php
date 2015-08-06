<?php namespace App\Models;

use cebe\markdown\MarkdownExtra;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use App\Traits\HasParamsTrait as HasParams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes, HasParams;

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
        'parent_code' => 'string',
        'name' => 'string',
        'alt_names' => 'string',
        'countries' => 'string',
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

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Check attributes when saving the model.
        static::saving([$this, 'checkAttributes']);

        // Markdown parser.
        $this->markdown = new MarkdownExtra;
        $this->markdown->html5 = true;
    }

    /**
     * Parent relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        return $this->belongsTo('App\Models\Language', 'parent_code', 'code');
    }

    /**
     * Children relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {
        return $this->hasMany('App\Models\Language', 'parent_code', 'code');
    }

    /**
     * Definitions relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function definitions() {
        return $this->belongsToMany('App\Models\Definition');
    }

    /**
     * Looks up a language model by code.
     *
     * @param string $code
     * @return \App\Models\Language
     */
    public static function findByCode($code) {
        return static::where(['code', $code])->first();
    }

    /**
     * Gets the URI for the language.
     *
     * @param bool $full
     * @return string
     */
    public function getUri($full = true) {
        return $full ? url($this->code) : $this->code;
    }

    /**
     * Gets the URI to the language edit form.
     *
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

    /**
     * Checks language properties before saving to database.
     *
     * @param \App\Models\Language $lang
     * @return bool
     */
    public function checkAttributes($lang)
    {
        // Make sure state is valid.
        if (!is_int($lang->state)) {
            $lang->state = 1;
        }

        return true;
    }
}

