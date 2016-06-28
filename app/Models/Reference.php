<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ExportableTrait as Exportable;
use App\Traits\SearchableTrait as Searchable;
use App\Traits\ObfuscatableTrait as ObfuscatesID;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class Reference extends Model
{
    use CamelCaseAttrs, Exportable, ObfuscatesID, Searchable, SoftDeletes;


    //
    //
    // Attributes used by App\Traits\ExportableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * The attributes that should be hidden from the model's array form when exporting data to file.
     */
    protected $hiddenFromExport = [
        'id',
    ];

    //
    //
    // Attributes for App\Traits\ObfuscatableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @var int
     */
    public $obfuscatorId = 27;


    //
    //
    // Attributes for App\Traits\SearchableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    CONST SEARCH_LIMIT = 10;        // Maximum number of results to return on a search.
    CONST SEARCH_QUERY_LENGTH = 4;  // Minimum length of search query.

    /**
     * Indicates whether search results can be filtered by tags.
     *
     * @var bool
     */
    public static $searchIsTaggable = false;


    //
    //
    // Main attributes
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden from the model's array form.
     */
    protected $hidden = [
        'id',
        'pivot'
    ];

    /**
     * Attributes that SHOULD be appended to the model's array form.
     */
    protected $appends = [
        'uniqueId',
    ];

    /**
     * Attributes that CAN be appended to the model's array form.
     */
    public static $appendable = [
        'name',
        'shortCitation',
        'longCitation',
        'fullCitation',
        'editUri',
    ];

    /**
     * Supported reference types.
     */
    public static $types = [
        'article' => 'Article',
        'audio' => 'Audio clip',
        'book' => 'Book',
        'film' => 'Film',
        'interview' => 'Interview',
        'paper' => 'Research Paper',
        'person' => 'Person',
        'report' => 'Report',
        'social' => 'Social media',
        'song' => 'Song',
        'video' => 'Video clip',
        'website' => 'Website',
        'other' => 'Miscellaneous',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Validation rules.
     */
    public $validationRules  = [
        'type' => 'required|min:1|max:20',
        'data' => 'required|array',
    ];

    /**
     * Attributes that should be cast when assigned.
     */
    protected $casts = [
        'type' => 'string',
        'data' => 'json',
    ];


    //
    //
    // Relations
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Defines relation to Translation model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function translations() {
        return $this->morphedByMany('App\Models\Translation', 'referenceable');
    }

    /**
     * Defines relation to Data model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function data() {
        return $this->morphedByMany('App\Models\Data', 'referenceable');
    }

    /**
     * Defines relation to Media model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function media() {
        return $this->morphedByMany('App\Models\Media', 'referenceable');
    }


    //
    //
    // Methods for App\Traits\SearchableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @param string $term      Search query.
     * @param array $options    Search options.
     * @return Builder
     */
    protected static function getSearchQueryBuilder($term, array $options = [])
    {
        return static::selectRaw(
            'id, ' .
            'MATCH(string) AGAINST(?) AS score_high, '.
            '`string` LIKE ? AS score_low',
            [$term, '%'. $term .'%']
        )
        ->whereRaw(
            '('.
                'MATCH(string) AGAINST(?) OR '.
                '`string` LIKE ? '.
            ')',
            [$term, '%'. $term .'%']
        );
    }

    /**
     * @param object $rawScore
     * @return float
     */
    protected static function getSearchScore($rawScore)
    {
        return (
            $rawScore->score_high * 10 +
            $rawScore->score_low * 3
        );
    }

    /**
     * @param array $IDs
     * @return \Illuminate\Support\Collection
     */
    protected static function getSearchResults(array $IDs) {
        return static::whereIn('id', $IDs)->get();
    }

    /**
     * Normalizes the search score and formats a model for search results.
     *
     * @param object $reference
     * @param object $scores
     * @param float $maxScore
     * @return void
     */
    protected static function normalizeSearchResult($reference, $scores, $maxScore)
    {
        // Assign a relative score out of 1.0
        $reference->score = $scores->total / $maxScore;
    }


    //
    //
    // Helper methods
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Returns an abridged version of a name, in the format J. Doe.
     *
     * @param string $dataKey   Key of data parameter for the name.
     * @return string|array
     */
    public function getAbridgedName($dataKey)
    {
        $name = $this->getDataParam($dataKey);

        // Convert many names.
        if (strpos($name, ';') !== false)
        {
            $abridged = '';
            $names = @explode(';', $name);

            for ($i = 0; $i < count($names); $i++) {
                $names[$i] = $this->getAbridgedName($names[$i]);
            }

            return implode(';', $names);
        }

        // Performance check.
        if (strpos($name, ' ') === false) {
            return $name;
        }

        // Replace all names by their initial, except for the last name.
        $abridged = '';
        $names = @explode(' ', $name);

        for ($i = 0; $i < count($names) - 1; $i++)
        {
            $abridged .= $names[$i][0] .'. ';
        }

        return $abridged . $names[$i];
    }

    /**
     * Returns the data array.
     */
    public function getDataArray()
    {
        return $this->data;
    }

    /**
     * Checks whether a data parameter exists or not.
     *
     * @param string $key
     * @return mixed
     */
    public function hasDataParam($key) {
        return array_has($this->data, $key);
    }

    /**
     * Retrieves a data parameter.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getDataParam($key, $default = null) {
        return array_get($this->data, $key, $default);
    }

    /**
     * Stores a data parameter.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setDataParam($key, $value = null)
    {
        $oldValue = $this->getDataParam($key);
        $data = $this->data;
        array_set($data, $key, $value);
        $this->data = $data;

        return $oldValue;
    }


    //
    //
    // Accessors and mutators.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Accessor for $this->name.
     *
     * @return string
     */
    public function getNameAttribute($name = '')
    {
        switch ($this->type)
        {
            case 'person':
                $name =  $this->getDataParam('givenName') .' '. $this->getDataParam('otherNames');
                break;

            case 'article':
            case 'audio':
            case 'book':
            case 'film':
            case 'interview':
            case 'paper':
            case 'report':
            case 'song':
            case 'video':
            case 'website':
            default:
                $name = $this->getDataParam('title');
                break;
        }

        return $name;
    }

    /**
     * Accessor for $this->shortCitation.
     *
     * @return string
     */
    public function getShortCitationAttribute($citation = '')
    {
        $strLimit = 30;

        switch ($this->type)
        {
            case 'film':
                $short =
                    '&quot;'. str_limit($this->getDataParam('title'), $strLimit) .'&quot; '.
                    'by '. $this->getAbridgedName('director');
                break;

            case 'interview':
                $short =
                    '&quot;'. str_limit($this->getDataParam('title'), $strLimit) .'&quot; '.
                    'with '. $this->getAbridgedName('interviewee');
                break;

            case 'person':
                $short =  $this->getDataParam('givenName') .' '. $this->getDataParam('otherNames');

                // Append birthplace to short name.
                if (strlen($this->getDataParam('cityOfBirth'))) {
                    $short .= ' from '. $this->getDataParam('cityOfBirth');
                }

                elseif (strlen($this->getDataParam('countryOfBirth'))) {
                    $short .= ' from '. $this->getDataParam('countryOfBirth');
                }
                break;

            case 'song':
                $short =
                    '&quot;'. str_limit($this->getDataParam('title'), $strLimit) .'&quot; '.
                    'featuring '.  $this->getAbridgedName('mainArtist');
                break;

            case 'video':
                $short =
                    '&quot;'. str_limit($this->getDataParam('title'), $strLimit) .'&quot; '.
                    'by '. $this->getAbridgedName('creator');
                break;

            case 'website':
                $short =
                    '&quot;'. str_limit($this->getDataParam('title'), $strLimit) .'&quot; '.
                    'from '. $this->getDataParam('name');
                break;

            case 'article':
            case 'audio':
            case 'book':
            case 'paper':
            case 'report':
            default:
                $short =
                    '&quot;'. str_limit($this->getDataParam('title'), $strLimit) .'&quot; '.
                    'by '. $this->getAbridgedName('author');
                break;
        }

        // Append year to short name.
        if (strlen($this->getDataParam('date'))) {
            $short .= ' ('. date('Y', strtotime($this->getDataParam('date'))) .')';
        }

        return $short;
    }

    /**
     * Accessor for $this->longCitation.
     *
     * @return string
     */
    public function getLongCitationAttribute($citation = '')
    {
        switch ($this->type)
        {
            case 'film':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; '.
                    'by '. $this->getAbridgedName('director');
                break;

            case 'interview':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; '.
                    'with '. $this->getAbridgedName('interviewee');
                break;

            case 'person':
                $short =  $this->getDataParam('givenName') .' '. $this->getDataParam('otherNames');

                // Append birthplace to short name.
                if (strlen($this->getDataParam('cityOfBirth'))) {
                    $short .= ' from '. $this->getDataParam('cityOfBirth');
                }

                elseif (strlen($this->getDataParam('countryOfBirth'))) {
                    $short .= ' from '. $this->getDataParam('countryOfBirth');
                }
                break;

            case 'song':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; featuring '.  $this->getAbridgedName('mainArtist');
                break;

            case 'video':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; by '. $this->getAbridgedName('creator');
                break;

            case 'website':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; from '. $this->getDataParam('name');
                break;

            case 'article':
            case 'audio':
            case 'book':
            case 'paper':
            case 'report':
            default:
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; by '. $this->getAbridgedName('author');
                break;
        }

        // Append year to short name.
        if (strlen($this->getDataParam('date'))) {
            $short .= ' ('. date('Y', strtotime($this->getDataParam('date'))) .')';
        }

        return $short;
    }

    /**
     * Accessor for $this->fullCitation.
     *
     * @return string
     */
    public function getFullCitationAttribute($citation = '')
    {
        switch ($this->type)
        {
            case 'film':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; '.
                    'by '. $this->getAbridgedName('director');
                break;

            case 'interview':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; '.
                    'with '. $this->getAbridgedName('interviewee');
                break;

            case 'person':
                $short =  $this->getDataParam('givenName') .' '. $this->getDataParam('otherNames');

                // Append birthplace to short name.
                if (strlen($this->getDataParam('cityOfBirth'))) {
                    $short .= ' from '. $this->getDataParam('cityOfBirth');
                }

                elseif (strlen($this->getDataParam('countryOfBirth'))) {
                    $short .= ' from '. $this->getDataParam('countryOfBirth');
                }
                break;

            case 'song':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; featuring '.  $this->getAbridgedName('mainArtist');
                break;

            case 'video':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; by '. $this->getAbridgedName('creator');
                break;

            case 'website':
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; from '. $this->getDataParam('name');
                break;

            case 'article':
            case 'audio':
            case 'book':
            case 'paper':
            case 'report':
            default:
                $short =
                    '&quot;'. $this->getDataParam('title') .'&quot; by '. $this->getAbridgedName('author');
                break;
        }

        // Append year to short name.
        if (strlen($this->getDataParam('date'))) {
            $short .= ' ('. date('Y', strtotime($this->getDataParam('date'))) .')';
        }

        return $short;
    }

    /**
     * Accessor for $this->typeName.
     *
     * @return string
     */
    public function getTypeNameAttribute() {
        return static::$types[$this->type];
    }

    /**
     * Accessor for $this->uri.
     *
     * @return string
     */
    public function getUriAttribute() {
        return 'javascript:;';
    }

    /**
     * Accessor for $this->editUri.
     *
     * @return string
     */
    public function getEditUriAttribute() {
        return route('r.reference.edit', ['id' => $this->uniqueId, 'return' => 'create']);
    }

    /**
     * Accessor for $this->editUriAdmin.
     *
     * @return string
     */
    public function getEditUriAdminAttribute() {
        return route('r.reference.edit', ['id' => $this->uniqueId, 'return' => 'admin']);
    }
}
