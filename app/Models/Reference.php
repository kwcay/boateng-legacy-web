<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ExportableTrait as Exportable;
use App\Traits\SearchableTrait as Searchable;
use App\Traits\ObfuscatableTrait as ObfuscatesID;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class Reference extends Model
{
    use CamelCaseAttrs, ObfuscatesID, Searchable;

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
    public static $appendable = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


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
     * @param object $tag
     * @param object $scores
     * @param float $maxScore
     */
    protected static function normalizeSearchResult($tag, $scores, $maxScore)
    {
        // Assign a relative score out of 1.0
        $tag->score = $scores->total / $maxScore;
    }
}
