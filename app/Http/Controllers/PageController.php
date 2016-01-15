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
                $data = [
                    'totalDefs'     => Definition::count(),
                    'totalLangs'    => Language::count()
                ];
                break;

            case 'story':
                $view = 'pages.about.story';
                $data = [
                    'defs' => Definition::count(),
                    'langs' => Language::count()
                ];
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
