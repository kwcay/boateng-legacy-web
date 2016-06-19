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
     *
     */
    protected $name = 'admin';

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
            case 'api':
            case 'author':
            case 'sponsors':
                $view = 'pages.about.'. $topic;
                break;

            case 'agoro':
            case 'stats':
                $view = 'pages.about.'. $topic;

                // Retrieve top languages.
                $topLangs = [];
                $topLangQuery = DB::table('definitions')
                                ->selectRaw('main_language_code AS code')
                                ->selectRaw('COUNT(main_language_code) AS total')
                                ->whereRaw('LENGTH(main_language_code) > 2')
                                ->groupBy('code')
                                ->orderBy('total', 'desc')
                                ->get();

                for ($i = 0; $i < min(5, count($topLangQuery)); $i++)
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

    public function agoro() {
        return $this->about('agoro');
    }
    public function api() {
        return $this->about('api');
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
     * Admin landing page.
     */
    public function admin() {
        return view('admin.index');
    }

    public function import() {
        return view('admin.import');
    }

    public function backup() {
        return view('admin.backup');
    }

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
                $view = 'pages.sitemap.index';
                $data = [
                    'languages' => Language::sortedBy('name', 'asc')
                ];
        }

        return view($view, $data);
    }
}
