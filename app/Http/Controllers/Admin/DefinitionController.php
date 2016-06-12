<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Lang;
use Request;
use Session;
use Redirect;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use App\Http\Requests;
use App\Models\Tag;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Translation;
use App\Models\Definitions\Word;
use App\Models\DefinitionTitle as Title;
use App\Http\Controllers\Admin\BaseController as Controller;

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
        if (!$titles = $this->getTitles(Request::input('titleStr'), Request::input('titles'))) {
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
        if ($mainLanguage && $mainLanguage->alphabets)
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
        $definition->rating = Definition::RATING_DEFAULT;

        // TODO: update rating based on related data...

        // Create definition.
        if (!$definition->save()) {
            abort(500, 'Could not save definition.');
        }

        // Save relations.
        $definition->titles()->saveMany($titles);
        $definition->translations()->saveMany($translations);
        $definition->languages()->sync($languages);

        // Send response.
        if (Request::ajax())
        {
            // TODO
            return response($definition, 200);
        }

        else
        {
            Session::push('messages', 'The details for <em>'. $definition->titles[0]->title .
                '</em> were successfully saved, thanks :)');

            $rdir = Request::input('next') == 'continue' ?
                route('definition.edit', ['id' => $definition->uniqueId]) :
                $definition->uri;

            return redirect($rdir);
        }
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
        if (!$titles = $this->getTitles(Request::input('titleStr'), Request::input('titles'))) {
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

        // Definition rating
        $definition->rating = Definition::RATING_DEFAULT;

        // TODO: update rating based on related data...
        // ...

        // Update definition.
        if (!$definition->save()) {
            abort(500, 'Could not save definition.');
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
     * Shortcut to create a new definition or save an existing one.
     *
     * @param object $definition    Definition object.
     * @param array $data           Definition details to update.
     * @param string $return        Relative URI to redirect to.
     * @return mixed
     */
    private function save(Definition $definition, array $data, $return)
    {
        // TODO: move each step to its own method, and let the "store" and "update" methods
        // decide how to save the definition and its relations.

        // Pull relations.
        $relations = (array) Arr::pull($data, 'relations');

        // Main language
        $mainLanguage = null;

        // Update definition details.
        // $definition->fill($data);
        // $definition->save();

        // Save titles.
        // TODO.
        Session::push('messages', 'TODO: save definitions');
        return back();

        // Set rating.
        // TODO.

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
            case 'expression':
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
     * @param array $titleArray
     * @return array|false
     */
    protected function getTitles($titleStr = null, $titleArray = null)
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
                        'title' => $title
                    ]);
                }
            }
        }

        // Retrieve other, individually specified titles.
        // ...

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

        $languages = $languageObjects = [];
        $mainLanguage = null;

        foreach ($raw as $code)
        {
            if ($lang = Language::findByCode($code))
            {
                $languages[] = $lang->id;
                $languageObjects[] = $lang;

                if (!$mainLanguage) {
                    $mainLanguage = $lang;
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

        // Check if the languages have a parents, and whether those parents are already
        // in the list.
        foreach ($languageObjects as $lang)
        {
            if (strlen($lang->parentCode) >= 3 && $lang->parent && !in_array($lang->parent->id, $languages))
            {
                $languages[] = $lang->parent->id;

                // Notify the user of the change
                Session::push('messages',
                    '<em>'. $lang->parent->name .'</em> is the parent language for <em>'.
                    $lang->name .'</em>, and was added to the list of languages the expression <em>'.
                    $title .'</em> exists in.');
            }
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
