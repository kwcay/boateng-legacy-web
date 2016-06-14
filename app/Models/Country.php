<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ExportableTrait as Exportable;
use App\Traits\SearchableTrait as Searchable;
use App\Traits\ObfuscatableTrait as ObfuscatesID;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class Country extends Model
{
    use CamelCaseAttrs, Exportable, ObfuscatesID, Searchable;


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
    // Attributes used by App\Traits\ObfuscatableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @var int
     */
    public $obfuscatorId = 11;


    //
    //
    // Attributes used by App\Traits\SearchableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    CONST SEARCH_LIMIT = 10;        // Maximum number of results to return on a search.
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
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden from the model's array form.
     *
     * @var array
     */
    protected $hidden = ['id'];

    /**
     * Attributes that SHOULD be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'uniqueId',
        'resourceType',
    ];


    //
    //
    // Helper methods
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Looks up a country model by code.
     *
     * @param string|\App\Models\Country $code
     * @return \App\Models\Country|null
     */
    public static function findByCode($code)
    {
        // Performance check.
        if ($code instanceof static) {
            return $code;
        }

        // Retrieve country by code.
        $code = preg_replace('/[^a-z]/', '', strtolower($code));
        return $code ? static::where('code', '=', strtoupper($code))->first() : null;
    }


    //
    //
    // Search-related methods.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @param string $term      Search query.
     * @param array $options    Search options.
     * @return Builder
     */
    protected static function getSearchQueryBuilder($term, array $options = [])
    {
        $builder = DB::table('countries AS c')

            // Create temporary score columns so we can sort the IDs.
            ->selectRaw(
                'c.id, '.
                'MATCH(c.name, c.alt_names) AGAINST(?) AS name_score, '.
                'c.name LIKE ? AS name_score_low, '.
                'c.alt_names LIKE ? AS alt_name_score_low, '.
                'c.code = ? AS code_score ',
                [$term, '%'. $term .'%', '%'. $term .'%', $term]
            )

            // Try to search in a relevant way.
            ->whereRaw(
                '('.
                    'MATCH(c.name, c.alt_names) AGAINST(?) OR '.
                    'c.name LIKE ? OR '.
                    'c.alt_names LIKE ? OR '.
                    'c.code = ? '.
                ')',
                [$term, '%'. $term .'%', '%'. $term .'%', $term]
            );

        return $builder;
    }

    /**
     * Scores a country result between 0 and 1.
     *
     * @param object $rawScore
     * @return float
     */
    protected static function getSearchScore($rawScore)
    {
        return (
            $rawScore->name_score * 10 +
            $rawScore->name_score_low * 3 +
            $rawScore->alt_name_score_low * 3 +
            $rawScore->code_score
        );
    }

    /**
     * Retrieves countries and their relations in the context of a search.
     *
     * @param array $IDs
     * @return \Illuminate\Support\Collection
     */
    protected static function getSearchResults(array $IDs) {
        return static::whereIn('id', $IDs)->get();
    }

    /**
     * Normalizes the search score and formats a model for search results.
     *
     * @param App\Models\Country $country
     * @param stdClass $scores
     * @param float $maxScore
     */
    protected static function normalizeSearchResult($country, $scores, $maxScore)
    {
        // If the code is an exact match, assign max score.
        if ($scores->code_score > 0) {
            $country->score = 1;
        }

        // In any other case, assign a score out of 0.9.
        else {
            $country->score = $scores->total * 0.9 / $maxScore;
        }
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
        return 'javascript:;';
    }

    /**
     * Accessor for $this->editUri.
     *
     * @return string
     */
    public function getEditUriAttribute() {
        return 'javascript:;';
    }

    /**
     * Accessor for $this->editUriAdmin.
     *
     * @return string
     */
    public function getEditUriAdminAttribute() {
        return 'javascript:;';
    }

    /**
     * Accessor for $this->resourceType.
     *
     * @return string
     */
    public function getResourceTypeAttribute() {
        return 'country';
    }
}
