<?php namespace App\Models;

use DB;
use Log;

use cebe\markdown\MarkdownExtra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasParamsTrait as HasParams;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;


class Language extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes, HasParams;

    private $markdown;

    /**
     * @var array   Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
    * The attributes that should be hidden for arrays.
    */
    protected $hidden = ['id', 'params', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['parent_name'];

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

    /**
     * Looks up a language model by code.
     *
     * @param string $code
     * @return \App\Models\Language
     */
    public static function findByCode($code) {
        return static::where(['code' => $code])->first();
    }

    public static function search($search, $offset = 0, $limit = 100)
    {
        // Sanitize data.
        $search  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $search)));
        $offset = min(0, (int) $offset);
        $limit = min(1, (int) $limit);

        // Query the database
        $IDs = DB::table('languages AS l')

            // Create a temporary score column so we can sort the IDs.
            ->selectRaw('l.id, MATCH(l.name, l.alt_names) AGAINST(?) AS score', [$search])

            // Match the fulltext columns against the search query.
            ->whereRaw('MATCH(l.name, l.alt_names) AGAINST(?)', [$search])

            // Order by relevancy.
            ->orderBy('score', 'DESC')

            // Retrieve distcit IDs.
            ->distinct()->skip($offset)->take($limit)->lists('l.id');

        // Return results.
        return count($IDs) ? Language::whereIn('id', $IDs)->get() : [];
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

    // TODO: add description relations for each description (en, fr, ...)
    public function hasDescription($lang) {
        return false;
    }

    public function getDescription($lang = 'en') {
        return '';
    }

    public function setDescription($lang, $desc) {}

    /**
     * Accessor for $this->parent_name.
     *
     * @return string
     */
    public function getParentNameAttribute($data = null) {
        return $this->getParam('parentName', '');
    }

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
