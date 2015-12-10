<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @brief   Handles definition pages.
 */
namespace App\Http\Controllers;

use DB;
use URL;
use Auth;
use Lang;
use Request;
use Session;
use Redirect;
use Validator;

use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;
use Illuminate\Support\Arr;


class DefinitionController extends Controller
{
    public function __construct()
    {
        // Enable the auth middleware.
		// $this->middleware('auth', ['except' => ['show', 'search', 'exists']]);
    }

    /**
     * Displays the word page, with similar definitions.
     *
     * @param string $code  ISO 639-3 language code.
     * @param string $raw   Word to be defined.
     * @return mixed
     *
     * TODO: handle different definition types.
     */
    public function show($code, $raw = null)
    {
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
        $definitions = $lang->definitions()
            ->with('languages', 'translations')
            ->where('title', '=', $data)
            ->get();

        if (!count($definitions))
        {
            // If no definitions were found, check alternate titles...
            $alts = Definition::search($data, 0, 1);
            if (count($alts)) {
                return redirect($alts[0]->relativeUri);
            }

            abort(404, Lang::get('errors.resource_not_found'));
        }

        // TODO: the view will depend on the definition type.
        return view('pages.words', [
            'lang'  => $lang,
            'query' => $data,
            'definitions' => $definitions
        ]);
    }

    /**
     * Performs a fulltext search against a query.
     *
     * @param string $query
     * @return string
     */
    public function search($query = '')
    {
        // This method should really only be called through the API.
        if (Request::method() != 'POST' && env('APP_ENV') != 'local') {
            return $this->abort(405);
        }

        // Let the definition model do all the checks.
        $defs = Definition::fulltextSearch($query, Request::input('offset'), Request::input('limit'), [
            'lang' => Request::input('lang')
        ]);

        // Format results
        $results  = [];
        if (count($defs)) {
            foreach ($defs as $def) {
                $results[] = $def->toArray();
            }
        }

        return $this->send(['query' => $search, 'definitions' => $results]);
    }

    /**
     * Performs an exact match search for a definition.
     *
     * @param string $title
     */
    public function exists($title)
    {
        // Performance check
        $title = trim(preg_replace('/[\s+]/', ' ', strip_tags($title)));
        if (strlen($title) < 2) {
            return $this->abort(400, 'Query too short');
        }

        // Find a specific definition.
        $def = Definition::where('title', '=', $title)->first();

        return $this->send($def);
    }

	/**
	 * Displays the form to add a new definition.
	 *
	 * @return Response
	 */
    private function createType($type, $langCode)
    {
        // Make sure we have a logged in user.
        // if (Auth::guest()) {
        //     return redirect()->guest(route('auth.login'));
        // }

        // Create a specific definition instance.
        if (!$def = Definition::getInstance($type)) {
            abort(500);
        }

        // Retrieve language object.
        if (!$lang = Language::findByCode($langCode)) {
            abort(404, Lang::get('errors.resource_not_found'));
        }

        // Define view.
        $typeName = Definition::getTypeName($type);
        $template = 'forms.definition.'. $typeName .'.walkthrough';

        return view($template, [
            'lang' => $lang,
            'type' => $type,
            $typeName => $def
        ]);
    }

    public function createWord($langCode) {
        return $this->createType(Definition::TYPE_WORD, $langCode);
    }

    public function createName($langCode) {
        return $this->createType(Definition::TYPE_NAME, $langCode);
    }

    public function createPhrase($langCode) {
        return $this->createType(Definition::TYPE_PHRASE, $langCode);
    }

    public function createPoem($langCode) {
        return $this->createType(Definition::TYPE_POEM, $langCode);
    }

