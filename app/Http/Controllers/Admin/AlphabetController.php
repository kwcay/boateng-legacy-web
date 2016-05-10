<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use App\Models\Alphabet;
use App\Http\Controllers\Controller;

class AlphabetController extends Controller
{
    /**
     *
     */
    public function index()
    {
        $limit = 30;
        $orderBy = 'id';
        $orderDir = 'desc';

        $alphabets = Alphabet::take($limit)->orderBy($orderBy, $orderDir)->get();

        return view('admin.alphabets.index', [
            'alphabets' => $alphabets
        ]);
    }
}
