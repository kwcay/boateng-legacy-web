<?php

namespace App\Http\Controllers;

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

    /**
     * Displays the form to add a new language.
     */
    public function create()
    {
        return $this->form([
            'id'    => null,
            'name'  => '',
        ]);
    }

    /**
     * Helper method for the "form" view.
     *
     * @param  array $details
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function form(array $details)
    {
        return view('language.form', $details);
    }
}
