<?php namespace App\Models;

use App\Models\Language;
use Illuminate\Support\Arr;
use cebe\markdown\MarkdownExtra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasParamsTrait as HasParams;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;

/**
 *
 */
class Definition extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes, HasParams;

    CONST TYPE_WORD = 0;        // Regular definitions.
    CONST TYPE_PHRASE = 1;      // Proverbs, sayings, etc.
    CONST TYPE_POEM = 2;        // Poems, songs, etc.
    CONST TYPE_STORY = 3;       // Short stories.

    private $markdown;

    /**
     * @var array
     */
    public $types = [
        0 => 'word',
        1 => 'phrase',
        2 => 'poem',
        3 => 'story',
    ];

    /**
     * Attributes which aren't mass-assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Attributes that should be cast when assigned.
     */
    protected $casts = [
        'title' => 'string',
        'extra_data' => 'string',
        'type' => 'integer',
        'tags' => 'string',
        'state' => 'integer',
        'params' => 'array',
    ];

    public $validationRules = [
        'title' => 'required|string|min:2',
        'type' => 'required|integer',
        'tags' => 'string|min:2|regex:/^([a-z, \-]+)$/i',
        'languages' => 'required'
    ];

    public $exportFormats = ['yml', 'yaml', 'json', 'bgl', 'dict'];

    /**
     * Parts of speech. Used for "word" type.
     *
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

    /**
     * @var array
     */
    public $typesOfPhrases = [
        'ex'    => 'expression',
        'prov'  => 'proverb',
        'say'   => 'saying',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Markdown parser.
        $this->markdown = new MarkdownExtra;
        $this->markdown->html5 = true;
    }

    public function translations() {
        return $this->hasMany('App\Models\Translations');
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Languages');
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
