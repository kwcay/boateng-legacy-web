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
    protected $name = 'page';

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
            case 'translation-engine':
                $view = 'pages.about.'. $topic;
                break;

            case 'learning-app':
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

    public function api() {
        return $this->about('api');
    }
    public function author() {
        return $this->about('author');
    }
    public function learningApp() {
        return $this->about('learning-app');
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
    public function translationEngine() {
        return $this->about('translation-engine');
    }
    public function sponsors() {
        return $this->about('sponsors');
    }

    /**
     * humans.txt
     */
    public function humans()
    {
        $txt = '';

        // Team.
        $txt .= "/* TEAM */\n";
        $txt .=
            "\tAuthor: Francis Amankrah\n".
            "\tTwitter: @francisamankrah\n".
            "\tGithub: @frnkly\n".
            "\tFrom: Accra, Ghana & Montreal, Canada\n";

        // Special thanks.
        $txt .= "\n/* THANKS */\n";

        // Site info
        $txt .= "\n/* SITE */\n";
        $txt .=
            "\tLanguage: English\n".
            "\tDoctype: HTML5\n".
            "\tIDE: Atom, cmder, Vagrant, Homestead\n";

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'text/plain')
            ->setContent($txt);
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
     * @return View
     */
    public function sitemap($sub = '')
    {
        return view('pages.sitemap');
    }


    //
    //
    // Redirects
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    public function redirectAdd() {
        return redirect(route('contribute'));
    }

    public function redirectAgoro() {
        return redirect(route('about.agoro'));
    }

    public function redirectContribute() {
        return redirect(route('contribute'));
    }

    public function redirectHome() {
        return redirect(route('home'));
    }

    public function redirectInNumbers() {
        return redirect(route('about.stats'));
    }

    public function redirectStats() {
        return redirect(route('about.stats'));
    }
}
