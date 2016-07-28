<?php

namespace App\Models\Definitions;

use Cache;
use Carbon\Carbon;
use App\Models\Language;
use App\Models\Definition;

class Word extends Definition
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set the definiiton type.
        $this->attributes['type'] = static::TYPE_WORD;
    }

    /**
     * Retrieves a random word.
     *
     * @param App\Models\Language $lang
     * @param array $relations
     * @return mixed
     */
    public static function random($lang = null, array $relations = ['languages', 'translations'])
    {
        if (is_string($lang)) {
            $lang = Language::findByCode($lang);
        }

        // Get query builder.
        $query = $lang instanceof Language ? $lang->definitions() : static::query();

        // Return a random definition.
        return $query
            ->where('type', static::TYPE_WORD)
            ->with($relations)
            ->orderByRaw('RAND()')
            ->first();
    }

    /**
     * Retrieves word of the day.
     *
     * @param string $lang
     * @return App\Models\Definition
     */
    public static function daily($lang = 'all')
    {
        $cacheKey = 'definitions.word.daily.'.$lang;

        $expires = Carbon::now()->addDay();

        return Cache::remember($cacheKey, $expires, function () use ($lang) {
            return Word::random($lang);
        });
    }

    /**
     * Searches the database for words.
     *
     * @param string $query     Search query.
     * @param array $options    Search options.
     * @return array
     */
    public static function search($query, array $options = [])
    {
        return parent::search($query, array_merge($options, [
            'type' => static::types()[static::TYPE_WORD],
        ]));
    }

    /**
     * Gets the list of sub types for this definition.
     */
    public function getSubTypes()
    {
        return $this->subTypes[Definition::TYPE_WORD];
    }
}
