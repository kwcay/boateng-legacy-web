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

class Country extends Model
{
    use CamelCaseAttrs, Exportable, ObfuscatesID, Searchable;

    CONST SEARCH_LIMIT = 40;        // Maximum number of results to return on a search.
    CONST SEARCH_QUERY_LENGTH = 1;  // Minimum length of search query.

    /**
     * @var int
     */
    public $obfuscatorId = 11;

    /**
     * The attributes that should be hidden from the model's array form when exporting data to file.
     */
    protected $hiddenFromExport = [
        'id',
    ];
}
