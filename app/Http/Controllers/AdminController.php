<?php
namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Definition;

/**
 *
 */
class AdminController extends Controller
{
	/**
	 * Displays main landing page.
	 */
    public function index() {
        return view('admin.index');
    }

    public function import() {
        return view('admin.import');
    }

    public function export() {
        return view('admin.export');
    }
}
