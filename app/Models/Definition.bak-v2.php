<?php namespace App\Models;

use URL;

use App\Models\Language;
use Illuminate\Support\Arr;
use App\Traits\ValidatableTrait as Validatable;
use App\Traits\ObfuscatableTrait as Obfuscatable;
use App\Traits\ExportableTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefinitionBACKUP_V2 extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes;

    CONST TYPE_WORD = 0;        // Regular definitions.
    CONST TYPE_PHRASE = 1;      // Proverbs, sayings, etc.
    CONST TYPE_POEM = 2;        // Poems, songs, etc.
    CONST TYPE_STORY = 3;       // Short stories.

    /**
     * @var array   Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * @var array   Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array   Attribute types
     */
    protected $casts = [
        'data' => 'string',
        'alt_data' => 'string',
        'type' => 'integer',
        'languages' => 'string',
        'translations' => 'array',
        'literal_translations' => 'array',
        'meanings' => 'array',
        'source' => 'string',
        'tags' => 'string',
        'state' => 'integer',
        'params' => 'array'
    ];

    /**
     * @var array   Validation rules.
     */
    public $validationRules = [
        'data' => 'required|min:2',
        'alt_data' => 'min:2',
        'languages' => 'required|min:2|regex:/^([a-z, \-]+)$/',
//        'type'      => 'in:adj,adv,conn,ex,pre,pro,n,v'
    ];

    /**
     * @var array
     */
    public $exportFormats = ['yml', 'yaml', 'json', 'bgl', 'dict'];

    /**
     * See: http://www.edb.utexas.edu/minliu/pbl/ESOL/help/libry/speech.htm
     * See: http://www.aims.edu/student/online-writing-lab/grammar/parts-of-speech
     *
     * @var array   Parts of speech.
     */
    public $partsOfSpeech = [
        'adj'   => 'adjective',
        'adv'   => 'adverb',
        'conn'  => 'connective',
        'ex'    => 'exclamation',
        'pre'   => 'preposition',
        'pro'   => 'pronoun',
        'n'     => 'noun',
        'v'     => 'verb',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

//        //
//        static::saving([$this, 'onSave']);
//
//        // Markdown parser.
//        $this->markdown = new MarkdownExtra;
//        $this->markdown->html5 = true;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getUri($full = false) {
        $path   = $this->mainLanguage->code .'/'. str_replace(' ', '_', $this->data);
        return $full ? url($path) : $path;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getEditUri($full = true) {
        return route('definition.edit', ['id' => $this->getId()], $full);
    }

    /**
     * @param string $lang
     * @return mixed
     */
    public static function random($lang = '') {
        return strlen($lang) ?
            static::where('languages', 'LIKE', '%'. $lang .'%')->orderByRaw('RAND()')->first() :
            static::orderByRaw('RAND()')->first();
    }

    //
    // Methods dealing with translations.
    //

    public function hasTranslation($lang) {
        return Arr::has($this->translations, $lang) && strlen(Arr::get($this->translations, $lang));
    }

    public function getTranslation($lang = 'en') {
        return Arr::get($this->translations, $lang, '');
    }

    public function setTranslation($lang, $translation)
    {
        $translations = $this->translations;
        Arr::set($translations, $lang, $translation);
        $this->translations = $translations;
    }

    //
    // Methods dealing with literal translations.
    //

    public function hasLiteralTranslation($lang) {
        return Arr::has($this->literalTranslations, $lang) && strlen(Arr::get($this->literalTranslations, $lang));
    }

    public function getLiteralTranslation($lang = 'en') {
        return Arr::get($this->literalTranslations, $lang, '');
    }

    public function setLiteralTranslation($lang, $translation)
    {
        $translations = $this->literalTranslations;
        Arr::set($translations, $lang, $translation);
        $this->literalTranslations = $translations;
    }

    //
    // Methods dealing with detailed meanings.
    //

    public function hasMeaning($lang) {
        return Arr::has($this->meanings, $lang) && strlen(Arr::get($this->meanings, $lang));
    }

    public function getMeaning($lang = 'en') {
        return Arr::get($this->meanings, $lang, '');
    }

    public function setMeaning($lang, $meaning)
    {
        $meanings = $this->translations;
        Arr::set($meanings, $lang, $meaning);
        $this->translations = $meanings;
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
     * Accessor for $this->languages.
     *
     * @param $str
     * @return array
     */
    public function getLanguagesAttribute($str) {
        return explode(',', $str);
    }

    /**
     * Accessor for $this->mainLanguage.
     *
     * @param $str
     * @return \App\Models\Language
     */
    public function getMainLanguageAttribute($str = null)
    {
        static $lang = false;

        if ($lang === false) {
            $lang = Language::findByCode($this->languages[0]);
        }

        return $lang;
    }
}

