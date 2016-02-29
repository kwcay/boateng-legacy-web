<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\SearchableTrait as Searchable;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class Country extends Model
{
    use CamelCaseAttrs, Searchable;

    CONST SEARCH_LIMIT = 40;        // Maximum number of results to return on a search.
    CONST SEARCH_QUERY_LENGTH = 1;  // Minimum length of search query.

    //
}
