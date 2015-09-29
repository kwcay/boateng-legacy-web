<?php namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Definition;

/**
 *
 */
class PageController extends Controller
{
	/**
	 * Displays main landing page.
	 */
	public function home() {
		return view('pages.home');
	}

	/**
	 * Displays about page.
	 */
	public function about() {
		return view('static.about');
	}

	/**
	 * Displays the page "Di Nkomo: in numbers."
	 */
	public function stats() {
		return view('static.stats', [
            'totalDefs'     => Definition::count(),
            'totalLangs'    => Language::count()
        ]);
	}

	/**
	 * Displays the API description page.
	 */
	public function api() {
		return view('static.api');
	}
}
