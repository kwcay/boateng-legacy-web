<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    /**
     *
     */
    protected $name = 'tag';

    /**
     *
     */
    protected $defaultQueryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id' => 'ID',
        'title' => 'Title',
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
