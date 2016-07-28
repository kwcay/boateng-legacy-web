<?php

namespace App\Models\Definitions;

use Cache;
use Carbon\Carbon;
use App\Models\Language;
use App\Models\Definition;


class Expression extends Definition
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['type'] = static::TYPE_EXPRESSION;
    }

    /**
     * Retrieves a random phrase.
     *
     * @param string|App\Models\Language $lang
     * @param array $relations
     * @return mixed
     *
     * TODO: filter by phrase type.
     */
    public static function random($lang = null, array $relations = ['languages', 'translations'])
    {
        if (is_string($lang)) {
            $lang = Language::findByCode($lang);
        }

        // Get query builder.
        $query = $lang instanceof Language ? $lang->definitions() : static::query();

        // Return a random expression.
        return $query
            ->where('type', static::TYPE_EXPRESSION)
            ->with($relations)
            ->orderByRaw('RAND()')
            ->first();
    }

    /**
     * Retrieves expression of the day.
     *
     * @param string $lang
     * @return App\Models\Definition
     */
    public static function daily($lang = 'all')
    {
        $cacheKey = 'definitions.expression.daily.'.$lang;

        $expires = Carbon::now()->addDay();

        return Cache::remember($cacheKey, $expires, function () use ($lang) {
            return Expression::random($lang);
        });
    }

    /**
     * Does a fulltext search for a phrase.
     *
     * @param string $search
     * @param int $offset
     * @param int $limit
     *
     * TODO: filter by phrase type.
     */
    public static function search($query, array $options = [])
    {
        return parent::search($query, array_merge($options, [
            'type' => static::types()[static::TYPE_EXPRESSION],
        ]));
    }

    /**
     * Gets the list of sub types for this definition.
     */
    public function getSubTypes()
    {
        return $this->subTypes[Definition::TYPE_EXPRESSION];
    }
}
