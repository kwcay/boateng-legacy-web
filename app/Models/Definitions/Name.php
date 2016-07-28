<?php

namespace App\Models\Definitions;

use App\Models\Definition;


class Name extends Definition
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['type'] = static::TYPE_NAME;
    }

    /**
     * Retrieves a random name.
     *
     * @param string $lang
     * @return mixed
     *
     * TODO: filter by name type.
     */
    public static function random($lang = null)
    {
        abort(501, 'App\Models\Definitions\Name::random not implemented.');
    }

    /**
     * Does a fulltext search for a name.
     *
     * @param string $search
     * @param int $offset
     * @param int $limit
     *
     * TODO: filter by name type.
     */
    public static function search($search, $offset = 0, $limit = 1000, $langCode = false)
    {
        abort(501, 'App\Models\Definitions\Name::search not implemented.');
    }

    /**
     * Gets the list of sub types for this definition.
     */
    public function getSubTypes()
    {
        return $this->subTypes[Definition::TYPE_NAME];
    }
}
