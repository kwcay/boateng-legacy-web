<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved.
 *
 * @version 0.1
 * @brief   Handles definition-related API requests.
 */
namespace App\Http\Controllers\API\v01;

use Auth;
use Lang;
use Request;
use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Translation;
use App\Models\Definitions\Word;
use App\Models\Definitions\Expression;
// use Frnkly\ControllerTraits\Embedable;
use App\Http\Controllers\Controller;

class DefinitionController extends Controller
{
    public function __construct()
    {
        // Enable the auth middleware.
        // $this->middleware('auth', ['except' => ['show', 'search', 'exists']]);
    }

    /**
     * Returns a definition resource.
     *
     * @param string $id    Unique ID of definition.
     * @return object
     */
    public function show($id)
    {
        // Performance check.
        if (! $id = Definition::decodeId($id)) {
            return response('Invalid Definition ID.', 400);
        }

        // Retrieve definition object
        if (! $definition = Definition::embed(Request::get('embed'))->find($id)) {
            return response('Definition Not Found.', 404);
        }

        $definition->applyEmbedableAttributes(Request::get('embed'));

        return $definition;
    }

    /**
     * Finds definitions matching a title (exact match).
     *
     * @param string $definitionType
     * @param string $title
     * @return Response
     */
    public function findByTitle($definitionType, $title)
    {
        // Performance check
        $title = trim(preg_replace('/[\s+]/', ' ', strip_tags($title)));
        if (strlen($title) < 2) {
            return response('Query Too Short.', 400);
        }

        // TODO: add definition type to where clause.
        // ...

        // List of relations and attributes to append to results.
        $embed = $this->getEmbedArray(
            Request::get('embed'),
            Definition::$appendable
        );

        // Lookup definitions with a specific title
        $definitions = Definition::with($embed['relations'])->where('title', '=', $title)->get();

        // Append extra attributes.
        if (count($embed['attributes']) && count($definitions)) {
            foreach ($definitions as $definition) {
                foreach ($embed['attributes'] as $accessor) {
                    $definition->setAttribute($accessor, $definition->$accessor);
                }
            }
        }

        return $definitions ?: response('Definition Not Found.', 404);
    }

    /**
     * Returns the definition of the day.
     *
     * @param string $definitionType
     * @param string $title
     * @return Response
     */
    public function getDaily($type)
    {
        // Performance check.
        $type = Definition::isValidType($type);
        if (is_null($type)) {
            abort(400);
        }

        // List of relations and attributes to append to results.
        $embed = $this->getEmbedArray(
            Request::get('embed'),
            Definition::$appendable
        );

        switch ($type) {
            case Definition::TYPE_WORD:
                $daily = Word::daily(Request::get('lang'));
                break;

            case Definition::TYPE_EXPRESSION:
                $daily = Expression::daily(Request::get('lang'));
                break;

            default:
                $daily = Definition::random(Request::get('lang'), $embed['relations']);
        }

        // Append extra attributes.
        if (count($embed['attributes'])) {
            foreach ($embed['attributes'] as $accessor) {
                $daily->setAttribute($accessor, $daily->$accessor);
            }
        }

        return $daily;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        // Instantiate by definition type.
        switch (Request::input('type')) {
            case Definition::TYPE_WORD:
                $definition = new Word;
                break;

            default:
                return response('Invalid definition type.', 400);
        }

        $definition->state = Definition::STATE_VISIBLE;

        // Retrieve data for new definition.
        $data = Request::only(['title', 'alt_titles', 'sub_type']);

        // Create the record in the database.
        return $this->save($definition, $data);
    }

    /**
     * Shortcut to save a definition model.
     *
     * @param \App\Models\Definition $definition
     * @param array $data
     * @return Response
     */
    public function save($definition, array $data = [])
    {
        // Validate incoming data.
        $validation = Definition::validate($data);
        if ($validation->fails()) {
            // Return first message as error hint.
            return response($validation->messages()->first(), 400);
        }

        // Add definition to database.
        $definition->fill($data);
        if (! $definition->save()) {
            return response('Could Not Save Definition.', 500);
        }

        // Add language relations.
        $languageCodes = Request::input('languages');
        if (is_array($languageCodes)) {
            $languageIDs = [];

            foreach ($languageCodes as $langCode) {
                if ($lang = Language::findByCode($langCode)) {
                    $languageIDs[] = $lang->id;
                }
            }

            $definition->languages()->sync($languageIDs);
        }

        // Add translation relations.
        $rawTranslations = Request::input('translations');
        if (is_array($rawTranslations)) {
            $translations = [];

            foreach ($rawTranslations as $foreign => $data) {
                $data['language'] = $foreign;
                $translations[] = new Translation($data);
            }

            $definition->translations()->saveMany($translations);
        }

        return $definition;
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
        // TODO ...

        return response('Not Implemented.', 501);
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
        // TODO ...

        return response('Not Implemented.', 501);
    }
}
