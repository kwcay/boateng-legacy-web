<?php namespace App\Models;

use DB;
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
    protected $markdown;

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
    protected $relationsToBeImported = [];

    /**
     *
     */
    public function translations() {
        return $this->hasMany('App\Models\Translation');
    }

    /**
     *
     */
    public function languages() {
        return $this->belongsToMany('App\Models\Language');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Markdown parser.
        $this->markdown = new MarkdownExtra;
        $this->markdown->html5 = true;
    }

    /**
     * @param string $lang
     * @return mixed
     */
    public static function random($lang = null)
    {
        // Get query builder.
        $query = $lang instanceof Language ? $lang->definitions() : static::query();

        // Return a random definition.
        return $query->with('languages', 'translations')->orderByRaw('RAND()')->first();
    }

    public static function search($search, $offset = 0, $limit = 1000)
    {
        // Sanitize data.
        $search  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $search)));
        $offset = min(0, (int) $offset);
        $limit = min(1, (int) $limit);

        // Query the database
        $IDs = DB::table('definitions AS d')

            // Create a temporary score column so we can sort the IDs.
            ->selectRaw(
                'd.id, '.
                'MATCH(d.title, d.alt_titles) AGAINST(?) * 10 AS title_score, '.
                'MATCH(t.translation, t.meaning, t.literal) AGAINST(?) * 9 AS tran_score, '.
                'MATCH(d.data) AGAINST(?) * 8 AS data_score, '.
                'MATCH(d.tags) AGAINST(?) * 5 AS tags_score ',
                [$search, $search, $search, $search])

            // Join the translations table so we can search its columns.
            ->leftJoin('translations AS t', 't.definition_id', '=', 'd.id')

            // Match the fulltext columns against the search query.
            ->whereRaw(
                'MATCH(d.title, d.alt_titles) AGAINST(?) '.
                'OR MATCH(t.translation, t.meaning, t.literal) AGAINST(?) '.
                'OR MATCH(d.data) AGAINST(?) '.
                'OR MATCH(d.tags) AGAINST(?) ',
                [$search, $search, $search, $search])

            // Order by relevancy.
            ->orderByraw('(title_score + tran_score + data_score + tags_score) DESC')

            // Retrieve distcit IDs.
            ->distinct()->skip($offset)->take($limit)->lists('d.id');

        // Return results.
        return count($IDs) ? Definition::with('translations')->whereIn('id', $IDs)->get() : [];
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
     *
     * @param string $lang      The language code for the translation, e.g. 'en'.
     * @param string $attribute The attribute to update, e.g. 'translation' or 'meaning'.
     * @param string $data      The data to store for the attribute.
     * @param bool $create      Whether to create a new translation relation if one doesn't already exist.
     */
    protected function setTranslationAttribute($lang, $attribute, $data, $create = false)
    {
        // Update an existing translation.
        if ($translation = $this->getTranslationRelation($lang))
        {
            $translation->$attribute = $data;
            $translation->save();
        }

        // Create a new translation.
        elseif ($create === true && $attribute == 'translation')
        {
            $this->translations()->create([
                'language' => $lang,
                'translation' => $data
            ]);
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

    public function setTranslation($lang, $translation, $create = false) {
        return $this->setTranslationAttribute($lang, 'translation', $translation, $create);
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

    /**
     *
     */
    public function setRelationToBeImported($relation, $data) {
        $this->relationsToBeImported[$relation] = $data;
    }

    /**
     *
     */
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

    public function updateRelations(array $relations)
    {
        // Performance check.
        if (empty($relations)) {
            return;
        }

        foreach ($relations as $relation => $data)
        {
            $methodName = 'update'. ucfirst(strtolower($relation)) .'Relation';
            if (method_exists($this, $methodName)) {
                $this->$methodName($data);
            }
        }
    }

    public function updateTranslationRelation($data)
    {
        
    }
}
