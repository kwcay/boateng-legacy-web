<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @version 0.1
 */
namespace App\Http\Controllers\API\v01;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Models\Definition;
use App\Models\Definition\Name;
use App\Models\Definition\Phrase;
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

        // Retrieve search options.
        $options = [
            'offset' => $this->request->input('offset', 0),
            'limit' => $this->request->input('limit'),
            'lang' => $this->request->input('lang', ''),
            'method' => $this->request->input('method', 'default')
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
        // Lookup all definitions.
        $options = [];
        $results = Definition::search($query, $options);

        // Add languages.
        $results = $results->merge(Language::search($query));

        // Add countries.
        // ...

        return $results;
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

            foreach ($language->definitions as $definition)
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
