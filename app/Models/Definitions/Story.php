<?php

namespace App\Models\Definitions;

use App\Models\Definition;


class Story extends Definition
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['type'] = static::TYPE_STORY;
    }

    /**
     * Retrieves a random story.
     *
     * @param string $lang
     * @return mixed
     *
     * TODO: filter by story type.
     */
    public static function random($lang = null)
    {
        abort(501, 'App\Models\Definitions\Story::random not implemented.');
    }

    /**
     * Does a fulltext search for a story.
     *
     * @param string $search
     * @param int $offset
     * @param int $limit
     *
     * TODO: filter by story type.
     */
    public static function search($search, $offset = 0, $limit = 1000, $langCode = false)
    {
        abort(501, 'App\Models\Definitions\Story::search not implemented.');
    }

    /**
     * Gets the list of sub types for this definition.
     */
    public function getSubTypes()
    {
        return $this->subTypes[Definition::TYPE_STORY];
    }

    /**
     * Accessor for $this->uri.
     *
     * @return string
     */
    public function getUriAttribute()
    {
        return url($this->mainLanguage->code.'/_story/'.str_replace(' ', '_', $this->title));
    }
}
