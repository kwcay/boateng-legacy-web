<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     *
     */
    protected $name = 'user';

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
        'email' => 'Email',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'name';

    /**
     *
     */
    protected $defaultOrderDirection = 'asc';
}
