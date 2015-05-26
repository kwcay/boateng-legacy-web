<?php namespace App\Http\Controllers;

use Input;
use Session;
use Redirect;
use Request;
use Validator;
use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Http\Controllers\Controller;

class LanguageController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		abort(501, 'Not Implemented');
	}

	/**
	 * Displays the form to add a new language.
	 *
	 * @return Response
	 */
	public function create(Language $lang)
	{
        // Set some defaults
        if ($name = Input::get('name', Input::old('name'))) {
            $lang->setName($name);
        }
        if ($alt = Input::old('alt')) {
            $lang->setAltName($alt);
        }
        if ($code = Input::get('code', Input::old('code'))) {
            $lang->code     = preg_replace('/[^a-z\-]/', '', $code);
        }
        if ($parent = Input::get('parent', Input::old('parent'))) {
            if ($parentObj = Language::findByCode($parent)) {
                $lang->parent   = $parentObj->code;
                $lang->setParam('parentName', $parentObj->getName());
            }
        }
        if ($countries = Input::old('countries')) {
            $lang->countries    = preg_replace('/[^a-z,]/', '', $countries);
        }
        if ($desc = Input::old('desc')) {
            $lang->desc     = strip_tags($desc);
        }

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
        $data = Input::all();
        $data['code'] = isset($data['code']) ? $data['code'] : '';

        return $this->save(new Language($data), $data, route('language.create', [], false));
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

        // Redirect if accessing language directly.
        if (Request::path() != $lang->getUri(false)) {
            return Redirect::to($lang->getUri(false));
        }

        return view('pages.lang', [
            'lang' => $lang,
            'random' => Definition::where('language', 'LIKE', '%'. $lang->code .'%')->orderByRaw('RAND()')->first()
        ]);
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
            abort(404, 'Can\'t find that languge :(');
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

        return $this->save($lang, Input::all(), $lang->getEditUri(false));
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
     * @param object $lang      Language object.
     * @param array $data       Language details to update.
     * @param string $return    Relative URI to redirect to.
     * @return Response
     */
    public function save($lang, $data, $return)
    {
        // Validate input data
        $test   = Validator::make($data, $lang->validationRules);
        if ($test->fails())
        {
            // Flash input data to session
            Input::flashExcept('_token');

            // Return to form
            return Redirect::to($return)->withErrors($test);
        }

        // Quick checks.
        $data['countries'] = isset($data['countries']) ? $data['countries'] : [];

        // Main details
        $lang->setName($data['name']);
        $lang->setAltName($data['alt']);
        $lang->countries    = implode(',', $data['countries']);
        $lang->countries    = preg_replace('/[^A-Z,]/', '', $lang->countries);
        $lang->desc         = trim(strip_tags($data['desc']));

        // Parent language details
        if (strlen($data['parent']) >= 3 && $parent = Language::findByCode($data['parent'])) {
            $lang->parent   = $parent->code;
            $lang->setParam('parentName', $parent->getName());
        } else {
            $lang->parent   = '';
            $lang->setParam('parentName', '');
        }

        $lang->save();

        Session::push('messages', 'The details for <em>'. $lang->getName() .'</em> were successfully saved, thanks :)');
        return Redirect::to($lang->getUri(false));
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
            ->orWhere('code', 'LIKE', '%'. $query .'%')
            ->orWhere('parent', 'LIKE', '%'. $query .'%')->get();

        // Format results
        $results    = [];
        if (count($langs)) {
            foreach ($langs as $lang) {
                $results[]  = [
                    'code'          => $lang->code,
                    'name'          => $lang->getName(),
                    'altNames'      => $lang->getAltNames(true),
                    'parentCode'    => $lang->parent,
                    'parentName'    => $lang->getParam('parentName', ''),
                    'uri'           => $lang->getUri()
                ];
            }
        }

        return $this->send(['query' => $query, 'languages' => $results]);
    }

    /**
     * @param $format
     * @return mixed
     */
    public static function export($format = 'yaml')
    {
        $data = Language::all();
        $formatted = [];

        foreach ($data as $item) {
            $formatted[] = $item->toArray();
        }

        return Language::exportToFormat($formatted, $format, false);
    }

    public function import($data)
    {

    }

    /**
     * Shortcut to retrieve a language object.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return mixed        Language object or NULL.
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
