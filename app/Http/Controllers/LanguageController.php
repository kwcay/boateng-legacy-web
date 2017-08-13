<?php
/**
 * Copyright Dora Boateng(TM) 2017, all rights reserved.
 */
namespace App\Http\Controllers;

use Cache;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     *
     */
    public function show($code)
    {
        $language = $this->cache->remember('language.'.$code, 60, function() use ($code) {
            return $this->api->getLanguage($code, [
                'definitionCount',
                'parentName',
                'randomDefinition'
            ]);
        });

        if (! $language) {
            // TODO: handle errors
            // ...

            abort(404);
        }

        // Retrieve search results if a query we have a search query.
        $search = $this->getSearchResults($code);

        return view('language.index', [
            'lang'      => $language,
            'query'     => $search['query'],
            'results'   => $search['results'],
        ]);
    }
}
