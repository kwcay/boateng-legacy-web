<?php namespace App\Models;

use URL;

use App\Models\Language;
use Illuminate\Support\Arr;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Definition extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes;

    /**
     * @var array   Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * @var array   Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    protected $casts = [
        'translations' => 'array',
        'meanings' => 'array',
        'params' => 'array'
    ];

    /**
     * @var array  Array to help validate input data
     */
    public $validationRules = [
        'data'      => 'required|min:2',
        'languages' => 'required|min:2|regex:/^([a-z, ]+)$/',
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

    public function getLanguagesAttribute($str) {
        return explode(',', $str);
    }

    public function getMainLanguageAttribute($str)
    {
        static $lang = false;

        if ($lang === false) {
            $lang = Language::findByCode($this->getAttribute('languages')[0]);
        }

        return $lang;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getUri($full = true) {
        $path   = $this->mainLanguage->getAttribute('code') .'/'. str_replace(' ', '_', $this->getAttribute('data'));
        return $full ? URL::to($path) : $path;
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
        return Arr::has($this->translations, $lang);
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
    // Methods dealing with detailed meanings.
    //

    public function hasMeaning($lang) {
        return Arr::has($this->meanings, $lang);
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
}

