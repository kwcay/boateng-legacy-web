<?php namespace App\Http\Controllers;

use URL;
use Lang;
use Request;
use Session;
use Redirect;
use Validator;

use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;

class DefinitionController extends Controller
{
    /**
     * Displays the word page, with similar definitions.
     *
     * @param string $code  ISO 639-3 language code.
     * @param string $raw   Word to be defined.
     * @return mixed
     */
    public function show($code, $raw = null)
    {
        // Redirect if accessing definition directly.
        if (!$raw) {
            $def = Definition::find($code);
            return $def ? redirect($def->getUri(false)) : redirect(route('home'))->withMessages([Lang::get('errors.resource_not_found')]);
        }

        // Retrieve language object
        if (!$lang = Language::findByCode($code)) {
            abort(404, Lang::get('errors.resource_not_found'));
        }

        // Check user input.
        $data   = trim(preg_replace('/[\s]+/', '_', strip_tags($raw)));
        if (strlen($data) < 2) {
            abort(404, Lang::get('errors.resource_not_found'));
        } elseif ($data != $raw) {
            return redirect($lang->code .'/'. $data);
        }

        // Find definitions matching the query
        $data   = str_replace('_', ' ', $data);
        $wData  = '(data = :a OR data LIKE :b or data LIKE :c or data LIKE :d)';
        $wLang  = '(languages = :w OR languages LIKE :x or languages LIKE :y or languages LIKE :z)';
        $definitions = Definition::whereRaw($wData .' AND '. $wLang, [
            ':a' => $data,
            ':b' => $data .',%',
            ':c' => '%,'. $data .',%',
            ':d' => '%,'. $data,
            ':w' => $lang->code,
            ':x' => $lang->code .',%',
            ':y' => '%,'. $lang->code .',%',
            ':z' => '%,'. $lang->code
        ])->get();

        if (!count($definitions)) {
            abort(404, Lang::get('errors.resource_not_found'));
        }

        return view('pages.def', array(
            'lang'  => $lang,
            'query' => $data,
            'definitions' => $definitions
        ));
    }

	/**
	 * Displays the form to add a new definition.
	 *
	 * @return Response
	 */
	public function create(Definition $def)
	{
        $lso    = [];

        // Set some defaults.
        $def->data = Request::get('data', Request::old('data', ''));
        $def->altData = Request::get('alt_data', Request::old('alt_data', ''));
        $def->type = (int) Request::get('type', Request::old('type', 0));
        $def->translations = (array) Request::get('translations', Request::old('translations', []));
        $def->literalTranslations = (array) Request::get('literal_translations', Request::old('literal_translations', []));
        $def->meanings = (array) Request::get('meanings', Request::old('meanings', []));
        $def->source = Request::get('source', Request::old('source', ''));
        $def->tags = Request::get('tags', Request::old('tags', ''));

        // Retrieve language data.
        if ($lang = Request::get('lang', Request::old('lang')))
        {
            $def->language  = preg_replace('/[^a-z, -]/', '', $lang);

            if ($lang = Language::findByCode($def->language)) {
                $lso[] = [
                    'code' => $lang->code,
                    'name' =>$lang->name
                ];
            }
        }

        return view('forms.definition.default', [
            'def'       => $def,
            'options'   => $lso
        ]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {
        return $this->save(new Definition, Request::all(), route('definition.create', [], false));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
     * @param string $id    Definition ID
	 * @return Response
	 */
	public function edit($id)
	{
        // Retrieve the definition object.
        if (!$def = Definition::find($id)) {
            abort(404, Lang::get('errors.resource_not_found'));
        }

        // Create language options for selectize plugin.
        $lso = [];
        $langs  = Language::whereIn('code', $def->languages)->get();
        foreach ($langs as $lang) {
            $lso[] = [
                'code' => $lang->code,
                'name' => $lang->name
            ];
        }

        return view('forms.definition.default', [
            'def'       => $def,
            'options'   => $lso
        ]);
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @throws \Exception
     * @return Response
     */
	public function update($id)
	{
        // Retrieve the definition object.
        if (!$def = Definition::find($id)) {
            throw new \Exception(Lang::get('errors.resource_not_found'), 404);
        }

        return $this->save($def, Request::all(), $def->getEditUri(false));
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @throws \Exception
     * @return Response
     */
	public function destroy($id)
	{
        // Retrieve the definition object.
        if (!$def = Definition::find($id)) {
            throw new \Exception(Lang::get('errors.resource_not_found'), 404);
        }

        // Delete record
        Session::push('messages', '<em>'. $def->data .'</em> has been succesfully deleted.');
        $def->delete();

        return redirect(route('home'));
	}

    /**
     * Shortcut to create a new definition or save an existing one.
     *
     * @param object $def       Definition object.
     * @param array $data       Definition details to update.
     * @param string $return    Relative URI to redirect to.
     * @return mixed
     */
    public function save($def, $data, $return)
    {
        // Validate input data
        $test = Validator::make($data, $def->validationRules);
        if ($test->fails())
        {
            // Flash input data to session
            Request::flashExcept('_token');

            // Return to form
            return redirect($return)->withErrors($test);
        }

        // Check languages, suggest other languages (esp. parents)
        $codes  = explode(',', $data['language']);
        foreach ($codes as $code)
        {
            if ($lang = Language::findByCode($code))
            {
                // Check if the language has a parent, and
                // whether that parent is already in the list.
                if (strlen($lang->parent) >= 3 && !in_array($lang->parent, $codes))
                {
                    $codes[] = $lang->parent;

                    // Notify the user of the change
                    Session::push('messages',
                        '<em>'. $lang->getParam('parentName') .'</em> is the parent language for <em>'.
                        $lang->name .'</em>, and was added to the list of languages the word <em>'.
                        $data['data'] .'</em> exists in.');
                }
            }
        }

        $def->fill($data);

        $def->state     = 1;

        $def->save();
        $rdir = $data['more'] ?
            route('definition.create', ['lang' => $def->getMainLanguage(true)], false) : $def->getUri(false);

        Session::push('messages', 'The details for <em>'. $def->data .'</em> were successfully saved, thanks :)');
        return Redirect::to($rdir);
    }

    /**
     * @param string $query
     * @return string
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

        // Query the database
        $defs = Definition::where('data', 'LIKE', '%'. $query .'%')
            ->orWhere('alt_data', 'LIKE', '%'. $query .'%')
            ->orWhere('translations', 'LIKE', '%'. $query .'%')
            ->orWhere('literal_translations', 'LIKE', '%'. $query .'%')
            ->orWhere('meanings', 'LIKE', '%'. $query .'%')
            ->skip($offset)->take($limit)->get();

        // Format results
        $results  = [];
        if (count($defs)) {
            foreach ($defs as $def) {
                $results[]  = [
                    'data'          => $def->getAttribute('data'),
                    'type'          => $def->getParam('type'),
                    'alt'           => $def->alt_data,
                    'translations'  => ['en' => $def->getTranslation('en')],
                    'meanings'      => ['en' => $def->getMeaning('en')],
                    'language'      => [
                        'code'  => $def->mainLanguage->getAttribute('code'),
                        'name'  => $def->mainLanguage->getAttribute('name'),
                        'uri'   => $def->mainLanguage->getUri(),
                        'all'   => $def->languages
                    ],
                    'uri'           => $def->getUri()
                ];
            }
        }

        return $this->send(['query' => $query, 'definitions' => $results]);
    }
}

