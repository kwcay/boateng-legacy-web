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
     * @throws \DoraBoateng\Api\Exceptions\Exception
     */
    public function show($code)
    {
        try {
            if (! $language = $this->getLanguage($code)) {
                abort(404);
            }
        } catch (\DoraBoateng\Api\Exceptions\Exception $e) {
            if (app()->environment() == 'local') {
                throw $e;
            } else {
                abort(404);
            }
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
            'isNew'         => true,
            'code'          => strtolower(trim($this->request->get('code', ''))),
            'name'          => trim($this->request->get('name', '')),
            'parentCode'    => strtolower(trim($this->request->get('parent_code', ''))),
        ]);
    }

    /**
     * Displays the form to edit an existing language.
     *
     * @param  string $code
     * @return \Illuminate\View\View
     */
    public function edit($code)
    {
        if (! $language = $this->getLanguage($code)) {
            abort(404);
        }

        return $this->form([
            'isNew'         => false,
            'code'          => $language->code,
            'name'          => $language->name,
            'parentCode'    => $language->parentCode,
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
     * @param  string $code
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function save($code = null)
    {
        $this->validate($this->request, [
            'name'          => 'required',
            'code'          => ['required', 'regex:/\s*[A-z]{3}(-[A-z]{3})?\s*/'],
            'parent_code'   => 'string|nullable',
        ]);

        $data = [
            'code'          => strtolower(trim($this->request->get('code'))),
            'name'          => trim($this->request->get('name')),
            'parent_code'   => strtolower(trim($this->request->get('parent_code', ''))),
        ];

        $failRoute = $code
            ? route('language.show', $code)
            : route('language.create', $data);

        try {
            if ($code) {
                $saved = $this->api->patch(
                    $this->request->user()->getAccessToken(),
                    'languages/'.$code,
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
            $errors = ['Could not save Language'];

            if (app()->environment() == 'local') {
                $errors[] = $e->getMessage();
            }

            return redirect($failRoute)->withErrors($errors);
        }

        if (! $saved) {
            return redirect($failRoute)->withErrors('Could not save Language');
        }

        // Clear local cache
        if ($code) {
            $this->cache->forget($this->getCacheKey($code));
        }

        return redirect(route('language.show', ['code' => $saved->code, 'saved' => 1]));
    }

    /**
     * Retrieves a language by code.
     *
     * @param  string $code
     * @return \App\Resources\Language|null
     */
    protected function getLanguage($code)
    {
        try {
            $language = $this->cache->remember('language.'.$code, 60, function() use ($code) {
                return $this->api->getLanguage($code, [
                    'definitionCount',
                    'parentName',
                    'randomDefinition'
                ]);
            });
        } catch (\DoraBoateng\Api\Exceptions\Exception $exception) {
            return null;
        }

        if (! $language) {
            return $language;
        }

        return new \App\Resources\Language($language);
    }
}
