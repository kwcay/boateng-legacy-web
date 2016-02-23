<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Models;

use DB;
use Log;

use cebe\markdown\MarkdownExtra;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;


class Language extends Model
{
    use Validatable, Obfuscatable, Exportable, SoftDeletes, CamelCaseAttrs;

    /**
     *
     */
    private $markdown;

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
        'definitions',
        'pivot',
    ];

    /**
     * The attributes that should be hidden from the model's array form when exporting data to file.
     */
    protected $hiddenFromExport = [
        'id',
        'updated_at',
        'definitions',
        'pivot',
        'uri',
        'editUri',
        'resourceType',
    ];

    /**
     * Attributes that SHOULD be appended to the model's array form.
     */
    protected $appends = [
        'count',
        'uri',
        'editUri',
        'resourceType',
    ];

    /**
     * Attributes that CAN be appended to the model's array form.
     */
    public static $appendable = [
        'firstDefinition',
        'latestDefinition',
        'randomDefinition',
    ];

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
    ];

    /**
     * Validation rules.
     */
    public $validationRules  = [
        'code' => 'sometimes|required|min:3|max:7|unique:languages',
        'parent_code' => 'min:3|max:7',
        'name' => 'required|min:2',
        'alt_names' => 'min:2'
    ];

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
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

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
     * Looks up a language model by code.
     *
     * @param string|\App\Models\Language $code
     * @param array $embed
     * @return \App\Models\Language|null
     */
    public static function findByCode($code, array $embed = [])
    {
        // Performance check.
        if ($code instanceof static) {
            return $code;
        }

        // Retrieve langauge by code.
        $code = static::sanitizeCode($code);
        return $code ? static::with($embed)->where(['code' => $code])->first() : null;
    }

    /**
     * Looks up lannguages by name.
     *
     * @param string $term
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public static function search($term, $offset = 0, $limit = 100)
    {
        // Sanitize data.
        $term  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $term)));
        $offset = min(0, (int) $offset);
        $limit = min(1, (int) $limit);

        // Performance check.
        if (strlen($term) < 2) {
            return new Collection;
        }

        // Query the database
        $IDs = DB::table('languages AS l')

            // Create a temporary score column so we can sort the IDs.
            ->selectRaw(
                'l.id, l.name,'.
                'l.name = ? AS name_score, ' .
                'l.name LIKE ? AS name_score_low, '.
                'l.alt_names LIKE ? AS alt_score ',
                [$term, '%'. $term .'%', '%'. $term .'%']
            )

            // Try to search in a relevant way.
            ->whereRaw(
                '(l.name = ? OR ' .
                'l.name LIKE ? OR '.
                'l.alt_names LIKE ?)',
                [$term, '%'. $term .'%', '%'. $term .'%']
            )

            // Or match the language code.
            ->orWhere('code', '=', $term)

            // Order by relevancy.
            ->orderByraw('(name_score * 3 + name_score_low + alt_score) DESC')

            // Retrieve distcit IDs.
            ->distinct()->skip($offset)->take($limit)->lists('l.id');

        // Return results.
        return count($IDs) ? Language::whereIn('id', $IDs)->get() : new Collection;
    }

    /**
     *
     */
    public static function sortedBy($sort = 'name', $dir = 'asc')
    {
        return static::query()->orderBy($sort, $dir)->get();
    }

    /**
     * @param string $code
     * @return string|null
     */
    public static function sanitizeCode($code)
    {
        // Performance check.
        if (!is_string($code)) {
            return null;
        }

        // A language code can contain letters and dashes.
        $code = preg_replace('/[^a-z\-]/', '', strtolower($code));

        // And will have the format "abc" or "abc-def"
        return preg_match('/^([a-z]{3}(-[a-z]{3})?)$/', $code) ? $code : null;
    }

    /**
     * Gets the URI to the language edit form.
     *
     * @param bool $full
     * @return string
     *
     * @deprecated  Use url($this->editUri) instead.
     */
    public function getEditUri($full = true) {
        return route('admin.language.edit', ['code' => $this->code], $full);
    }

    // TODO: add description relations for each description (en, fr, ...)
    public function hasDescription($lang) {
        return false;
    }

    public function getDescription($lang = 'en') {
        return '';
    }

    public function setDescription($lang, $desc) {}


    //
    //
    // Accessors and mutators.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Accessor for $this->parentName.
     *
     * @return string
     */
    public function getParentNameAttribute($data = null) {
        return $this->parent ? $this->parent->name : '';
    }

    /**
     * Accessor for $this->parentLanguage.
     *
     * @return string
     */
    public function getParentLanguageAttribute($data = null) {
        return $this->parent ?: null;
    }

    /**
     * Accessor for $this->uri.
     *
     * @return string
     */
    public function getUriAttribute() {
        return route('language.show', ['code' => $this->code]);
    }

    /**
     * Accessor for $this->editUri.
     *
     * @return string
     */
    public function getEditUriAttribute() {
        return route('admin.language.edit', ['code' => $this->code]);
    }

    /**
     * Accessor for $this->count.
     *
     * @return int
     */
    public function getCountAttribute() {
        return $this->definitions()->count();
    }

    /**
     * Accessor for $this->firstDefinition.
     *
     * @return array
     */
    public function getFirstDefinitionAttribute()
    {
        $first = null;

        if ($definition = $this->definitions()->first())
        {
            $first = [
                'title' => $definition->title,
                'translation' => $definition->translation,
                'type' => $definition->type,
                'mainLanguage' => [
                    'code' => $definition->mainLanguage->code
                ]
            ];
        }

        return $first;
    }

    /**
     * Accessor for $this->latestDefinition.
     *
     * @return array
     */
    public function getLatestDefinitionAttribute()
    {
        $latest = null;

        if ($definition = $this->definitions()->orderBy('created_at', 'DESC')->first())
        {
            $latest = [
                'title' => $definition->title,
                'translation' => $definition->translation,
                'type' => $definition->type,
                'mainLanguage' => [
                    'code' => $definition->mainLanguage->code
                ]
            ];
        }

        return $latest;
    }

    /**
     * Accessor for $this->randomDefinition.
     *
     * @return array
     */
    public function getRandomDefinitionAttribute()
    {
        $random = null;

        if ($definition = $this->definitions()->orderByRaw('RAND()')->first())
        {
            $random = [
                'title' => $definition->title,
                'translation' => $definition->translation,
                'type' => $definition->type,
                'mainLanguage' => [
                    'code' => $definition->mainLanguage->code
                ]
            ];
        }

        return $random;
    }

    /**
     * Accessor for $this->resourceType.
     *
     * @return string
     */
    public function getResourceTypeAttribute() {
        return 'language';
    }


    //
    //
    // Import/export-related methods.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Creates the relation between an language and a definition.
     */
     public static function addRelatedDefinition($code, $def)
     {
         // Keep a static array of languages so that we don't have to
         // retrieve them again and again from the database.
         static $languages;

         // Performance check.
         if (!strlen($code) || !$def instanceof Definition) {
             Log::debug('Invalid code or definition object in Language::addRelatedDefinition.');
             return false;
         }

         // Retrieve language object.
         if (!isset($languages[$code]))
         {
             $languages[$code] = static::findByCode($code);

             if (!$languages[$code]) {
                 Log::debug('Language::addRelatedDefinition - Could not retrieve language object.');
                 return false;
             }
         }

         // Add relation.
         isset($def->id) && $def->id > 0
            ? $languages[$code]->definitions()->attach($def)
            : $languages[$code]->definitions()->save($def);

        return true;
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
    public static function checkAttributes($lang)
    {

        return true;
    }
}
