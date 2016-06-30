<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Lang;
use Session;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
    /**
     * Admin landing page.
     */
    public function index() {
        return view('admin.index');
    }

    /**
     * Import landing page.
     */
    public function import() {
        return view('admin.import');
    }

    /**
     * Backups landing page.
     */
    public function backup() {
        return view('admin.backup');
    }
}
