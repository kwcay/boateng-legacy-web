<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @brief   Serves the mostly static app pages.
 */
namespace App\Http\Controllers;

use DB;
use App\Models\Language;
use App\Models\Definition;

class PageController extends Controller
{
	/**
	 * Main landing page.
	 */
	public function home() {
		return view('pages.reference');
	}

	/**
	 * Contribute page.
	 */
	public function contribute() {
		return view('pages.contribute');
	}

    /**
     * Random definition.
     */
    public function random()
    {
        // Find a random definition.
        $definition = Definition::random();

        return redirect($definition->uri);
    }

	/**
	 * About the app.
     *
     * @param string $topic
     * @return View
	 */
	public function about($topic = '')
    {
        $data = [];

        switch ($topic)
        {
            // About the author
            case 'author':
                $view = 'pages.about.author';
                break;

            // Statistics and other facts.
            case 'stats':
                $view = 'pages.about.stats';

                // Retrieve top languages.
                $topLangs = [];
                $topLangQuery = DB::table('definitions')
                                ->selectRaw('main_language_code AS code')
                                ->selectRaw('COUNT(main_language_code) AS total')
                                ->whereRaw('LENGTH(main_language_code) > 2')
                                ->groupBy('code')
                                ->orderBy('total', 'desc')
                                ->get();

                for ($i = 0; $i < min(3, count($topLangQuery)); $i++)
                {
                    $lang = Language::findByCode($topLangQuery[$i]->code);
                    $topLangs[] = [
                        'name' => $lang->name,
                        'code' => $lang->code,
                        'total' => $topLangQuery[$i]->total,
                    ];
                }

                $data = [
                    'topLanguages' => $topLangs
                ];
                break;

            case 'story':
                $view = 'pages.about.story';
                $data = [
                    'defs' => Definition::count(),
                    'langs' => Language::count()
                ];
                break;

            case 'sponsors':
                $view = 'pages.about.sponsors';
                break;

            case 'team':
                $view = 'pages.about.team.index';
                break;

            default:
                $view = 'pages.about.index';
                $data = [
                    'defs' => Definition::count(),
                    'langs' => Language::count()
                ];
        }

		return view($view, $data);
	}

    public function author() {
        return $this->about('author');
    }
    public function stats() {
        return $this->about('stats');
    }
    public function story() {
        return $this->about('story');
    }
    public function team() {
        return $this->about('team');
    }
    public function sponsors() {
        return $this->about('sponsors');
    }

    /**
     * Sitemap pages.
     *
     * @param string $sub
     * @return View
     */
    public function sitemap($sub = '')
    {
        switch ($sub)
        {
            case 'language':
                $view = 'pages.sitemap.language';
                $data = [];
                break;

            default:
                $view = 'pages.sitemap.main';
                $data = [
                    'languages' => Language::sortedBy('name', 'asc')
                ];
        }

        return view($view, $data);
    }

	/**
	 * Displays the API description page.
	 */
	public function api() {
		return view('pages.api');
	}
}
