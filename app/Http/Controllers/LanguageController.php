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
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function show($code)
    {
        if (! $language = $this->getLanguage($code)) {
            $code = strtolower(trim($code));

            // Redirect to language form.
            // TODO: localize message.
            // TODO: message gets lost on login redirect.
            if (strlen($code) === 3 || strlen($code) === 7) {
                return redirect(route('language.create', [
                    'code' => $code,
                ]))->withErrors('We could not find that language :( Help us improve Dora Boateng by adding it to our database.');
            }

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
     * Landing page for language learning.
     *
     * @param  string $code
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     * @throws \DoraBoateng\Api\Exceptions\Exception
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function learn($code = null)
    {
        // Language learning landing page.
        if (! $code) {
            return view('language.learn.landing', [
                'languages' => [
                    [
                        'code' => 'hau',
                        'name' => 'Hausa',
                        'regions' => 'Western Africa',
                    ],
                    [
                        'code' => 'swa',
                        'name' => 'Swahili',
                        'regions' => 'Eastern Africa',
                    ],
                    [
                        'code' => 'amh',
                        'name' => 'Amharic',
                        'regions' => 'Ethiopia',
                    ],
                    [
                        'code' => 'twi',
                        'name' => 'Twi',
                        'regions' => 'Ghana &amp; C&ocirc;te d’Ivoire',
                    ],
                    [
                        'code' => 'xho',
                        'name' => 'Xhosa',
                        'regions' => 'South African, Botswana &amp; Zimbabwe',
                    ],
                    [
                        'code' => 'wol',
                        'name' => 'Wolof',
                        'regions' => 'Ethiopia',
                    ],
                ]
            ]);
        }

        // Language-specific page
        if ($lang = $this->getLanguage($code)) {
            return view('language.learn.index', ['lang' => $lang]);
        }

        return redirect(route('language.learn'));
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
     * @throws \DoraBoateng\Api\Exceptions\Exception
     * @throws \GuzzleHttp\Exception\ClientException
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
            return $this->redirectWithErrors(
                $failRoute,
                'Could not save Language',
                $e
            );
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
     * @throws \DoraBoateng\Api\Exceptions\Exception
     * @throws \GuzzleHttp\Exception\ClientException
     */
    protected function getLanguage($code)
    {
        try {
            $language = $this->cache->remember('language.'.$code, 60, function() use ($code) {
                return $this->api->getLanguage($code, [
                    'definitionCount',
                    'parentName',
                    'randomDefinition',
                    'children',
                ]);
            });
        } catch (\DoraBoateng\Api\Exceptions\Exception $apiException) {
            if (app()->environment() === 'local') {
                throw $apiException;
            } else {
                return null;
            }
        } catch (\GuzzleHttp\Exception\ClientException $clientException) {
            if ($clientException->getCode() === 404) {
                return null;
            } elseif (app()->environment() === 'local') {
                throw $clientException;
            } else {
                return null;
            }
        }

        if (! $language) {
            return $language;
        }

        return new \App\Resources\Language($language);
    }
}
