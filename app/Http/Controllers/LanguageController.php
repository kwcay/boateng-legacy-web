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
        $language = Cache::remember('language.'.$code, 60, function() use ($code) {
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

        // Todo: move to parent class
        $results    = null;
        $query      = trim($this->request->get('q'));
        $query      = strlen($query) ? $query : null;

        // Do general search
        if ($query) {
            $response = Cache::remember('language.search.'.$code.'-'.$query, 5, function() use ($query, $language) {
                return $this->api->searchDefinitions($query, $language->code);
            });

            if (is_int($response)) {
                // TODO: handle errors
                // ...
            } else {
                $results = $response->results;
            }
        }

        return view('pages.language', [
            'lang'      => $language,
            'query'     => $query,
            'results'   => $results,
        ]);
    }
}
