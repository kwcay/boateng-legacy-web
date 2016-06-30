<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers;

use App\Models\Tag;

/**
 * @abstract Main controller for the Tag resource.
 */
class TagController extends Controller
{
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
