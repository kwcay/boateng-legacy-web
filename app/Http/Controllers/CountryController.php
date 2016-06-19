<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends Controller
{
    /**
     *
     */
    protected $name = 'country';

    /**
     *
     */
    protected $defaultQueryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id' => 'ID',
        'name' => 'Name',
        'code' => 'ISO 3166-1 code',
        'createdAt' => 'Created date',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'code';

    /**
     *
     */
    protected $defaultOrderDirection = 'asc';
}
