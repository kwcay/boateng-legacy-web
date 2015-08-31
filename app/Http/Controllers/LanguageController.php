<?php namespace App\Http\Controllers;

use Lang;
use Session;
use Redirect;
use Request;
use Validator;

use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;
use App\Http\Controllers\Controller;


/**
 *
 */
class LanguageController extends Controller
{
    public function __construct()
    {
        // Enable the auth middleware.
		$this->middleware('auth', ['except' => 'show', 'search']);
    }
    
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

        return view('pages.language', [
            'lang' => $lang,
            'random' => Word::random($lang->code),
            'first' => $lang->definitions()->orderBy('created_at', 'asc')->first(),
            'latest' => $lang->definitions()->orderBy('created_at', 'desc')->first()
        ]);
    }

	/**
	 * Displays the form to add a new language.
	 *
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
	 * @return Response
	 */
	public function store()
	{
        // Retrieve the language details.
        $data = Request::only(['code', 'parent_code', 'name', 'alt_names', 'countries']);

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
        if (!$lang = $this->getLanguage($id)) {
            abort(404, Lang::get('errors.resource_not_foud'));
        }

        return view('forms.language.default')->withLang($lang);
	}

	/**
	 * Update the specified resource in storage.
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
        $data = Request::only(['parent_code', 'name', 'alt_names', 'countries']);

        return $this->save($lang, $data, 'index');
	}

	/**
	 * Remove the specified resource from storage.
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
     * @param \App\Models\Language $lang    Language object.
     * @param array $data                   Language details to update.
     * @param string $return                Relative URI to redirect to.
     * @return Response
     */
    public function save($lang, $data, $return)
    {
        // ...
        if (isset($data['countries']) && is_array($data['countries'])) {
            $data['countries'] = implode(',', $data['countries']);
        }

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
        if (strlen($data['parent_code']) >= 3 && $parent = Language::findByCode($data['parent_code'])) {
            $lang->setParam('parentName', $parent->name);
        } else {
            $data['parent_code'] = '';
            $lang->setParam('parentName', '');
        }

        // Update language details.
        $lang->fill($data);
        $lang->save();

        // ...
        switch ($return)
        {
            case 'index':
                $return = $lang->getUri(false);
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
     * @param string $id                    Either the ISO 639-3 language code or language ID.
     * @return \App\Models\Language|null
     */
    private function getLanguage($id)
    {
        // Performance check.
        if (empty($id) || is_numeric($id) || !is_string($id)) {
            return null;
        }

        $lang = (strlen($id) == 3 || strlen($id) == 7) ? Language::findByCode($id) : Language::find($id);

        return $lang;
    }
}
