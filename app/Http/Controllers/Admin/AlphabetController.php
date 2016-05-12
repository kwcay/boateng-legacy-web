<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use App\Models\Alphabet;
use App\Http\Controllers\Admin\BaseController as Controller;

class AlphabetController extends Controller
{
    /**
     *
     */
    protected $name = 'alphabet';

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
        'scriptCode',
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
        return Alphabet::query();
    }
}
