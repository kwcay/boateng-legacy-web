<?php

namespace App\Http\Controllers;

use App\Resources\Language;
use App\Resources\Resource;
use DoraBoateng\Api\Client;
use Illuminate\Http\Request;
use App\Resources\Definition;
use Illuminate\Http\Response;
use Illuminate\Contracts\Cache\Repository as Cache;

class SearchController extends Controller
{
    /**
     * @const array
     */
    const SUPPORTED_FORMATS = [
        'atom',
        'html',
        'json',
        'rss',
    ];

    /**
     * @var \Illuminate\Http\Response
     */
    private $response;

    /**
     * @var \SimpleXMLElement
     */
    private $rootElement;

    /**
     * @param \Illuminate\Http\Request                  $request
     * @param \Illuminate\Contracts\Cache\Repository    $cache
     * @param \DoraBoateng\Api\Client                   $api
     * @param \Illuminate\Http\Response                 $response
     */
    public function __construct (Request $request, Cache $cache, Client $api, Response $response)
    {
        parent::__construct($request, $cache, $api);

        $this->response = $response;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function suggest()
    {
        if (! in_array($format = strtolower(trim($this->request->get('format', 'json'))), self::SUPPORTED_FORMATS)) {
            return response('Invalid format specified.', 400);
        }

        // Format results
        $search  = $this->getSearchResults();
        $results = $search['results'] ? array_filter(array_map(function ($result) {
            return ($resource = Resource::from($result)) ? [
                'title'       => $resource->getTitle(),
                'description' => $resource->summarize(),
                'link'        => $resource->route(),
            ] : null;
        }, array_slice($search['results'], 0, 10))) : [];

        switch ($format) {
            case 'atom':
                return $this->toAtom($results);

            case 'html':
                return $this->toHtml($results);

            case 'json':
                return $this->toJson($results);

            case 'rss':
                return $this->toRss($results);

            default:
                return response('Internal Error', 501);
        }
    }

    /**
     * @param  array $results
     * @return \Illuminate\Http\Response
     */
    private function toAtom(array $results)
    {
        return response('Not Implemented.', 501);
    }

    /**
     * @param  array $results
     * @return \Illuminate\Http\Response
     */
    private function toHtml(array $results)
    {
        return response('Not Implemented.', 501);
    }

    /**
     * @param  array $results
     * @return \Illuminate\Http\Response
     */
    private function toJson(array $results)
    {
        return response($results, 200);
    }

    /**
     * @param  array $results
     * @return \Illuminate\Http\Response
     */
    private function toRss(array $results)
    {
        return response('Not Implemented.', 501);
    }

    /**
     * @see    http://www.opensearch.org/Specifications/OpenSearch/1.1#OpenSearch_description_document
     * @return \Illuminate\Http\Response
     */
    public function openSearchDescription()
    {
        $attribution = 'Search data Copyright '.date('Y').', '.trans('branding.title').', All Rights Reserved';
        $searchUri = route('search.suggest', ['%s']).'?q={searchTerms}';

        // Root element.
        $this->rootElement = new \SimpleXMLElement('<OpenSearchDescription></OpenSearchDescription>');

        // Child elements.
        $this->attribute('xmlns', 'http://a9.com/-/spec/opensearch/1.1/')
            ->child('ShortName', trans('branding.title'), 16)
            ->child('LongName', trans('branding.title').': '.trans('branding.tag_line'), 48)
            ->child('Description', trans('branding.10_sec_pitch'), 1024)
            ->child('Tags', 'Dora Boateng culture language reference dictionary encyclopedia', 256)
            ->child('Developer', 'Francis Amankrah', 64)
            ->child('Contact', 'frank@doraboateng.com')
            ->child('Attribution', $attribution, 256)
            ->child('SyndicationRight', 'open')
            ->child('AdultContent', 'false')
            ->child('InputEncoding', 'UTF-8')
            ->child('OutputEncoding', 'UTF-8')

            // Sample search
            ->child('Query', null, 0, [
                'role' => 'example',
                'searchTerms' => 'hello',
            ])

            // URI for Atom format
//            ->child('Url', null, 0, [
//                'type' => 'application/atom+xml',
//                'rel' => 'results',
//                'template' => sprintf($searchUri, 'atom'),
//            ])

            // URI for RSS format
//            ->child('Url', null, 0, [
//                'type' => 'application/rss+xml',
//                'rel' => 'results',
//                'template' => sprintf($searchUri, 'rss'),
//            ])

            // URI for JSON format
            ->child('Url', null, 0, [
                'type' => 'application/json',
                'rel' => 'results',
                'template' => sprintf($searchUri, 'json'),
            ])

            // URI for HTML format
//            ->child('Url', null, 0, [
//                'type' => 'text/html',
//                'rel' => 'results',
//                'template' => route('search').'?q={searchTerms}',
//            ])

            // Self-reference
            ->child('Url', null, 0, [
                'type' => 'application/opensearchdescription+xml',
                'rel' => 'self',
                'template' => route('search.osd'),
            ]);

        // Supported langauges
        foreach (['en', 'fr', '*'] as $language) {
            $this->child('Language', $language);
        }

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', 'application/opensearchdescription+xml')
            ->setContent($this->rootElement->asXML());
    }

    /**
     * @param string  $name
     * @param mixed   $value
     * @return static
     */
    protected function attribute(string $name, $value) : self
    {
        $this->rootElement->addAttribute($name, $value);

        return $this;
    }

    /**
     * @param  string $name
     * @param  string $value
     * @param  int    $length
     * @param  array  $params
     * @return static
     */
    protected function child(string $name, string $value = null, int $length = 0, array $params = []) : self
    {
        $child = $this->rootElement->addChild($name, $length ? substr($value, 0, $length) : $value);

        foreach ($params as $k => $v) {
            $child->addAttribute($k, $v);
        }

        return $this;
    }
}
