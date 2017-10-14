<?php
/**
 * Copyright Dora Boateng(TM) 2015, all rights reserved.
 *
 * @brief   Serves the mostly static app pages.
 */
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
    public function search()
    {
        $search = $this->getSearchResults();

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
     * OpenSearch description.
     *
     * @see    http://www.opensearch.org/Specifications/OpenSearch/1.1#OpenSearch_description_document
     * @param  \Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    public function openSearchDescription(Response $response)
    {
        // Root element.
        $root = new \SimpleXMLElement('<OpenSearchDescription></OpenSearchDescription>');
        $root->addAttribute('xmlns', 'http://a9.com/-/spec/opensearch/1.1/');

        $root->addChild('ShortName', trans('branding.title'));
        $root->addChild('LongName', trans('branding.title').': '.trans('branding.tag_line'));
        $root->addChild('Description', trans('branding.short-pitch'));
        $root->addChild('Tags', 'Dora Boateng cultural culture language reference dictionary encyclopedia');
        $root->addChild('Developer', 'Francis Amankrah (frank@doraboateng.com)');
        $root->addChild('Contact', 'frank@doraboateng.com');
        $root->addChild('Attribution', 'Search data Copyright '.date('Y').', '.trans('branding.title').', All Rights Reserved');
        $root->addChild('SyndicationRight', 'open');
        $root->addChild('AdultContent', 'false');
        $root->addChild('OutputEncoding', 'UTF-8');
        $root->addChild('InputEncoding', 'UTF-8');

        // Sample search
        $sample = $root->addChild('Query');
        $sample->addAttribute('role', 'example');
        $sample->addAttribute('searchTerms', 'hello');

        $searchUri = route('search').'?q={searchTerms}&amp;limit={count}&amp;offset={startIndex}&amp;format=%s';

        // URI for Atom format
        $atom = $root->addChild('Url');
        $atom->addAttribute('type', 'application/atom+xml');
        $atom->addAttribute('rel', 'results');
        $atom->addAttribute('template', sprintf($searchUri, 'atom'));

        // URI for RSS format
        $rss = $root->addChild('Url');
        $rss->addAttribute('type', 'application/rss+xml');
        $rss->addAttribute('rel', 'results');
        $rss->addAttribute('template', sprintf($searchUri, 'rss'));

        // URI for JSON format
        $json = $root->addChild('Url');
        $json->addAttribute('type', 'application/json');
        $json->addAttribute('rel', 'results');
        $json->addAttribute('template', sprintf($searchUri, 'json'));

        // URI for HTML format
        $html = $root->addChild('Url');
        $html->addAttribute('type', 'text/html');
        $html->addAttribute('rel', 'results');
        $html->addAttribute('template', route('search').'?q={searchTerms}');

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'application/opensearchdescription+xml')
            ->setContent($root->asXML());
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
