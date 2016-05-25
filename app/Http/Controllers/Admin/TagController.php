<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Http\Controllers\Admin\BaseController as Controller;

class TagController extends Controller
{
    /**
     *
     */
    protected $name = 'tag';

    /**
     *
     */
    protected $queryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id',
        'title',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'title';

    /**
     *
     */
    protected $defaultOrderDirection = 'asc';
}
