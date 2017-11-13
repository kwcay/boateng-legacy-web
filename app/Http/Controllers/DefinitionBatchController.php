<?php

namespace App\Http\Controllers;

class DefinitionBatchController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('definition.batch.create');
    }
}
