<?php
/**
 * Copyright Dora Boateng(TM) 2017, all rights reserved.
 *
 * @brief   This controller serves as an abstract for all other controllers.
 */
namespace App\Http\Controllers;

use DoraBoateng\Api\Client;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * @var DoraBoateng\Api\Client
     */
    protected $api;

    /**
     * Internal name used to map controller to its model, views, etc.
     *
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $defaultQueryLimit = 20;

    /**
     * @var array
     */
    protected $supportedOrderColumns = [];

    /**
     * @var string
     */
    protected $defaultOrderColumn = 'id';

    /**
     * @var string
     */
    protected $defaultOrderDirection = 'desc';

    /**
     * @param  Illuminate\Http\Request                  $request
     * @param  Illuminate\Contracts\Cache\Repository    $cache
     * @param  DoraBoateng\Api\Client                   $api
     * @return void
     */
    public function __construct(Request $request, Cache $cache, Client $api)
    {
        // Determine internal name from class name.
        if (! $this->name) {
            $namespace  = explode('\\', get_class($this));
            $this->name = strtolower(substr(array_pop($namespace), 0, -10));
        }

        $this->api      = $api;
        $this->cache    = $cache;
        $this->request  = $request;
    }

    /**
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        abort(501);
    }

    /**
     * @return Illuminate\Http\Response
     */
    public function create()
    {
        abort(501);
    }

    /**
     * @return Illuminate\Http\Response
     */
    public function store()
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return Illuminate\Http\Response
     */
    public function update($id)
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(501);
    }

    /**
     * Retrieves search results.
     *
     * @param  string $langCode
     * @return array
     */
    protected function getSearchResults($langCode = null)
    {
        $search = [
            'query'     => trim($this->request->get('q')),
            'results'   => null,
        ];

        if (! $search['query']) {
            return $search;
        }

        $cacheKey = ($langCode ? 'search.'.$langCode : 'search.all').'-'.base64_encode($search['query']);

        $response = $this->cache->remember($cacheKey, 5, function() use ($search, $langCode) {
            return $langCode
                ? $this->api->searchDefinitions($search['query'], $langCode)
                : $this->api->search($search['query']);
        });

        if (is_object($response)) {
            $search['results'] = $response->results;
        }

        return $search;
    }

    protected function getWeeklyLanguage()
    {
        // Cache language of the week for 3 hours
        return $this->cache->remember('language.weekly', 180, function() {
            return $this->api->getLanguageOfTheWeek();
        });
    }
}
