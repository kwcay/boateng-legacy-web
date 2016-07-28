<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved.
 *
 * @brief   Controls the administration section.
 */
namespace App\Http\Controllers;

class AdminController extends Controller
{
    /**
     * Displays main landing page.
     */
    public function index()
    {
        return view('admin.index');
    }

    public function import()
    {
        return view('admin.import');
    }

    public function backup()
    {
        return view('admin.backup');
    }
}
