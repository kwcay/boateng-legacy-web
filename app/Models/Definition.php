<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Models;

use DB;
use Log;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Support\Arr;
use cebe\markdown\MarkdownExtra;
use App\Models\Definitions\Poem;
use App\Models\Definitions\Word;
use App\Models\Definitions\Phrase;
use App\Models\Definitions\Story;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasParamsTrait as HasParams;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class Definition extends Model
{
    use Validatable, Obfuscatable, Exportable, SoftDeletes, HasParams, CamelCaseAttrs;

    CONST TYPE_WORD = 0;        // Regular definitions.
    CONST TYPE_NAME = 1;        // Names.
    CONST TYPE_PHRASE = 10;     // Proverbs, sayings, etc.
    CONST TYPE_POEM = 20;       // Poems, songs, etc.
    CONST TYPE_STORY = 30;      // Short stories.

    CONST STATE_HIDDEN = 0;     // Hidden definition.
    CONST STATE_VISIBLE = 10;   // Default state.

    CONST SEARCH_LIMIT = 20;    // Maximum number of results to return on a search.

    /**
     * Definition types.
     */
    public $types = [
        0 => 'word',
        1 => 'name',
        10 => 'phrase',
        20 => 'poem',
        30 => 'story',
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

        // Types of names.
        1 => [
            'fam'   => 'family',
            'given' => 'given',
        ],

        // Types of phrases.
        10 => [
            'ex'    => 'expression',
            'prov'  => 'proverb',
            'sayng' => 'saying',
        ],

        //
        20 => [

        ],

        //
        30 => [

        ]
    ];

    /*
     * Defaults for definition sub types.
     */
    public $defaultSubTypes = [
        0 => 'n',
        1 => 'given',
        10 => 'sayng',
        20 => '',
        30 => '',
    ];

    /**
     * Definition state.
     */
    public $states = [
        0 => 'hidden',
        10 => 'visible'
    ];

    /**
     * The Markdown parser.
     */
    protected $markdown;

    /**
     *
     */
    public $exportFormats = ['yml', 'yaml', 'json', 'bgl', 'dict'];


    //
    //
    // Attirbutes used by Illuminate\Database\Eloquent\Model
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Associated MySQL table.
     */
    protected $table = 'definitions';

    /**
     * Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden from the model's array form.
     */
    protected $hidden = [
        'id',
        'updated_at',
        'deleted_at',
        'languages',
        'translations',
    ];

    /**
     * The attributes that should be hidden from the model's array form when exporting data to file.
     */
    protected $hiddenFromExport = [
        'id',
        'updated_at',
        'languages',
        'translations',
        'uri',
        'editUri',
        'resourceType',
        'uniqueId',
    ];

    /**
     * Attributes that SHOULD be appended to the model's array form.
     */
    protected $appends = [
        'translation',
        'language',
        'uri',
        'editUri',
        'uniqueId',
        'resourceType',
    ];

    /**
     * Attributes that CAN be appended to the model's array form.
     */
    public static $appendable = [
        'translation',
        'language',
        'mainLanguage',
    ];

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
        'params' => 'array',
    ];

    public $validationRules = [
        'title' => 'required|string|min:2',
        'alt_titles' => 'string|min:2',
        'type' => 'integer',
        'sub_type' => 'string',
        'state' => 'integer'
    ];

    /**
     * Defines the translation relations.
     */
    public function translations() {
        return $this->hasMany('App\Models\Translation', 'definition_id');
    }

    /**
     * Defines the language relations.
     */
    public function languages() {
        return $this->belongsToMany('App\Models\Language', 'definition_language', 'definition_id', 'language_id');
    }

    /**
     * Relations to be created when importing this definition.
     */
    protected $relationsToBeImported = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set some default values.
        // foreach ($this->validationRules as $key => $rule)
        // {
        //     if (!array_key_exists($key, $this->attributes)) {
        //         $this->attributes[$key] = strpos($rule, 'integer') ? 0 : '';
        //     }
        // }

        // Markdown parser.
        $this->markdown = new MarkdownExtra;
        $this->markdown->html5 = true;
    }


    //
    //
    // Helper methods
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Creates an instance of a definition type.
     *
     * @param int $type
     * @param array $attributes
     * @return App\Models\Definition|null
     */
    public static function getInstance($type, array $attributes = [])
    {
        // Check that the definition type is valid.
        $types = static::types();
        if (!array_key_exists($type, $types)) {
            return null;
        }

        // Return new instance.
        $className = '\\App\\Models\\Definitions\\'. ucfirst(strtolower($types[$type]));
        return new $className($attributes);
    }

    /**
     * Gets the valid definition types.
     *
     * @return array
     */
    public static function types() {
        return (new static)->types;
    }

    /**
     * Returns the name of a definition type.
     *
     * @param int $type
     * @return string
     */
    public static function getTypeName($type) {
        return (new Definition)->types[$type];
    }

    /**
     * Gets the list of sub types for this definition.
     *
     * @return array
     */
    public function getSubTypes() {
        // return $this->subTypes[$this->getAttributeFromArray('type')];
        return $this->subTypes[$this->rawType];
    }

    /**
     * Gets a random definition.
     *
     * @param App\Models\Language $lang
     * @return App\Models\Definition
     */
    public static function random($lang = null)
    {
        // Get query builder.
        $query = $lang instanceof Language ? $lang->definitions() : static::query();

        // Return a random definition.
        return $query->with('languages', 'translations')->orderByRaw('RAND()')->first();
    }

    /**
     * Searches the database for definitions.
     *
     * @param string $query     Search query.
     * @param array $options    Search options.
     * @return array
     */
    public static function search($query, array $options = [])
    {
        // Retrieve search parameters.
        $offset = isset($options['offset']) ? $options['offset'] : 0;
        $limit = isset($options['limit']) ? $options['limit'] : static::SEARCH_LIMIT;
        $method = isset($options['method']) ? $options['method'] : 'fulltext';

        switch (strtolower($method))
        {
            case 'like':
                $results = static::likeSearch($query, $offset, $limit, $options);
                break;

            default:
                $results = static::fulltextSearch($query, $offset, $limit, $options);
        }

        return $results;
    }

    /**
     * Performs a fulltext search.
     *
     * @param string $term      Search term.
     * @param int $offset       Search offset.
     * @param int $limit        Search limit (max 50).
     * @param array $options    Search options.
     * @return array
     */
    public static function fulltextSearch($term, $offset = 0, $limit = 50, array $options = [])
    {
        // Sanitize data and retrieve search options.
        $term = trim(preg_replace('/[\s+]/', ' ', $term));
        $offset = min(0, (int) $offset);
        $limit = max(1, min(static::SEARCH_LIMIT, (int) $limit));
        $lang = isset($options['lang']) ? Language::findByCode($options['lang']) : null;

        $type = null;
        if (isset($options['type']) && in_array($options['type'], static::types())) {
            $type = (int) array_flip(static::types())[$options['type']];
        }

        // Start building our database query.
        $builder = DB::table('definitions AS d')

            // Join the translations table so we can search its columns.
            ->leftJoin('translations AS t', 't.definition_id', '=', 'd.id')

            // Create temporary score columns so we can sort the IDs.
            ->selectRaw(
                'd.id, '.
                'MATCH(d.title, d.alt_titles) AGAINST(?) * 10 AS title_score, '.
                'MATCH(t.practical, t.literal, t.meaning) AGAINST(?) * 8 AS tran_score ',
                [$term, $term])

            // Match the fulltext columns against the search query.
            ->whereRaw(
                '( MATCH(d.title, d.alt_titles) AGAINST(?) '.
                'OR MATCH(t.practical, t.literal, t.meaning) AGAINST(?) )',
                [$term, $term])

            // Order by relevancy.
            ->orderByraw('(title_score + tran_score) DESC');

        // Limit scope to a specific language.
        if ($lang)
        {
            // We join the pivot table so that we may join the language table. Joining the language
            // table allows us to limit the search to a specific language.
            $builder->join('definition_language AS pivot', 'pivot.definition_id', '=', 'd.id')
                ->where('pivot.language_id', DB::raw($lang->id));
        }

        // Limit scope to a specific definition type.
        if (is_integer($type)) {
            $builder->where('d.type', '=', DB::raw($type));
        }

        // dd($builder->toSql());

        // Retrieve distinct IDs.
        $IDs = $builder->distinct()->skip($offset)->take($limit)->lists('d.id');

        // Return results.
        if (count($IDs))
        {
            $results = Definition::with('languages', 'translations')->whereIn('id', $IDs)->get();

            foreach ($results as $result) {
                $result->setAttribute('mainLanguage', $result->mainLanguage);
            }
        }

        else {
            $results = new Collection;
        }

        // Return results.
        return $results;
    }

    /**
     * Performs a basic comparison search.
     *
     * @param string $query     Search query.
     * @param int $offset       Search offset.
     * @param int $limit        Search limit (max 50).
     * @param array $options    Search options.
     * @return array
     */
    public static function likeSearch($query, $offset = 0, $limit = 50, array $options = [])
    {
        // Sanitize data.
        $query = trim(preg_replace('/[\s+]/', ' ', strip_tags($query)));
        $offset = min(0, (int) $offset);
        $limit = max(1, min(static::SEARCH_LIMIT, (int) $limit));
        $lang = isset($options['lang']) ? Language::findByCode($options['lang']) : null;

        // Query builder.
        $builder = $lang ? $lang->definitions() : static::query();

        // ...

        return new Collection;
    }


    //
    //
    // Translations-related methods.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Retrieves a translation relation.
     *
     * @param string $lang  A language code.
     * @return App\Models\Translation|false
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
     *
     * @param string $lang      A language code.
     * @param string $attribute The attribute to check.
     * @return bool
     */
    protected function _hasTranslationAttribute($lang, $attribute)
    {
        if ($translation = $this->getTranslationRelation($lang)) {
            return (strlen($translation->$attribute));
        }

        return false;
    }

    /**
     * Retrieves a translation attribute.
     *
     * @param string $lang      A language code.
     * @param string $attribute The attribute to retrieve.
     * @param string $default   The default value to return.
     * @return string
     */
    protected function _getTranslationAttribute($lang, $attribute, $default = null)
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
    protected function _setTranslationAttribute($lang, $attribute, $data, $create = false)
    {
        // Update an existing translation.
        if ($translation = $this->getTranslationRelation($lang))
        {
            $translation->$attribute = $data;
            $translation->save();
        }

        // Create a new translation.
        elseif ($create === true && $attribute == 'practical')
        {
            $this->translations()->create([
                'language' => $lang,
                'practical' => $data
            ]);
        }
    }

    //
    // Translations attribute.
    //

    public function hasPracticalTranslation($lang) {
        return $this->_hasTranslationAttribute($lang, 'practical');
    }

    public function getPracticalTranslation($lang = 'eng') {
        return $this->_getTranslationAttribute($lang, 'practical');
    }

    public function setPracticalTranslation($lang, $translation, $create = false) {
        return $this->_setTranslationAttribute($lang, 'practical', $translation, $create);
    }

    //
    // Literal translations attribute.
    //

    public function hasLiteralTranslation($lang) {
        return $this->_hasTranslationAttribute($lang, 'literal');
    }

    public function getLiteralTranslation($lang = 'en') {
        return $this->_getTranslationAttribute($lang, 'literal');
    }

    public function setLiteralTranslation($lang, $translation) {
        return $this->_setTranslationAttribute($lang, 'literal', $translation);
    }

    //
    // Meanings attribute.
    //

    public function hasMeaning($lang) {
        return $this->_hasTranslationAttribute($lang, 'meaning');
    }

    public function getMeaning($lang = 'en') {
        return $this->_getTranslationAttribute($lang, 'meaning');
    }

    public function setMeaning($lang, $meaning) {
        return $this->_setTranslationAttribute($lang, 'meaning', $meaning);
    }


    //
    //
    // Accessors and mutators.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Accessor for $this->mainLanguage.
     *
     * @param $str
     * @return \App\Models\Language
     */
    public function getMainLanguageAttribute()
    {
        $mainLanguage = null;

        // Loop through related languages.
        if ($code = $this->getParam('mainLang', false))
        {
            foreach ($this->languages as $lang)
            {
                if ($lang->code == $code)
                {
                    $mainLanguage = $lang;
                    break;
                }
            }
        }

        // Or pick first language as a default.
        if (!$mainLanguage) {
            $mainLanguage = $this->languages->first();
        }

        return $mainLanguage;
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
     * Mutator for $this->type.
     *
     * @param string|int $type
     * @return void
     */
    public function setTypeAttribute($type)
    {
        // Try to find the right type constant.
        if (in_array(strtolower($type), $this->types)) {
            $type = array_keys($this->types, strtolower($type))[0];
        }

        $this->attributes['type'] = $type;
    }

    /**
     * Accessor for $this->rawType.
     */
    public function getRawTypeAttribute($type = '') {
        return $this->getAttributeFromArray('type');
    }

    /**
     * Accessor for $this->subType.
     */
    public function getSubTypeAttribute($subType = '')
    {
        // "subType" will be null, since it will try to retrieve it from $this->attributes['subType']
        // which doesn't exist. We set it to the correct value here.
        if (!$subType = $this->rawSubType)
        {
            // Set and return a default type/sub-type.
            if (!$this->rawType) {
                $this->attributes['type'] = static::TYPE_WORD;
            }

            $this->attributes['sub_type'] = $subType = $this->defaultSubTypes[$this->rawType];
        }

        foreach ($this->types as $index => $type) {
            if (array_has($this->subTypes[$index], $subType)) {
                return array_get($this->subTypes[$index], $subType);
            }
        }

        return $subType;
    }

    /**
     * Mutator for $this->subType.
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
     * Accessor for $this->rawSubType.
     */
    public function getRawSubTypeAttribute($subType = '') {
        return $this->getAttributeFromArray('sub_type');
    }

    /**
     * Accessor for $this->state.
     *
     * @param int $state
     * @return string
     */
    public function getStateAttribute($state = 0) {
        return array_get($this->states, $state, $this->states[static::STATE_VISIBLE]);
    }

    /**
     * Mutator for $this->state.
     *
     * @param string $state
     * @return void
     */
    public function setStateAttribute($state)
    {
        // If the state is already numeric, assume we're using a constant.
        if (is_numeric($state) && array_key_exists($state, $this->states)) {
            $this->attributes['state'] = (int) $state;
        }

        // Else, try to find the right constant.
        else {
            $this->attributes['state'] = is_int(array_search(strtolower($state), $this->states)) ?
                array_flip($this->states)[$state] : Definition::STATE_VISIBLE;
        }
    }

    /**
     * Accessor for $this->rawState.
     */
    public function getRawStateAttribute($state = 0) {
        return $this->getAttributeFromArray('state');
    }

    /**
     * Accessor for $this->language.
     */
    public function getLanguageAttribute()
    {
        $codes = [];

        foreach ($this->languages as $lang) {
            $codes[$lang->code] = $lang->name;
        }

        return $codes;
    }

    /**
     * Accessor for $this->practicalTranslations.
     */
    public function getPracticalTranslationsAttribute()
    {
        $translations = [];

        foreach ($this->translations as $translation) {
            $translations[$translation->language] = $translation->practical;
        }

        return $translations;
    }

    /**
     * Accessor for $this->literalTranslations.
     */
    public function getLiteralTranslationsAttribute()
    {
        $literals = [];

        foreach ($this->translations as $translation) {
            $literals[$translation->language] = $translation->literal;
        }

        return $literals;
    }

    /**
     * Accessor for $this->meanings.
     */
    public function getMeaningsAttribute()
    {
        $meanings = [];

        foreach ($this->translations as $translation) {
            $meanings[$translation->language] = $translation->meaning;
        }

        return $meanings;
    }

    /**
     * Accessor for $this->translation. Used to combine translation data when arraying this model.
     */
    public function getTranslationAttribute()
    {
        return [
            'practical' => $this->practicalTranslations,
            'literal'   => $this->literalTranslations,
            'meaning'   => $this->meanings
        ];
    }

    /**
     * Accessor for $this->uri.
     *
     * @return string
     */
    public function getUriAttribute() {
        return url($this->mainLanguage->code .'/'. str_replace(' ', '_', $this->title));
    }

    /**
     * Accessor for $this->editUri.
     *
     * @return string
     */
    public function getEditUriAttribute() {
        return route('admin.definition.edit', ['id' => $this->getId()]);
    }

    /**
     * Accessor for $this->uniqueId.
     */
    public function getUniqueIdAttribute() {
        return $this->getUniqueId();
    }

    /**
     * Accessor for $this->resourceType.
     *
     * @return string
     */
    public function getResourceTypeAttribute() {
        return 'definition';
    }

    //
    //
    // Import/export-related methods.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


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
                    'literal' => Arr::get($relations['literals'], $lang, ''),
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

    /**
     * Updates the language relations.
     *
     * @param array $languages
     */
    public function updateLanguageRelation(array $languages)
    {
        // Perormance check.
        if (empty($languages)) {
            return;
        }

        // Make sure we have an array of IDs.
        foreach ($languages as $index => $langID)
        {
            if (!is_numeric($langID))
            {
                // If we can't find the language by code, assume it was invalid.
                if (!$lang = Language::findByCode($langID)) {
                    unset($languages[$index]);
                    continue;
                }

                // Replace the code with an ID.
                $languages[$index] = $lang->id;
            }
        }

        // Sync language relations.
        $this->languages()->sync($languages);

        // Make the first language the main language.
        if ($lang = Language::find($languages[0])) {
            $this->setParam('mainLang', $lang->code);
            $this->save();
        }
    }

    /**
     * Updates or creates one or more practical translations.
     *
     * @param array $translations
     */
    public function updatePracticalRelation(array $translations)
    {
        foreach ($translations as $code => $translation) {
            $this->setPracticalTranslation($code, $translation, true);
        }
    }

    /**
     * Updates one or more literal translations in the translation relations.
     *
     * @param array $translations
     */
    public function updateLiteralRelation(array $translations)
    {
        foreach ($translations as $code => $translation) {
            $this->setLiteralTranslation($code, $translation);
        }
    }

    /**
     * Updates one or more meanings in the translation relations.
     *
     * @param array $translations
     */
    public function updateMeaningRelation(array $meanings)
    {
        foreach ($meanings as $code => $meaning) {
            $this->setMeaning($code, $meaning);
        }
    }
}
