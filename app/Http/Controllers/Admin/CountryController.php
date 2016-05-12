<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Http\Controllers\Admin\BaseController as Controller;

class CountryController extends Controller
{
    /**
     *
     */
    protected $name = 'country';

    /**
     *
     */
    protected $queryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id',
        'name',
        'code',
        'createdAt',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'name';

    /**
     *
     */
    protected $defaultOrderDirection = 'asc';

    /**
     *
     */
    protected function getModelQueryBuilder()
    {
        return Country::query();
    }
}
