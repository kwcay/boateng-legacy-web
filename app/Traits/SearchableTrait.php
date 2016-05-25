<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Traits;

use Illuminate\Support\Collection;

trait SearchableTrait
{
    /**
     * Searches the database for models related to the search query.
     *
     * @param string $term      Search query.
     * @param array $options    Search options.
     * @return \Illuminate\Support\Collection
     */
    public static function search($term, array $options = [])
    {
        // Format search parameters.
        $term = $fullTerm = trim(preg_replace('/[\s+]/', ' ', $term));
        $offset = min(0, array_get($options, 'offset', 0));
        $limit = max(1, min(static::SEARCH_LIMIT, array_get($options, 'limit', static::SEARCH_LIMIT)));

        // Retrieve tags. We look for the occurence of "#" before doing a preg_match
        // for performance.
        if (strpos($term, '#') !== false && preg_match('/#([a-z0-9\-_]+)/i', $term) > 0)
        {
            $options['tags'] = isset($options['tags']) ? $options['tags'] : [];

            preg_match_all('/#([a-z0-9\-_]+)/i', $term, $matches);

            foreach ($matches[1] as $tag)
            {
                $options['tags'][] = $tag;
                $term = preg_replace("/#{$tag}/", '', $term);
            }

            $term = trim($term);
        }

        // Performance check.
        $lengthCheck = static::$searchIsTaggable ? $fullTerm : $term;
        if (strlen($lengthCheck) < static::SEARCH_QUERY_LENGTH) {
            return new Collection;
        }

        // Build SQL query.
        $builder = static::getSearchQueryBuilder($term, $options);
        // dd($builder->distinct()->toSql());
        // dd($builder->distinct()->get());

        // Retrieve IDs and scores of the results.
        $rawScores = $builder->distinct()->skip($offset)->take($limit)->get();
        // dd($rawScores);

        // Format results.
        if (count($rawScores))
        {
            // Loop through raw scores to (1) create an array of IDs, and (2) collect score data
            // and find the highest score so we can normalize them to 1.
            $IDs = $scores = $results = [];

            foreach ($rawScores as $rawScore)
            {
                $IDs[] = $rawScore->id;
                $scores[$rawScore->id] = $rawScore;
                $scores[$rawScore->id]->total = static::getSearchScore($rawScore);
            }

            // Pull results and their relations from the DB.
            $unsorted = static::getSearchResults($IDs);

            // Add normalized score and other attributes.
            $maxScore = collect($scores)->max('total');
            foreach ($unsorted as $model) {
                static::normalizeSearchResult($model, $scores[$model->id], $maxScore);
            }

            $results = $unsorted->sortByDesc(function($searchable) {
                return $searchable->score;
            })->values();
        }

        else {
            $results = new Collection;
        }

        // Return results.
        return $results;
    }
}
