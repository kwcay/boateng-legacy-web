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

        // Set the definiiton type.
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
     * Searches the database for words.
     *
     * @param string $query     Search query.
     * @param array $options    Search options.
     * @return array
     */
    public static function search($query, array $options = [])
    {
        return parent::search($query, array_merge($options, ['type' => static::types()[static::TYPE_WORD]]));
    }

    /**
     * Gets the list of sub types for this definition.
     */
    public function getSubTypes() {
        return $this->subTypes[Definition::TYPE_WORD];
    }
}
