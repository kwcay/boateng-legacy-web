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

class Alphabet extends Model
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
    public $obfuscatorId = 92;


    //
    //
    // Attributes used by App\Traits\SearchableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    CONST SEARCH_LIMIT = 10;        // Maximum number of results to return on a search.
    CONST SEARCH_QUERY_LENGTH = 3;  // Minimum length of search query.

    /**
     * Indicates whether search results can be filtered by tags.
     *
     * @var bool
     */
    public static $searchIsTaggable = false;


    //
    //
    // Attirbutes used by Illuminate\Database\Eloquent\Model
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden from the model's array form.
     */
    protected $hidden = ['id'];

    /**
     * Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * Defines relation to Language model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages() {
        return $this->belongsToMany('App\Models\Language');
    }


    //
    //
    // Helper methods
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Looks up an alphabet model by code.
     *
     * @param string|\App\Models\Alphabet $code
     * @return \App\Models\Alphabet|null
     */
    public static function findByCode($code)
    {
        // Performance check.
        if ($code instanceof static) {
            return $code;
        }

        // Retrieve alphabet by code.
        $code = preg_replace('/[^a-z\-]/', '', strtolower($code));
        return $code ? static::where('code', '=', ucfirst($code))->first() : null;
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
        $builder = DB::table('alphabets AS a')

            // TODO: Search language names and codes for languages that use "this" alphabet.
            // ...

            // Create temporary score columns so we can sort the IDs.
            ->selectRaw(
                'a.id, '.
                'a.name = ? AS name_score, ' .
                'a.name LIKE ? AS name_score_low, '.
                'MATCH(a.transliteration) AGAINST(?) AS transliteration_score, '.
                'a.code = ? AS code_score, ' .
                'a.script_code = ? AS script_code_score, ' .
                'a.letters LIKE ? AS letters_score ',
                [$term, '%'. $term .'%', $term, $term, $term, '%'. $term .'%']
            )

            // Try to search in a relevant way.
            ->whereRaw(
                '('.
                    'a.name = ? OR '.
                    'a.name LIKE ? OR '.
                    'MATCH(a.transliteration) AGAINST(?) OR '.
                    'a.code = ? OR '.
                    'a.script_code = ? OR '.
                    'a.letters LIKE ? '.
                ')',
                [$term, '%'. $term .'%', $term, $term, $term, '%'. $term .'%']
            );

        // Limit scope to a specific language.
        if (isset($options['lang']) && $lang = Language::findByCode($options['lang']))
        {
            $builder->join('alphabet_language AS pivot', 'pivot.alphabet_id', '=', 'a.id')
                ->where('pivot.language_id', '=', DB::raw($lang->id));
        }

        return $builder;
    }

    /**
     * Scores an alphabet model between 0 and 1.
     *
     * @param object $rawScore
     * @return float
     */
    protected static function getSearchScore($rawScore)
    {
        return (
            $rawScore->name_score * 10 +
            $rawScore->name_score_low * 1.5 +
            $rawScore->code_score * 10 +
            $rawScore->script_code_score * 7 +
            $rawScore->letters_score
        );
    }

    /**
     * Retrieves alphabets and their relations in the context of a search.
     *
     * @param array $IDs
     * @return \Illuminate\Support\Collection
     */
    protected static function getSearchResults(array $IDs) {
        return Alphabet::with('languages')->whereIn('id', $IDs)->get();
    }

    /**
     * Normalizes the search score and formats a model for search results.
     *
     * @param object $alphabet
     * @param object $scores
     * @param float $maxScore
     */
    protected static function normalizeSearchResult($alphabet, $scores, $maxScore)
    {
        // If the name is an exact match, assign max score.
        if ($scores->name_score > 0) {
            $alphabet->score = 1;
        }

        // If the code is an exact match, assign max score.
        elseif ($scores->code_score > 0) {
            $alphabet->score = 1;
        }

        // In any other case, assign a score out of 0.9.
        else {
            $alphabet->score = $scores->total * 0.9 / $maxScore;
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
}
