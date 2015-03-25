<?php
namespace App\Http\Controllers;
use \App\Models\Language;
use \App\Models\Definition;

/**
 *
 */
class SimplePage extends Controller
{
	/**
	 * Displays main landing page.
	 */
	public function home() {
        
        // Word of the day
        $word   = Definition::where('state', '>', '0')->orderByRaw('RAND()')->first();
        
		return view('pages.home', ['wordOfTheDay' => $word]);
	}

	/**
	 * Displays about page.
	 */
	public function about() {
		return view('pages.about');
	}

	/**
	 * Displays the page "Di Nkomo: in numbers."
	 */
	public function stats() {
		return view('pages.stats', [
            'totalDefs'     => Definition::count(),
            'totalLangs'    => Language::count()
        ]);
	}

	/**
	 * Displays the API description page.
	 */
	public function api() {
		return view('pages.api');
	}
    
    /**
     * Displays Laravel's welcome page.
     */
    public function welcome() {
        return view('welcome');
    }
}
