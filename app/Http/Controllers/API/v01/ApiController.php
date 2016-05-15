<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @version 0.1
 */
namespace App\Http\Controllers\API\v01;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

use App\Http\Requests;
use App\Models\Country;
use App\Models\Alphabet;
use App\Models\Definition;
use App\Models\Definition\Name;
use App\Models\Definition\Expression;
use App\Models\Definition\Poem;
use App\Models\Definition\Story;
use App\Models\Definition\Word;
use App\Models\Language;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Counts the # of resources.
     *
     * @param string $resource
     * @return
     */
    public function count($resource)
    {
        // Retrieve model.
        if (!$model = $this->getResourceModel($resource)) {
            return response('Invalid resource type.', 400);
        }

        return $model->count();
    }

    /**
     * Generates an OpenSearch description document.
     */
    public function openSearchDescription()
    {
        // Root element.
        $root = new \SimpleXMLElement('<OpenSearchDescription ></OpenSearchDescription >');
        $root->addAttribute('xmlns', 'http://a9.com/-/spec/opensearch/1.1/');

        $root->addChild('ShortName', 'Di Nkɔmɔ');
        $root->addChild('LongName', 'Di Nkɔmɔ Cultural Reference');
        $root->addChild('Description', 'Use the Di Nkɔmɔ Cultural Reference to look up words and concepts.');
        $root->addChild('Tags', 'Di Nkɔmɔ cultural culture language reference dictionary encyclopedia');
        $root->addChild('Developer', 'Francis Amankrah (frank@frnk.ca)');
        $root->addChild('Attribution', 'Search data Copyright '. date('Y') .', Di Nkɔmɔ, All Rights Reserved');
        $root->addChild('SyndicationRight', 'open');
        $root->addChild('AdultContent', 'false');
        $root->addChild('OutputEncoding', 'UTF-8');
        $root->addChild('InputEncoding', 'UTF-8');

        // Sample search
        $sample = $root->addChild('Query');
        $sample->addAttribute('role', 'example');
        $sample->addAttribute('searchTerms', 'hello');

        //
        // Search results.
        //

        // URI for Atom format
        $atom = $root->addChild('Url');
        $atom->addAttribute('type', 'application/atom+xml');
        $atom->addAttribute('rel', 'results');
        $atom->addAttribute('template', url('/api/0.1/search/{searchTerms}?limit={count}&amp;offset={startIndex}&amp;format=atom'));

        // URI for RSS format
        $rss = $root->addChild('Url');
        $rss->addAttribute('type', 'application/rss+xml');
        $rss->addAttribute('rel', 'results');
        $rss->addAttribute('template', url('/api/0.1/search/{searchTerms}?limit={count}&amp;offset={startIndex}&amp;format=rss'));

        // URI for JSON format
        $json = $root->addChild('Url');
        $json->addAttribute('type', 'application/json');
        $json->addAttribute('rel', 'results');
        $json->addAttribute('template', url('/api/0.1/search/{searchTerms}?limit={count}&amp;offset={startIndex}&amp;format=json'));

        // URI for HTML format
        $html = $root->addChild('Url');
        $html->addAttribute('type', 'text/html');
        $html->addAttribute('rel', 'results');
        $html->addAttribute('template', url('/?q={searchTerms}'));

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'application/opensearchdescription+xml')
            ->setContent($root->asXML());
    }

    /**
     * Resource search.
     *
     * @param string $resourceName
     * @param string $query
     */
    public function search($resourceName, $query)
    {
        // Retrieve model.
        if (!$model = $this->getResourceModel($resourceName)) {
            return response('Invalid resource type.', 400);
        }

        // Retrieve search parameters.
        $options = [
            'offset' => $this->request->input('offset', 0),
            'limit' => $this->request->input('limit', $model::SEARCH_LIMIT),
            'lang' => $this->request->input('lang', '')
        ];

        // Perform search.
        return $model->search($query, $options);
    }

    /**
     * Queries the database for all resource types.
     *
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    public function searchAllResources($query)
    {
        // Retrieve search parameters (omit "offset" and "limit").
        $options = ['lang' => $this->request->input('lang', '')];

        // Lookup countries.
        $results = new Collection;
        // ...

        // Add cultures.
        // ...

        // Add definitions.
        $results = $results->merge(Definition::search($query, $options));

        // Add languages.
        $results = $results->merge(Language::search($query, $options));

        // Sort results by score.
        $results = $results->sortByDesc(function($result) {
            return $result->score;
        })->values();

        // Apply "offset" and "limit" search parameters.
        $limit = max([
            Country::SEARCH_LIMIT,
            Definition::SEARCH_LIMIT,
            Language::SEARCH_LIMIT
        ]);

        $results = $results->slice(
            $this->request->input('offset', 0),
            $this->request->input('limit', $limit)
        );

        // Format results.
        switch (strtolower($this->request->input('format')))
        {
            case 'opensearch':
                return $this->getOpenSearchResults($results);

            case 'json':
            default:
                return $results;
        }
    }

    /**
     * Converts search results to Open Search format.
     *
     * @param Illuminate\Support\Collection
     * @return array
     */
    private function getOpenSearchResults(Collection $data)
    {
    }

    /**
     * Handles OPTIONS requests.
     */
    public function options()
    {
        return null;
    }

    /**
     * Generates a sitemap.
     */
    public function map()
    {
        // Root element.
        $root = new \SimpleXMLElement('<urlset></urlset>');
        $root->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        // Home page.
        $this->addMapEntry($root, route('home'));

        // Languages & definitions
        foreach (\App\Models\Language::with('definitions')->get() as $language)
        {
            $this->addMapEntry($root, $language->uri, [
                'lastmod' => $language->updatedAt,
                'priority' => 0.8
            ]);

            foreach ($language->definitions()->limit(200)->orderBy('created_at', 'desc')->get() as $definition)
            {
                // Only add definitions where the main language is the current one.
                if ($definition->mainLanguage->code != $language->code) {
                    continue;
                }

                $this->addMapEntry($root, $definition->uri, [
                    'lastmod' => $definition->updatedAt,
                    'priority' => 1
                ]);
            }
        }

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'application/xml')
            ->setContent($root->asXML());
    }

    /**
     *
     *
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

    /**
     * Helper method to determine the requested resource's type.
     *
     * @param string $resourceName
     * @return mixed
     */
    private function getResourceModel($resourceName)
    {
        // Retrieve definition model.
        $definitionTypes = array_flip(Definition::types());
        if (array_key_exists($resourceName, $definitionTypes)) {
            $model = Definition::getInstance($definitionTypes[$resourceName]);
        }

        // Or another model.
        else
        {
            switch (strtolower($resourceName))
            {
                case 'user':
                    $model = null;
                    break;

                case 'alphabet':
                    $model = new Alphabet;
                    break;

                case 'language':
                    $model = new Language;
                    break;

                default:
                    $model = null;
            }
        }

        return $model;
    }
}
