<?php

namespace App\Http\Controllers;

class LanguageController extends Controller
{
    protected function boot()
    {
        // Set the "auth" middleware on create/destroy endpoints.
        $this->middleware('auth')
            ->only('create', 'edit', 'store', 'update', 'destroy');
    }

    /**
     * Displays the language page.
     *
     * @param  string $code
     * @return \Illuminate\View\View
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
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->form([
            'id'        => null,
            'code'      => strtoupper(trim($this->request->get('code', ''))),
            'name'      => trim($this->request->get('name', '')),
            'parent'    => strtoupper(trim($this->request->get('parent', ''))),
        ]);
    }

    /**
     * Helper method for the "form" view.
     *
     * @param  array $details
     * @return \Illuminate\View\View
     */
    protected function form(array $details)
    {
        return view('language.form', $details);
    }

    /**
     * Stores a new definition on the API.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        return $this->save();
    }

    /**
     * Updates a language record on the API.
     *
     * @param  string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        return $this->save($id);
    }

    /**
     * @param  string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function save($id = null)
    {
        $this->validate($this->request, [
            'name'      => 'required',
            'code'      => ['required', 'regex:/\s*[A-z]{3}(-[A-z]{3})?\s*/'],
            'parent'    => 'string',
        ]);

        $data = [
            'code'      => strtolower(trim($this->request->get('code'))),
            'name'      => trim($this->request->get('name')),
            'parent'    => strtolower(trim($this->request->get('parent', ''))),
        ];

        $failRoute = $id
            ? route('language.show', $id)
            : route('language.create', $data);

        try {
            if ($id) {
                $saved = $this->api->patch(
                    $this->request->user()->getAccessToken(),
                    'languages/'.$id,
                    $data
                );
            } else {
                $saved = $this->api->post(
                    $this->request->user()->getAccessToken(),
                    'languages',
                    $data
                );
            }
        } catch (\Exception $e) {
            return redirect($failRoute)->withErrors('Could not save Language');
        }

        if (! $saved) {
            return redirect($failRoute)->withErrors('Could not save Language');
        }

        // Clear local cache
        if ($id) {
            $this->cache->forget($this->getCacheKey($id));
        }

        return redirect(route('language.show', ['code' => $saved->code, 'saved' => 1]));
    }
}
