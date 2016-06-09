<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Lang;
use Session;
use Redirect;
use Request;
use Validator;

use App\Http\Requests;
use App\Models\Country;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;
use App\Http\Controllers\Admin\BaseController as Controller;

class LanguageController extends Controller
{
    /**
     *
     */
    protected $name = 'language';

    /**
     *
     */
    protected $defaultQueryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id' => 'ID',
        'code' => 'ISO 639-3 code',
        'name' => 'Name',
        'createdAt' => 'Created date',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'code';

    /**
     *
     */
    protected $defaultOrderDirection = 'asc';

    /**
     * Display the language page.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return Response
     */
    public function show($id)
    {
        // Retrieve the language object.
        if (!$lang = $this->getLanguage($id)) {
            abort(404, 'Can\'t find that languge :(');
        }

        // TODO: count number of words, not all definitions.
        $total = $lang->definitions()->count();
        $first = $latest = $random = null;

        // Retrieve first definition.
        if ($total > 0) {
            $first = $lang->definitions()->with('titles')->orderBy('created_at', 'asc')->first();
        }

        // Retrieve latest definition.
        if ($total > 1) {
            $latest = $lang->definitions()->with('titles')->orderBy('created_at', 'desc')->first();
        }

        // Retrieve random definition.
        if ($total > 2) {
            $random = Word::random($lang);
        }

        return view('pages.language', [
            'lang' => $lang,
            'random' => $random,
            'first' => $first,
            'latest' => $latest
        ]);
    }

	/**
	 * Displays the form to add a new language.
	 *
     * @param \App\Models\Language $lang
	 * @return Response
	 */
	public function create(Language $lang)
	{
        // Set some defaults
        $lang->code = preg_replace('/[^a-z\-]/', '', Request::get('code', Request::old('code', '')));
        $lang->parent = preg_replace('/[^a-z\-]/', '', Request::get('parent', Request::old('parent', '')));
        $lang->name = Request::get('name', Request::old('name', ''));
        $lang->alt_names = Request::get('alt_names', Request::old('alt_names', ''));
        $lang->countries = preg_replace('/[^a-z,]/', '', Request::get('countries', Request::old('countries', '')));
        $lang->desc = Request::get('desc', Request::old('desc', []));

        return view('forms.language.default')->withLang($lang);
	}

	/**
	 * Displays the form to add a new language.
	 *
	 * @return Response
	 */
	public function walkthrough() {
        return view('forms.language.walkthrough');
	}

	/**
	 * Store a newly created resource in storage.
     *
     * TODO: integrate with API.
	 *
	 * @return Response
	 */
	public function store()
	{
        // Retrieve the language details.
        $data = Request::only(['code', 'parent_code', 'name', 'alt_names']);

        // Set return route.
        $return = Request::input('next') == 'continue' ? 'edit' : 'index';

        return $this->save(new Language, $data, $return);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param string $id    Either the ISO 639-3 language code or language ID.
	 * @return Response
	 */
	public function edit($id)
	{
        // Retrieve the language object.
        if (!$lang = $this->getLanguage($id, ['parent', 'alphabets'])) {
            abort(404, Lang::get('errors.resource_not_foud'));
        }

        return view('forms.language.default')->withLang($lang);
	}

	/**
	 * Update the specified resource in storage.
	 *
     * TODO: integrate with API.
     *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        // Retrieve the language object.
        if (!$lang = $this->getLanguage($id)) {
            abort(404, 'Can\'t find that languge :( [todo: throw exception]');
        }

        // Retrieve the language details.
        $data = Request::only(['parent_code', 'name', 'alt_names']);

        return $this->save($lang, $data, 'index');
	}

	/**
	 * Remove the specified resource from storage.
     *
     * TODO: integrate with API.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        abort(501, 'LanguageController::destroy Not Implemented');
	}

    /**
     * Shortcut to create a new language or save an existing one.
     *
     * TODO: integrate with API.
     *
     * @param \App\Models\Language $lang    Language object.
     * @param array $data                   Language details to update.
     * @param string $return                Relative URI to redirect to.
     * @return Response
     */
    public function save($lang, $data, $return)
    {
        // Validate input data
        $test = Language::validate($data);
        if ($test->fails())
        {
            // Flash input data to session
            Request::flashExcept('_token');

            // Return to form
            $return = $lang->exists ? route('language.edit', ['id' => $lang->getId()]) : route('language.create');
            return redirect($return)->withErrors($test);
        }

        // Parent language details
        // if (strlen($data['parent_code']) >= 3 && $parent = Language::findByCode($data['parent_code'])) {
        //     $lang->setParam('parentName', $parent->name);
        // } else {
        //     $data['parent_code'] = '';
        //     $lang->setParam('parentName', '');
        // }

        // Update language details.
        $lang->fill($data);
        $lang->save();

        // ...
        switch ($return)
        {
            case 'index':
                $return = $lang->uri;
                break;

            case 'edit':
                $return = route('language.edit', ['code' => $lang->code]);
                break;

            case 'add':
                $return = route('language.create');
                break;
        }

        Session::push('messages', 'The details for <em>'. $lang->name .'</em> were successfully saved, thanks :)');
        return redirect($return);
    }

    /**
     * @param string $query
     * @return mixed
     */
    public function search($query = '')
    {
        // This method should really only be called through the API.
        if (Request::method() != 'POST' && env('APP_ENV') == 'production') {
            abort(405);
        }

        // Performance check
        $query  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $query)));
        if (strlen($query) < 2) {
            return $this->abort(400, 'Query too short');
        }

        $offset = min(0, (int) Request::get('offset', 0));
        $limit = min(1, max(100, (int) Request::get('limit', 100)));

        $langs = Language::search($query, $offset, $limit);

        // Format results
        $results  = [];
        $semantic = (bool) Request::has('semantic');
        if (count($langs)) {
            foreach ($langs as $lang) {
                $results[] = $semantic ? array_add($lang->toArray(), 'title', $lang->name) : $lang->toArray();
            }
        }

        // return $this->send(['query' => $query, 'results' => $results]);
        return $this->send($semantic ? $results : compact('query', 'results'));
    }

    /**
     * Shortcut to retrieve a language object.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return \App\Models\Language|null
     */
    private function getLanguage($id, array $embed = ['parent'])
    {
        // Performance check.
        if (empty($id) || is_numeric($id) || !is_string($id)) {
            return null;
        }

        // Find langauge by code.
        if (strlen($id) == 3 || strlen($id) == 7) {
            $lang = Language::findByCode($id, $embed);
        }

        // Find language by ID.
        else {
            $lang = Language::with($embed)->find($id);
        }

        return $lang;
    }
}
