<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    /**
     *
     */
    public function index()
    {
        $limit = 30;
        $orderBy = 'id';
        $orderDir = 'desc';

        $countries = Country::take($limit)->orderBy($orderBy, $orderDir)->get();

        return view('admin.countries.index', [
            'countries' => $countries
        ]);
    }
}
