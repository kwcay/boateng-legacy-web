<?php namespace App\Http\Controllers;

use Lang;
use Session;
use Redirect;
use Request;
use Validator;

use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Http\Controllers\Controller;

/**
 *
 */
class LanguageController extends Controller
{
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

        // Redirect if accessing language directly.
        if (Request::path() != $lang->getUri(false)) {
            return redirect($lang->getUri(false));
        }

        return view('pages.language', [
            'lang' => $lang,
            'random' => Definition::random($lang->code),
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
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        // Make sure we have a 'code' index.
        $data = Request::all();
        $data['code'] = isset($data['code']) ? $data['code'] : '';

        return $this->save(new Language($data), $data, route('language.create', [], false));
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

        return $this->save($lang, Request::all(), $lang->getEditUri(false));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        abort(501, 'Not Implemented');
	}

    /**
     * Shortcut to create a new language or save an existing one.
     *
     * @param object $lang      Language object.
     * @param array $data       Language details to update.
     * @param string $return    Relative URI to redirect to.
     * @return Response
     */
    public function save($lang, $data, $return)
    {

        // ...
        if (isset($data['countries']) && is_array($data['countries'])) {
            $data['countries'] = implode(',', $data['countries']);
        }

        // Validate input data
        $test   = Validator::make($data, $lang->validationRules);
        if ($test->fails())
        {
            // Flash input data to session
            Request::flashExcept('_token');

            // Return to form
            return redirect($return)->withErrors($test);
        }

        // Update language object.
        $lang->fill($data);

        // Parent language details
        if (strlen($data['parent']) >= 3 && $parent = Language::findByCode($data['parent'])) {
            $lang->parent   = $parent->code;
            $lang->setParam('parentName', $parent->name);
        } else {
            $lang->parent   = '';
            $lang->setParam('parentName', '');
        }

        $lang->save();

        Session::push('messages', 'The details for <em>'. $lang->name .'</em> were successfully saved, thanks :)');
        return redirect($lang->getUri(false));
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

        // Query the database
        $langs = Language::where('name', 'LIKE', '%'. $query .'%')
            ->orWhere('alt_name', 'LIKE', '%'. $query .'%')
            ->orWhere('desc', 'LIKE', '%'. $query .'%')
            ->orWhere('code', 'LIKE', '%'. $query .'%')
            ->orWhere('parent', 'LIKE', '%'. $query .'%')->get();

        // Format results
        $results    = [];
        if (count($langs)) {
            foreach ($langs as $lang) {
                $results[]  = [
                    'code'          => $lang->code,
                    'name'          => $lang->name,
                    'altNames'      => $lang->alt_name,
                    'parentCode'    => $lang->parent,
                    'parentName'    => $lang->getParam('parentName', ''),
                    'uri'           => $lang->getUri()
                ];
            }
        }

        return $this->send(['query' => $query, 'languages' => $results]);
    }
    /**
     * Shortcut to retrieve a language object.
     *
     * @param string $id        Either the ISO 639-3 language code or language ID.
     * @return object|null
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
