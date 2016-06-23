<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers;

use Auth;
use Request;
use Session;
use App\Models\Tag;
use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Translation;
use App\Models\Definitions\Word;
use Illuminate\Support\Collection;
use App\Models\DefinitionTitle as Title;
use App\Factories\TransliterationFactory as Transliterator;

class DefinitionController extends Controller
{
    /**
     * @var string
     */
    protected $name = 'definition';

    /**
     *
     */
    protected $defaultQueryLimit = 50;

    /**
     *
     */
    protected $supportedOrderColumns = [
        'id' => 'ID',
        'createdAt' => 'Created date',
    ];

    /**
     *
     */
    protected $defaultOrderColumn = 'id';

    /**
     *
     */
    protected $defaultOrderDirection = 'desc';

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
            abort(404);
        }

        // Check user input.
        $str = trim(preg_replace('/[\s]+/', '_', strip_tags($raw)));
        if (strlen($str) < 2) {
            abort(404);
        } elseif ($str != $raw) {
            return redirect($lang->code .'/'. $str);
        }

        // Find definitions matching the query
        $str = str_replace('__', '?', $str);
        $str = str_replace('_', ' ', $str);
        $definitions = $lang->definitions()
                        ->with('languages', 'translations', 'titles', 'tags')
                        ->whereIn('type', [Definition::TYPE_WORD, Definition::TYPE_EXPRESSION])
                        ->whereHas('titles', function($query) use($str) {
                            $query->where('title', $str);
                        })->get();

        if (!count($definitions))
        {
            // If no definitions were found, check alternate titles...
            $alts = Definition::search($str, ['offset' => 0, 'limit' => 1]);
            if (count($alts)) {
                return redirect($alts[0]->uri);
            }

            abort(404);
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
     * @todo Restrict access based on roles
	 *
     * @param string $type
     * @param string $langCode
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
            abort(404);
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

    public function createExpression($langCode) {
        return $this->createType(Definition::TYPE_EXPRESSION, $langCode);
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

        // Check definition titles.
        if (!$titles = $this->getTitles(Request::input('titleStr'))) {
            return back();
        }

        // Check translations.
        if (!$translations = $this->getTranslations(Request::input('translations'))) {
            return back();
        }

        // Check languagees
        if (!$languageData = $this->getLanguages(Request::input('languages'), $titles[0]->title)) {
            return back();
        }

        $languages = $languageData[0];
        $mainLanguage = $languageData[1];

        // Attach language alphabet to titles.
        // TODO: Check request for ID of default alphabet to use.
        if ($mainLanguage && $mainLanguage->alphabets->count())
        {
            $defaultAlphabet = $mainLanguage->alphabets->first();

            foreach ($titles as $title)
            {
                if (!$title->alphabetId) {
                    $title->alphabetId = $defaultAlphabet->id;
                }
            }
        }

        // Definition main language.
        $definition->mainLanguageCode = $mainLanguage->code;

        // Definition sub-type.
        $definition->subType = Request::input('subType');

        // Definition rating
        $definition->rating = Auth::check() ?
            Definition::RATING_AUTHENTICATED :
            Definition::RATING_DEFAULT;

        // Create definition.
        if (!$definition->save()) {
            Session::push('messages', 'Could not save definition.');
            return back();
        }

        // Save relations.
        $definition->titles()->saveMany($titles);
        $definition->translations()->saveMany($translations);
        $definition->languages()->sync($languages);

        Session::push('messages', 'The details for <em>'. $definition->titles[0]->title .
            '</em> were successfully saved, thanks :)');

        $rdir = Request::input('return') == 'continue' ?
            route('admin.definition.edit', $definition->uniqueId) :
            $definition->uri;

        return redirect($rdir);
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
            abort(404);
        }

        // Related definition array for selectize plugin.
        $relatedDefinitions = $definition->relatedDefinitionList->map(function($item) {
            return [
                'uniqueId' => $item->uniqueId,
                'mainTitle' => $item->mainTitle,
            ];
        });

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

        return view('admin.definition.edit', [
            'model' => $definition,
            'relatedDefinitionOptions' => $relatedDefinitions,
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
            abort(404);
        }

        // Check definition titles.
        if (!$titles = $this->getTitles(Request::input('titleStr'))) {
            return back();
        }

        // Check translations.
        if (!$translations = $this->getTranslations(Request::input('translations'))) {
            return back();
        }

        // Check languages
        if (!$languageData = $this->getLanguages(Request::input('languages'), $titles[0]->title)) {
            return back();
        }

        $languages = $languageData[0];
        $mainLanguage = $languageData[1];

        // Definition main language.
        $definition->mainLanguageCode = $mainLanguage->code;

        // Definition type
        $definition->type = Request::input('type');

        // Definition sub-type.
        $definition->subType = Request::input('subType');

        // Related definitions.
        $related = $this->getRelatedDefinitions(Request::input('relatedDefinitions'));
        $oldRelatedIDs = $definition->relatedDefinitions;
        $newRelatedIDs = $definition->relatedDefinitions = $related->map(function($item, $key) {
            return $item->id;
        });

        // Update related definition records.
        // TODO: this can be done in a cleaner way...
        $addRelated = collect($newRelatedIDs)->diff($oldRelatedIDs);
        $removeRelated = collect($oldRelatedIDs)->diff($newRelatedIDs);
        if (count($addRelated))
        {
            foreach ($addRelated as $id)
            {
                if ($def = Definition::find($id))
                {
                    if (!$def->relatedDefinitions || !in_array($definition->id, $def->relatedDefinitions))
                    {
                        $defRelated = (array) $def->relatedDefinitions;
                        $defRelated[] = $definition->id;
                        $def->relatedDefinitions = $defRelated;
                        $def->save();
                    }
                }
            }
        }
        if (count($removeRelated))
        {
            foreach ($removeRelated as $id)
            {
                if ($def = Definition::find($id))
                {
                    if ($def->relatedDefinitions && in_array($definition->id, $def->relatedDefinitions))
                    {
                        $defRelated = (array) $def->relatedDefinitions;
                        $key = array_search($definition->id, $defRelated);
                        array_splice($defRelated, $key, 1);
                        $def->relatedDefinitions = $defRelated;
                        $def->save();
                    }
                }
            }
        }

        // Update definition.
        if (!$definition->save()) {
            abort(500);
        }

        // Update titles.
        $definition->titles()->delete();
        $definition->titles()->saveMany($titles);

        // Update tags.
        $tags = $this->getTags(Request::input('tags'));
        $definition->tags()->sync($tags);

        // Update translations.
        $definition->translations()->delete();
        $definition->translations()->saveMany($translations);

        // Update languages.
        $definition->languages()->sync($languages);

        // TODO: handle AJAX requests.
        Session::push('messages', 'The details for <em>'. $definition->titles[0]->title .
            '</em> were successfully saved, thanks :)');

        // Return URI
        switch (Request::input('return'))
        {
            case 'admin':
                $return = route('admin.definition.index');
                break;

            case 'edit':
                $return = $definition->editUri;
                break;

            case 'continue':
                $return = route('definition.edit', ['id' => $definition->uniqueId]);
                break;

            case 'finish':
            case 'summary':
            default:
                $return = $definition->uri;
        }

        return redirect($return);
	}

    /**
     * Removes the specified resource from storage.
     *
     * @param  int $id
     * @throws \Exception
     * @return Response
     */
	public function destroy($id)
	{
        // Retrieve the definition model.
        if (!$def = Definition::find($id)) {
            throw new \Exception(trans('errors.resource_not_found'), 404);
        }

        // Retrieve main language
        $lang = $def->mainLanguage;

        // Delete record
        Session::push('messages', '<em>'. $def->titles[0]->title .'</em> has been succesfully deleted.');
        $def->delete();

        // Return URI
        switch (Request::input('return'))
        {
            case 'admin':
                $return = route('admin.definition.index');
                break;

            case 'home':
                $return = route('home');

            case 'language':
            default:
                $return = $lang->uri;
                break;
        }

        return redirect($return);
	}

    /**
     * Retrieves definition titles from request.
     *
     * @param string $titleStr
     * @param string $script
     * @return array|false
     */
    protected function getTitles($titleStr = null, $script = 'Latn')
    {
        $titles = [];

        // Retrieve titles from title string.
        $titleStr = trim($titleStr);
        if (strlen($titleStr))
        {
            foreach (@explode(',', $titleStr) as $title)
            {
                $title = trim($title);

                if (strlen($title)) {
                    $titles[] = new Title([
                        'title' => $title,
                        'transliteration' => Transliterator::make($script)->transliterate($title)
                    ]);
                }
            }
        }

        if (!count($titles))
        {
            // Flash input data to session.
            Request::flashExcept('_token');

            // Notify the user of their mistake.
            Session::push('messages', 'Please double-check the spelling of your definition.');

            return false;
        }

        return $titles;
    }

    /**
     * Retrieves tags from request.
     *
     * @param string $tagStr
     * @return array
     */
    protected function getTags($tagStr)
    {
        $tags = [];

        // Retrieve tag objects.
        $tagStr = trim($tagStr);
        if (strlen($tagStr))
        {
            foreach (@explode(',', $tagStr) as $tagId)
            {
                $tagId = trim($tagId);

                if (strlen($tagId))
                {
                    // Find tag by ID.
                    if ($tag = Tag::find($tagId)) {
                        $tags[] = $tag->id;
                    }

                    // Find tag by title.
                    elseif ($tag = Tag::where('title', '=', $tagId)->first()) {
                        $tags[] = $tag->id;
                    }

                    // New tag.
                    else {
                        $tag = Tag::create(['title' => $tagId]);
                        $tags[] = $tag->id;
                    }
                }
            }
        }

        return $tags;
    }

    /**
     * Retrieves language data from request.
     *
     * @param array|string $raw
     * @param string $title
     */
    protected function getLanguages($raw, $title)
    {
        // Make sure we have an array of language codes.
        $raw = is_array($raw) ? $raw : @explode(',', $raw);

        $languages = [];
        $mainLanguage = null;

        foreach ($raw as $code)
        {
            if ($lang = Language::findByCode($code))
            {
                $languages[] = $lang->id;

                if (!$mainLanguage) {
                    $mainLanguage = $lang;
                }

                // Check if the language has a parent, and whether that parent is already
                // in the list.
                if (strlen($lang->parentCode) >= 3 && !in_array($lang->parentCode, $languages) && $lang->parent)
                {
                    $languages[] = $lang->parent->id;

                    // Notify the user of the change
                    Session::push('messages',
                        '<em>'. $lang->parent->name .'</em> is the parent language for <em>'.
                        $lang->name .'</em>, and was added to the list of languages the expression <em>'.
                        $title .'</em> exists in.');
                }
            }
        }

        // Make sure we have a valid language.
        if (!count($languages))
        {
            // Flash input data to session.
            Request::flashExcept('_token');

            // Notify the user of their mistake.
            Session::push('messages', 'Make sure to select a valid language, or enter a 3-letter language code.');

            return false;
        }

        return [$languages, $mainLanguage];
    }

    /**
     * Retrieves translation data from the request.
     *
     * @param array $raw
     * @return array|false
     */
    protected function getTranslations($raw = null)
    {
        // Retrieve translations
        $translations = [];
        if (is_array($raw))
        {
            foreach ($raw as $langCode => $data)
            {
                // Performance check.
                if (!is_array($data) || empty($data)) {
                    continue;
                }

                // Validate language code.
                if (!$langCode = Language::sanitizeCode($langCode)) {
                    continue;
                }

                // Make sure we have a practical translation.
                if (!array_key_exists('practical', $data) || empty($data['practical'])) {
                    continue;
                }

                $data['language'] = $langCode;
                $translations[$langCode] = new Translation($data);
            }
        }

        if (!count($translations))
        {
            // Flash input data to session.
            Request::flashExcept('_token');

            // Notify the user of their mistake.
            Session::push('messages', 'Please double-check your translation.');

            return false;
        }

        return $translations;
    }

    /**
     * Retrieves related definitions from request.
     *
     * @param array $raw
     * @return Collection
     */
    public function getRelatedDefinitions($raw = '')
    {
        // Performance check.
        $raw = trim($raw);
        if (!strlen($raw)) {
            return new Collection;
        }

        // Un-obfuscate IDs.
        $ids = [];
        foreach (@explode(',', $raw) as $id)
        {
            if ($id = Definition::decodeId($id)) {
                $ids[] = $id;
            }
        }

        // Performance check.
        if (!count($ids)) {
            return new Collection;
        }

        return Definition::whereIn('id', $ids)->get();
    }
}
