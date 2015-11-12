<?php
/**
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

use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
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
            return $this->error(404, 'Language not found.');
        }


        return $this->error(501);
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
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
    {
        // TODO ...

        return $this->error(501);
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
