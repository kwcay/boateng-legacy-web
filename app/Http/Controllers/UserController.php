<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved.
 */
namespace App\Http\Controllers;

use App\Models\User;

/**
 * @abstract Main controller for the User resource.
 */
class UserController extends Controller
{
    protected $defaultQueryLimit = 20;


    protected $supportedOrderColumns = [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
    ];


    protected $defaultOrderColumn = 'name';


    protected $defaultOrderDirection = 'asc';
}