    public function createStory($langCode) {
        return $this->createType(Definition::TYPE_STORY, $langCode);
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
        $def->title = Request::get('title', Request::old('title', ''));
        $def->alt_titles = Request::get('alt_titles', Request::old('alt_titles', ''));
        $def->type = (int) Request::get('type', Request::old('type', Definition::TYPE_WORD));
        $def->sub_type = Request::get('sub_type', Request::old('sub_type', 'n'));
        $def->tags = Request::get('tags', Request::old('tags', ''));

        $translations = (array) Request::get('translations', Request::old('translations', []));
        $literalTranslations = (array) Request::get('literal_translations', Request::old('literal_translations', []));
        $meanings = (array) Request::get('meanings', Request::old('meanings', []));

        // Retrieve language data.
        if ($langCode = Request::get('lang', Request::old('lang')))
        {
            $langCode = preg_replace('/[^a-z-]/', '', $langCode);

            if ($lang = Language::findByCode($langCode)) {
                $lso[] = [
                    'code' => $lang->code,
                    'name' =>$lang->name
                ];
            }
        }

        // TODO: update view according to definition type.

        return view('forms.definition.word.default', [
            'def'       => $def,
            'options'   => $lso
        ]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
    {
        $def = $this->getDefinition();

        $data = Request::only([
            'title', 'alt_titles', 'data', 'type', 'sub_type', 'tags', 'state', 'relations'
        ]);

        $data['state'] = Auth::guest() ? Definition::STATE_VISIBLE : $data['state'];

        // Set return route.
        $return = Request::input('next') == 'continue' ? 'edit' : 'index';

        return $this->save($def, $data, $return);
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
        foreach ($def->languages as $lang) {
            $lso[] = [
                'code' => $lang->code,
                'name' => $lang->name
            ];
        }

        // TODO: update view according to definition type.

        return view('forms.definition.word.default', [
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

        $data = Request::only([
            'title', 'alt_titles', 'data', 'type', 'sub_type', 'tags', 'state', 'relations'
        ]);

        $data['type'] = $def->rawType;
        $data['state'] = $def->rawState;

        $return = Request::has('add') ? 'add' : 'index';

        return $this->save($def, $data, $return);
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
        Session::push('messages', '<em>'. $def->title .'</em> has been succesfully deleted.');
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
    private function save(Definition $def, array $data, $return)
    {
        // Validate input data
        $test = Definition::validate($data);
        if ($test->fails())
        {
            // Flash input data to session
            Request::flashExcept('_token');

            // Return to form
            $return = $def->exists ? route('definition.edit', ['id' => $def->getId()]) : route('definition.create');
            return redirect(route('definition.edit'))->withErrors($test);
        }

        // Pull relations.
        $relations = (array) Arr::pull($data, 'relations');

        // Check languages, suggest other languages (esp. parents)
        if (isset($relations['language']))
        {
            // Make sure we have an array.
            $relations['language']  = is_array($relations['language'])
                ? $relations['language'] :
                @explode(',', $relations['language']);

            foreach ($relations['language'] as $code)
            {
                if ($lang = Language::findByCode($code))
                {
                    // Check if the language has a parent, and
                    // whether that parent is already in the list.
                    if (strlen($lang->parent) >= 3 && !in_array($lang->parent, $relations['language'])
                        && $parent = Language::findByCode($lang->parent))
                    {
                        $relations['language'][] = $parent->code;

                        // Notify the user of the change
                        Session::push('messages',
                            '<em>'. $parent->name .'</em> is the parent language for <em>'.
                            $lang->name .'</em>, and was added to the list of languages the word <em>'.
                            $data['title'] .'</em> exists in.');
                    }
                }
            }
        }

        // Update definition details.
        $def->fill($data);
        $def->save();

        // Update translations.
        $def->updateRelations($relations);

        // ...
        switch ($return)
        {
            case 'index':
                $return = $def->getUri(false);
                break;

            case 'edit':
                $return = route('definition.edit', ['id' => $def->getId()]);
                break;

            case 'add':
                $return = route('definition.create', ['lang' => $def->mainLanguage->code]);
                break;
        }

        Session::push('messages', 'The details for <em>'. $def->title .'</em> were successfully saved, thanks :)');
        return redirect($return);
    }

    public function getDefinition()
    {
        $type = Request::input('type');

        switch ($type)
        {
            case Definition::TYPE_WORD:
                $def = new Word;
                break;

            default:
                $def = new Definition;
        }

        return $def;
    }
}
