<?php namespace App\Http\Controllers;

use URL;
use Input;
use Session;
use Redirect;
use Validator;
use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Http\Controllers\Controller;
use App\Traits\ExportableResourceTrait;
use App\Traits\ImportableResourceTrait;
use Illuminate\Http\Request;

class DefinitionController extends Controller {

    use ExportableResourceTrait, ImportableResourceTrait;

	/**
	 * Disable index view.
	 *
	 * @return Response
	 */
	public function index() {
        return Redirect::to('');
	}

	/**
	 * Displays the form to add a new definition.
	 *
	 * @return Response
	 */
	public function create(Definition $def)
	{
        $lso    = [];

        // Set some defaults
        if ($word = Input::get('word', Input::old('word'))) {
            $def->setWord($word);
        }
        if ($alt = Input::old('alt')) {
            $def->setAltWord($alt);
        }
        if ($type = Input::old('type')) {
            $def->setParam('type', $type);
        }
        if ($lang = Input::get('lang', Input::old('lang'))) {
            $def->language  = preg_replace('/[^a-z, -]/', '', $lang);

            if ($lang = Language::findByCode($def->language)) {
                $lso[] = [
                    'code' => $lang->code,
                    'name' =>$lang->getName()
                ];
            }
        }
        if ($translation = Input::old('translation')) {
            foreach ($translation as $lang => $trans) {
                $def->setTranslation($lang, $trans);
            }
        }
        if ($meaning = Input::old('meaning')) {
            foreach ($meaning as $lang => $mean) {
                $def->setMeaning($lang, $mean);
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
        return $this->save(new Definition, Input::all(), route('definition.create', [], false));
	}

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
            return $def ? Redirect::to($def->getWordUri(false)) : abort(404);
        }

        // Retrieve language object
        if (!$lang = Language::findByCode($code)) {
            abort(404, 'Can\'t find that language :(');
        }

        // Check user input.
        $word   = trim(preg_replace('/[\s]+/', '_', strip_tags($raw)));
        if (strlen($word) < 2) {
            abort(404, 'Can\'t find that word :(');
        } elseif ($word != $raw) {
            return Redirect::to($lang->code .'/'. $word);
        }

        // Find words matching the query
        $word   = str_replace('_', ' ', $word);
        $wWord  = '(word = :a OR word LIKE :b or word LIKE :c or word LIKE :d)';
        $wLang  = '(language = :w OR language LIKE :x or language LIKE :y or language LIKE :z)';
        $words  = Definition::whereRaw($wWord .' AND '. $wLang, array(
            ':a' => $word,
            ':b' => $word .',%',
            ':c' => '%,'. $word .',%',
            ':d' => '%,'. $word,
            ':w' => $lang->code,
            ':x' => $lang->code .',%',
            ':y' => '%,'. $lang->code .',%',
            ':z' => '%,'. $lang->code
        ))->get();

        if (!count($words)) {
            abort(404, 'Can\'t find that word :(');
        }

        return view('pages.word', array(
            'lang'  => $lang,
            'query' => $word,
            'words' => $words
        ));
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
            abort(404, 'Definition not found :(');
        }

        // Create language options for selectize plugin.
        $lso = [];
        $langs  = Language::whereIn('code', explode(',', $def->language))->get();
        foreach ($langs as $lang) {
            $lso[] = [
                'code' => $lang->code,
                'name' => $lang->getName()
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
            throw new \Exception('Can\'t find that definition :(', 404);
        }

        return $this->save($def, Input::all(), $def->getEditUri(false));
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
            throw new \Exception('Can\'t find that definition :(', 404);
        }

        // Delete record
        Session::push('messages', '<em>'. $def->getWord() .'</em> has been succesfully deleted.');
        $def->delete();

        return Redirect::to('');
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
            Input::flashExcept('_token');

            // Return to form
            return Redirect::to($return)->withErrors($test);
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
                        $lang->getName() .'</em>, and was added to the list of languages the word <em>'.
                        $data['word'] .'</em> exists in.');
                }
            }
        }

        // Main details
        $def->setWord($data['word']);
        $def->setAltWord($data['alt']);
        $def->language  = implode(',', $codes);
        $def->setParam('type', $data['type']);
        $def->state     = 1;

        // Translations
        foreach ($data['translation'] as $lang => $translation) {
            $def->setTranslation($lang, trim($translation));
        }

        // Meanings
        foreach ($data['meaning'] as $lang => $meaning) {
            $def->setMeaning($lang, trim($meaning));
        }

        $def->save();
        $rdir = $data['more'] ?
            route('definition.create', ['lang' => $def->getMainLanguage(true)], false) : $def->getWordUri(false);

        Session::push('messages', 'The details for <em>'. $def->getWord() .'</em> were successfully saved, thanks :)');
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

        // Query the database
        $defs = Definition::where('data', 'LIKE', '%'. $query .'%')
            ->orWhere('translations', 'LIKE', '%'. $query .'%')
            ->orWhere('meanings', 'LIKE', '%'. $query .'%')->get();

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

    /**
     * @param $format
     * @return mixed
     */
    public static function export($format = 'yaml')
    {
        $data = Definition::all();
        $formatted = [];

        foreach ($data as $item) {
            $formatted[] = $item->toArray();
        }

        return static::exportToFormat($formatted, $format, false);
    }

}
