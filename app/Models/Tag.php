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
use App\Traits\ValidatableTrait as Validatable;
use App\Traits\ObfuscatableTrait as ObfuscatesID;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class Tag extends Model
{
    use CamelCaseAttrs, Exportable, ObfuscatesID, Searchable, SoftDeletes, Validatable;


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
    public $obfuscatorId = 29;


    //
    //
    // Attributes for App\Traits\SearchableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    CONST SEARCH_LIMIT = 15;        // Maximum number of results to return on a search.
    CONST SEARCH_QUERY_LENGTH = 2;  // Minimum length of search query.

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
    public static $appendable = [];

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
        'title' => 'required|min:2'
    ];


    //
    //
    // Relations
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Defines relation to Tag model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function definitions() {
        return $this->belongsToMany('App\Models\Definition', 'definition_tag', 'tag_id', 'definition_id');
    }


    //
    //
    // Main methods
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
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
            'MATCH(title) AGAINST(?) AS title_score, '.
            'title LIKE ? AS title_score_low',
            [$term, '%'. $term .'%']
        )
        ->whereRaw(
            '('.
                'MATCH(title) AGAINST(?) OR '.
                'title LIKE ? '.
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
            $rawScore->title_score * 10 +
            $rawScore->title_score_low * 3
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
     * @param object $tag
     * @param object $scores
     * @param float $maxScore
     */
    protected static function normalizeSearchResult($tag, $scores, $maxScore)
    {
        // Assign a relative score out of 1.0
        $tag->score = $scores->total / $maxScore;
    }


    //
    //
    // Accessors and mutators.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Accessor for $this->uri.
     *
     * @return string
     */
    public function getUriAttribute() {
        return url('/') . '?q=%23' . $this->title;
    }

    /**
     * Accessor for $this->editUri.
     *
     * @return string
     */
    public function getEditUriAttribute() {
        return route('r.tag.edit', ['id' => $this->uniqueId, 'return' => 'summary']);
    }

    /**
     * Accessor for $this->editUriAdmin.
     *
     * @return string
     */
    public function getEditUriAdminAttribute() {
        return route('r.tag.edit', ['id' => $this->uniqueId, 'return' => 'admin']);
    }

    /**
     * Accessor for $this->definitionCount
     *
     * @return string
     */
    public function getDefinitionCountAttribute() {
        return number_format($this->definitions()->count());
    }
}
