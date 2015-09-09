<?php namespace App\Models\Definitions;

use DB;
use Log;

use App\Models\Language;
use App\Models\Translation;
use App\Models\Definition;
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
class Word extends Definition
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        //
        $this->attributes['type'] = static::TYPE_WORD;
    }

    /**
     * Retrieves a random word.
     *
     * @param string $lang
     * @return mixed
     *
     * TODO: filter by word type.
     */
    public static function random($lang = null)
    {
        // Get query builder.
        $query = $lang instanceof Language ? $lang->definitions() : static::query();

        // Return a random definition.
        return $query->with('languages', 'translations')->orderByRaw('RAND()')->first();
    }

    /**
     * Does a fulltext search for a word.
     *
     * @param string $search
     * @param int $offset
     * @param int $limit
     *
     * TODO: filter by word type.
     */
    public static function search($search, $offset = 0, $limit = 1000, $langCode = false)
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
     * Gets the list of sub types for this definition.
     */
    public function getSubTypes() {
        return $this->subTypes[Definition::TYPE_WORD];
    }
}
