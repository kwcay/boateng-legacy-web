<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved.
 */
namespace App\Http\Controllers;

/**
 * @abstract Main controller for the Alphabet resource.
 */
class ReferenceController extends Controller
{
    protected $defaultQueryLimit = 20;


    protected $supportedOrderColumns = [
        'id' => 'ID',
        'type' => 'Type',
        'string' => 'Summary',
    ];


    protected $defaultOrderColumn = 'id';


    protected $defaultOrderDirection = 'desc';
}
