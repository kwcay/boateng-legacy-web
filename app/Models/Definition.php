<?php namespace App\Models;

use Log;

use App\Models\Language;
use App\Models\Translation;
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

    CONST STATE_HIDDEN = 0;     // Hidden definition.
    CONST STATE_VISIBLE = 1;    // Default state.

    /**
     * Definition types.
     */
    public $types = [
        0 => 'word',
        1 => 'phrase',
        2 => 'poem',
        3 => 'story',
    ];

    /**
     * Definition sub-types.
     */
    public $subTypes = [

        // Parts of speech.
        // See: http://www.edb.utexas.edu/minliu/pbl/ESOL/help/libry/speech.htm
        // See: http://www.aims.edu/student/online-writing-lab/grammar/parts-of-speech
        0 => [
            'adj'   => 'adjective',
            'adv'   => 'adverb',
            'conn'  => 'connective',
            'ex'    => 'exclamation',
            'pre'   => 'preposition',
            'pro'   => 'pronoun',
            'n'     => 'noun',
            'v'     => 'verb',
        ],

        // Types of phrases.
        1 => [
            'ex'    => 'expression',
            'prov'  => 'proverb',
            'say'   => 'saying',
        ],

        //
        2 => [

        ],

        //
        3 => [

        ]
    ];

    /**
     * Definition states.
     */
    public $states = [
        0 => 'hidden',
        1 => 'visible'
    ];

    /**
     * The Markdown parser.
     */
    private $markdown;

    /**
     * Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
    * The attributes that should be hidden for arrays.
    */
    protected $hidden = ['id', 'state', 'params', 'created_at', 'updated_at', 'deleted_at', 'languages'];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['language', 'uri'];

    /**
     * Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * Attributes that should be cast when assigned.
     */
    protected $casts = [
        'title' => 'string',
        'alt_titles' => 'string',
        'type' => 'integer',
        'data' => 'string',
        'tags' => 'string',
        'state' => 'integer',
        'params' => 'array',
    ];

    public $validationRules = [
        'title' => 'required|string|min:2',
        'alt_titles' => 'string|min:2',
        'data' => 'string',
        'type' => 'required|integer',
        'sub_type' => 'string',
        'tags' => 'string|min:2|regex:/^([a-z, \-]+)$/i',
        'state' => 'required|integer'
    ];

    public $exportFormats = ['yml', 'yaml', 'json', 'bgl', 'dict'];

    /**
     * Relations to be created when importing this definition.
     */
    private $relationsToBeImported = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Markdown parser.
        $this->markdown = new MarkdownExtra;
        $this->markdown->html5 = true;
    }

    public function translations() {
        return $this->hasMany('App\Models\Translation');
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Language');
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
     * Retrieves a translation relation.
     */
    public function getTranslationRelation($lang)
    {
        $found = Arr::where($this->translations, function($key, $translation) use($lang) {
            return $translation->language == $lang;
        });

        return count($found) == 1 ? $found[0] : false;
    }

    /**
     * Checks whether the attribute of a translation is empty or not.
     */
    protected function hasTranslationAttribute($lang, $attribute)
    {
        if ($translation = $this->getTranslationRelation($lang)) {
            return strlen($translation->$attribute);
        }

        return false;
    }

    /**
     * Retrieves a translation attribute.
     */
    protected function getTranslationAttribute($lang, $attribute, $default = null)
    {
        if ($translation = $this->getTranslationRelation($lang)) {
            return strlen($translation->$attribute) ? $translation->$attribute : $default;
        }

        return $default;
    }

    /**
     * Sets a translation attribute.
     */
    protected function setTranslationAttribute($lang, $attribute, $data)
    {
        if ($translation = $this->getTranslationRelation($lang))
        {
            $translation->$attribute = $data;
            $translation->save();
        }
    }

    //
    // Methods dealing with translations.
    //

    public function hasTranslation($lang) {
        return $this->hasTranslationAttribute($lang, 'translation');
    }

    public function getTranslation($lang = 'en') {
        return $this->getTranslationAttribute($lang, 'translation');
    }

    public function setTranslation($lang, $translation) {
        return $this->setTranslationAttribute($lang, 'translation', $translation);
    }

    //
    // Methods dealing with literal translations.
    //

    public function hasLiteralTranslation($lang) {
        return $this->hasTranslationAttribute($lang, 'literal');
    }

    public function getLiteralTranslation($lang = 'en') {
        return $this->getTranslationAttribute($lang, 'literal');
    }

    public function setLiteralTranslation($lang, $translation) {
        return $this->setTranslationAttribute($lang, 'literal', $translation);
    }

    //
    // Methods dealing with detailed meanings.
    //

    public function hasMeaning($lang) {
        return $this->hasTranslationAttribute($lang, 'meaning');
    }

    public function getMeaning($lang = 'en') {
        return $this->getTranslationAttribute($lang, 'meaning');
    }

    public function setMeaning($lang, $meaning) {
        return $this->setTranslationAttribute($lang, 'meaning', $meaning);
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getUri($full = false) {
        $path   = $this->mainLanguage->code .'/'. str_replace(' ', '_', $this->title);
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
     * Accessor for $this->mainLanguage.
     *
     * @param $str
     * @return \App\Models\Language
     */
    public function getMainLanguageAttribute()
    {
        static $main = false;

        if ($main === false)
        {
            // Loop through related languages.
            if ($code = $this->getParam('mainLang', false))
            {
                foreach ($this->languages as $lang)
                {
                    if ($lang->code == $code)
                    {
                        $main = $lang;
                        break;
                    }
                }
            }

            // Or pick first language as a default.
            if ($main === false) {
                $main = $this->languages[0];
            }
        }

        return $main;
    }

    /**
     * Accessor for $this->type.
     *
     * @param int $type
     * @return string
     */
    public function getTypeAttribute($type = 0) {
        return Arr::get($this->types, $type, $this->types[0]);
    }

    /**
     * Accessor for $this->sub_type.
     *
     * @param string $subType
     * @return string
     */
    public function getSubTypeAttribute($subType = '')
    {
        foreach ($this->types as $index => $type) {
            if (Arr::has($this->subTypes[$index], $subType)) {
                return Arr::get($this->subTypes[$index], $subType);
            }
        }

        return $subType;
    }

    /**
     * Mutator for $this->sub_type.
     *
     * @param string $subType
     * @return void
     */
    public function setSubTypeAttribute($subType)
    {
        // Try to find the right key for this sub-type.
        $typeKey = $this->attributes['type'];
        if (in_array(strtolower($subType), $this->subTypes[$typeKey])) {
            $subType = array_keys($this->subTypes[$typeKey], strtolower($subType))[0];
        }

        $this->attributes['sub_type'] = $subType;
    }

    /**
     * Accessor for $this->state.
     *
     * @param int $state
     * @return string
     */
    public function getStateAttribute($state = 0) {
        return Arr::get($this->states, $state, $this->states[1]);
    }

    /**
     * Accessor for $this->language.
     *
     * @return string
     */
    public function getLanguageAttribute() {
        return [
            'code' => $this->mainLanguage->code,
            'name' => $this->mainLanguage->name,
            'uri' => $this->mainLanguage->getUri(true)
        ];
    }

    /**
     * Accessor for $this->uri.
     *
     * @return string
     */
    public function getUriAttribute() {
        return $this->getUri(true);
    }

    public function setRelationToBeImported($relation, $data) {
        $this->relationsToBeImported[$relation] = $data;
    }

    public function getRelationsToBeImported() {
        return $this->relationsToBeImported;
    }

    /**
     * Checks Definition properties before saving to database.
     *
     * @param \App\Models\Definition $def
     * @return bool
     */
    public static function checkAttributes($def)
    {
        // Check relations to be imported, if any.
        $relations = $def->getRelationsToBeImported();
        if (count($relations))
        {
            // This definition must exist in some language.
            if (!isset($relations['languages']) || !count($relations['languages'])) {
                return false;
            }

            // Check that languages exist in our database.
            foreach ($relations['languages'] as $code) {
                if (!Language::where('code', $code)->exists()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Tries to import relations when importing a definition.
     *
     * @param \App\Models\Definition $def
     * @return bool
     */
    public static function importRelations($def)
    {
        $relations = $def->getRelationsToBeImported();
        if (!count($relations)) {
            return true;
        }

        // Update language relations.
        foreach ($relations['languages'] as $code) {
            if (!Language::addRelatedDefinition($code, $def)) {
                return false;
            }
        }

        // Update translations relations.
        if (isset($relations['translations']) && count($relations['translations']))
        {
            foreach ($relations['translations'] as $lang => $translation)
            {
                $def->translations()->save(new Translation([
                    'language' => $lang,
                    'translation' => $translation,
                    'meaning' => Arr::get($relations['meanings'], $lang, '')
                ]));
            }
        }

        return true;
    }
}
