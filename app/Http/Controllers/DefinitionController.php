<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
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

use Illuminate\Support\Arr;

use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;

class DefinitionController extends Controller
{
    public function __construct()
    {
        // Enable the auth middleware.
		// $this->middleware('auth', ['except' => ['show', 'search', 'exists']]);
    }

    /**
     * Displays the word or phrase page, with similar definitions.
     *
     * @param string $code  ISO 639-3 language code.
     * @param string $raw   Word or phrase to be defined.
     * @return mixed
     */
    public function show($code, $raw = null)
    {
        // Retrieve language object
        if (!$lang = Language::findByCode($code)) {
            abort(404, Lang::get('errors.resource_not_found'));
        }

        // Check user input.
        $str = trim(preg_replace('/[\s]+/', '_', strip_tags($raw)));
        if (strlen($str) < 2) {
            abort(404, Lang::get('errors.resource_not_found'));
        } elseif ($str != $raw) {
            return redirect($lang->code .'/'. $str);
        }

        // Find definitions matching the query
        $str = str_replace('_', ' ', $str);
        $definitions = $lang->definitions()
                        ->with('languages', 'translations', 'titles')
                        ->whereIn('type', [Definition::TYPE_WORD, Definition::TYPE_PHRASE])
                        ->whereHas('titles', function($query) use($str) {
                            $query->where('title', $str);
                        })->get();
        // $definitions = $lang->definitions()
        //     ->with('languages', 'translations')
        //     ->where('title', '=', $data)
        //     ->whereIn('type', [Definition::TYPE_WORD, Definition::TYPE_PHRASE])
        //     ->get();

        if (!count($definitions))
        {
            // If no definitions were found, check alternate titles...
            $alts = Definition::search($str, ['offset' => 0, 'limit' => 1]);
            if (count($alts)) {
                return redirect($alts[0]->uri);
            }

            abort(404, Lang::get('errors.resource_not_found'));
        }

        return view('pages.definitions', [
            'lang'  => $lang,
            'query' => $str,
            'definitions' => $definitions
        ]);
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
        if (!$definition = Definition::getInstance($type)) {
            abort(500);
        }

        // Retrieve language object.
        if (!$lang = Language::findByCode($langCode)) {
            abort(404, Lang::get('errors.resource_not_found'));
        }

        // Define view.
        $template = 'forms.definition.'. Definition::getTypeName($type) .'.walkthrough';

        return view($template, [
            'lang' => $lang,
            'type' => $type,
            'definition' => $definition
        ]);
    }

    public function createWord($langCode) {
        return $this->createType(Definition::TYPE_WORD, $langCode);
    }

    public function createPhrase($langCode) {
        return $this->createType(Definition::TYPE_PHRASE, $langCode);
    }

    public function createStory($langCode) {
        return $this->createType(Definition::TYPE_STORY, $langCode);
    }

	/**
	 * Stores a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
    {
        // Performance check.
        if (!$definition = Definition::getInstance(Request::input('type'))) {
            Session::push('messages', 'Internal Error.');
            return redirect(route('home'));
        }

        // Retrieve new definition data.
        $data = Request::only(['type', 'subType', 'relations']);

        $return = Request::input('next') == 'continue' ? 'edit' : 'index';

        return $this->save($definition, $data, $return);
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
        if (!$definition = Definition::find($id)) {
            abort(404, Lang::get('errors.resource_not_found'));
        }

        // Language arrays for selectize plugin.
        $options = $items = [];
        foreach ($definition->languages as $lang)
        {
            // Only the value is used for the selected items.
            $items[] = $lang->code;

            // The value and name will be json-encoded for the selectize options.
            $options[] = [
                'code' => $lang->code,
                'name' => $lang->name
            ];
        }

        return view('forms.definition.default', [
            'definition' => $definition,
            'languageOptions' => $options
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
        if (!$definition = Definition::find($id)) {
            throw new \Exception(Lang::get('errors.resource_not_found'), 404);
        }

        $data = Request::only([
            'subType', 'relations'
        ]);

        $return = Request::has('add') ? 'add' : 'index';

        return $this->save($definition, $data, $return);
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
        // Make sure we have valid titles.
        $titles = [];
        $titleStr = trim(@$data['relations']['titleStr']);
        foreach (@explode(',', $titleStr) as $title)
        {
            $title = trim($title);

            if (strlen($title)) {
                $titles[] = $title;
            }
        }

        if (!count($titles))
        {
            // Flash input data to session.
            Request::flashExcept('_token');

            // Notify the user of their mistake.
            Session::push('messages', 'Please double-check the spelling of your definition.');

            // Return to form
            return back();
        }

        // Pull relations.
        $relations = (array) Arr::pull($data, 'relations');

        // Save titles.
        // TODO.
        Session::push('messages', 'TODO: save definitions');
        return back();

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
                    if (strlen($lang->parentCode) >= 3
                        && !in_array($lang->parentCode, $relations['language'])
                        && $lang->parent)
                    {
                        $relations['language'][] = $lang->parent->code;

                        // Notify the user of the change
                        Session::push('messages',
                            '<em>'. $lang->parent->name .'</em> is the parent language for <em>'.
                            $lang->name .'</em>, and was added to the list of languages the word <em>'.
                            $data['title'] .'</em> exists in.');
                    }
                }
            }
        }

        // Set rating.
        // TODO.

        // Update definition details.
        $def->fill($data);
        $def->save();

        // Update translations and other relations.
        $def->updateRelations($relations);

        // Redirect user to their requested next step.
        switch ($return)
        {
            case 'index':
                $return = $def->uri;
                break;

            case 'edit':
                $return = route('definition.edit', ['id' => $def->uniqueId]);
                break;

            case 'word':
            case 'phrase':
                $return = route('definition.create.'. $return, ['lang' => $def->mainLanguage->code]);
                break;

            default:
                $return = route('home');
        }

        Session::push('messages', 'The details for <em>'. $def->title .'</em> were successfully saved, thanks :)');
        return redirect($return);
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
