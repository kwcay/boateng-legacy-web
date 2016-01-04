<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @brief   Serves the mostly static app pages.
 */
namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Definition;

class PageController extends Controller
{
	/**
	 * Main landing page.
	 */
	public function home() {
		return view('pages.home');
	}

	/**
	 * Contribute page.
	 */
	public function contribute() {
		return view('pages.contribute');
	}

	/**
	 * About the app.
	 */
	public function about() {
		return view('pages.about.index', [
            'defs' => Definition::count(),
            'langs' => Language::count()
        ]);
	}

	/**
	 * Statistics and other facts.
	 */
	public function stats() {
		return view('pages.about.stats', [
            'totalDefs'     => Definition::count(),
            'totalLangs'    => Language::count()
        ]);
	}

	/**
	 * About the author.
	 */
	public function author() {
		return view('pages.about.author');
	}

	/**
	 * Displays the API description page.
	 */
	public function api() {
		return view('pages.api');
	}
}
