<?php

namespace App\Http\Controllers;

use DB;
use Cache;
use Illuminate\Http\Response;

class PageController extends Controller
{
    protected $name = 'page';

    /**
     * Main landing page.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        // Redirect searches to search page.
        if ($this->request->filled('q')) {
            return redirect(route('search', ['q' => $this->request->get('q')]));
        }

        return view('pages.home')
            ->withLanguage($this->getWeeklyLanguage());
    }

    /**
     * Search page.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSearchPage()
    {
        $search = $this->search($this->request->get('q'));

        return view('pages.search', [
            'query'     => $search['query'],
            'results'   => $search['results'],
            'language'  => $this->getWeeklyLanguage(),
        ]);
    }

    /**
     * About the app.
     *
     * @param string $topic
     * @return \Illuminate\Http\Response
     */
    public function about($topic = '')
    {
        return $this->notImplemented();

        $data = [];

        switch ($topic) {
            case 'api':
            case 'author':
            case 'sponsors':
            case 'translation-engine':
                $view = 'pages.about.'.$topic;
                break;

            case 'learning-app':
            case 'stats':
                $view = 'pages.about.'.$topic;

                // Retrieve top languages.
                $topLangs = [];
                $topLangQuery = DB::table('definitions')
                                ->selectRaw('main_language_code AS code')
                                ->selectRaw('COUNT(main_language_code) AS total')
                                ->whereRaw('LENGTH(main_language_code) > 2')
                                ->groupBy('code')
                                ->orderBy('total', 'desc')
                                ->get();

                for ($i = 0; $i < min(5, count($topLangQuery)); $i++) {
                    $lang = Language::findByCode($topLangQuery[$i]->code);
                    $topLangs[] = [
                        'name' => $lang->name,
                        'code' => $lang->code,
                        'total' => $topLangQuery[$i]->total,
                    ];
                }

                $data = [
                    'topLanguages' => $topLangs,
                ];
                break;

            case 'story':
                $view = 'pages.about.story';
                $data = [
                    'defs' => Definition::count(),
                    'langs' => Language::count(),
                ];
                break;

            case 'team':
                $view = 'pages.about.team.index';
                break;

            default:
                $view = 'pages.about.index';
                // $data = [
                //     'defs' => Definition::count(),
                //     'langs' => Language::count(),
                // ];
        }

        return view($view, $data);
    }

    public function api()
    {
        return $this->about('api');
    }

    public function author()
    {
        return $this->about('author');
    }

    public function learningApp()
    {
        return $this->about('learning-app');
    }

    public function stats()
    {
        return $this->about('stats');
    }

    public function story()
    {
        return $this->about('story');
    }

    public function team()
    {
        return $this->about('team');
    }

    public function translationEngine()
    {
        return $this->about('translation-engine');
    }

    public function sponsors()
    {
        return $this->about('sponsors');
    }

    /**
     * humans.txt.
     *
     * @param  \Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    public function humans(Response $response)
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
            "\tIDE: Atom, Sublime, cmder, Vagrant, Homestead\n";

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'text/plain')
            ->setContent($txt);
    }

    /**
     * Contribute page.
     */
    public function contribute()
    {
        return view('pages.contribute');
    }

    /**
     * Sitemap pages.
     *
     * @param  \Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    public function sitemap(Response $response)
    {
        // Root element.
        $root = new \SimpleXMLElement('<urlset></urlset>');
        $root->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        // Home page.
        $this->addMapEntry($root, route('home'), [
            'changefreq'    => 'daily',
            'priority'      => '0.8',
        ]);

        // About page.
        $this->addMapEntry($root, route('about'), [
            'changefreq'    => 'weekly',
            'priority'      => '0.5',
        ]);

        try {
            $resources = $this->api->get('latest');

            foreach ($resources as $r) {
                switch ($r->resourceType) {
                    case 'definition':
                        $this->addMapEntry($root, route('definition.show', $r->uniqueId), [
                            'changefreq'    => 'weekly',
                            'priority'      => '1.0',
                        ]);
                        break;

                    case 'language':
                        $this->addMapEntry($root, route('language.learn', $r->code), [
                            'changefreq'    => 'weekly',
                            'priority'      => '0.9',
                        ]);
                        $this->addMapEntry($root, route('language', $r->code), [
                            'changefreq'    => 'daily',
                            'priority'      => '0.9',
                        ]);
                        break;
                }
            }
        } catch (\Exception $e) {
            if (app()->environment() != 'production') {
                return response($e->getMessage(), 500);
            }
        }

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'application/xml')
            ->setContent($root->asXML());
    }

    /**
     * @param \SimpleXMLElement $root
     */
    private function addMapEntry(\SimpleXMLElement $root, $location, array $optional = [])
    {
        // Required nodes.
        $url = $root->addChild('url');
        $url->addChild('loc', $location);

        // Optional nodes.
        if (isset($optional['lastmod'])) {
            $url->addChild('lastmod', gmdate('Y-m-d', strtotime($optional['lastmod'])));
        }
        if (isset($optional['changefreq'])) {
            $url->addChild('changefreq', $optional['changefreq']);
        }
        if (isset($optional['priority'])) {
            $url->addChild('priority', $optional['priority']);
        }
    }

    public function setNoTrackingCookie()
    {
        Cookie::make('noga', 1);

        return redirect()->route('home');
    }

    public function notImplemented()
    {
        return redirect()->route('home');
    }

    //
    //
    // Redirects
    //
    ////////////////////////////////////////////////////////////////////////////////////////////

    public function redirectAdd()
    {
        return redirect(route('contribute'));
    }

    public function redirectAgoro()
    {
        return redirect(route('about.agoro'));
    }

    public function redirectContribute()
    {
        return redirect(route('contribute'));
    }

    public function redirectHome()
    {
        return redirect(route('home'));
    }

    public function redirectInNumbers()
    {
        return redirect(route('about.stats'));
    }

    public function redirectStats()
    {
        return redirect(route('about.stats'));
    }
}
