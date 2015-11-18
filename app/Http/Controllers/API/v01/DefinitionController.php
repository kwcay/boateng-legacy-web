<?php
/**
 * API v0.1
 */
namespace App\Http\Controllers\API\v01;

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
use App\Models\Translation;
use App\Models\Definitions\Word;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;


class DefinitionController extends Controller
{
    public function __construct()
    {
        // Enable the auth middleware.
		// $this->middleware('auth', ['except' => ['show', 'search', 'exists']]);
    }

    /**
     *
     *
     * TODO: restrict this method.
     */
    public function index()
    {
        return Request::has('dev') ? Definition::all() : response('Bad Request.', 400);
    }

    /**
     * Returns a definition resource.
     *
     * @param string $id    Unique ID of definition.
     * @return object
     */
    public function show($id)
    {
        // Retrieve definition object
        if ($definition = Definition::find($id)) {
            return $definition;
        }

        return response('Definition Nod Found.', 404);
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
     * @param string $definitionType
     * @param string $title
     * @return Response
     */
    public function exists($definitionType, $title)
    {
        // Performance check
        $title = trim(preg_replace('/[\s+]/', ' ', strip_tags($title)));
        if (strlen($title) < 2) {
            return response('Query Too Short.', 400);
        }

        // TODO: add definition type to where clause.
        // ...

        // Find a specific definition.
        $definition = Definition::where('title', '=', $title)->first();

        return $definition ?: response('Definition Not Found.', 404);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
    {
        // Instantiate by definition type.
        switch (Request::input('type'))
        {
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
        if ($validation->fails())
        {
            // Return first message as error hint.
            return response($validation->messages()->first(), 400);
        }

        // Add definition to database.
        $definition->fill($data);
        if (!$definition->save()) {
            return response('Could Not Save Definition.', 500);
        }

        // Add language relations.
        $languageCodes = Request::input('languages');
        if (is_array($languageCodes))
        {
            $languageIDs = [];

            foreach ($languageCodes as $langCode)
            {
                if ($lang = Language::findByCode($langCode)) {
                    $languageIDs[] = $lang->id;
                }
            }

            $definition->languages()->sync($languageIDs);
        }

        // Add translation relations.
        $rawTranslations = Request::input('translations');
        if (is_array($rawTranslations))
        {
            $translations = [];

            foreach ($rawTranslations as $foreign => $data)
            {
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

        return $this->error(501);
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

        return $this->error(501);
	}
}
